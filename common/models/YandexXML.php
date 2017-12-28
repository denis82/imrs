<?php

class YandexXML {
	
	const STATIC_LOG = true;
	const CACHE_HOURS = 12;
	
	public $log = array();
	
	protected $proxy_list = array();
	protected $proxy_using = false;
	protected $proxy = false;
	
	public $redis = false;
	private $binded_crc = array();

	public $lastXML = '';

	public function __construct(){
		$this->ch = curl_init();
	}
	
	public function setRedis(Redis &$rds){
		$this->redis = $rds;
	}
	
	public function addLog($string){
		$this->log[] = $string;
	}
	
	public function getLog($clear = false){
		$log = $this->log;
		if($clear) $this->log = array();
		return $log;
	}
	
	public function getLogClear(){
		return $this->getLog(true);
	}
	
	public static function create(){
		$instance = new self();
		return $instance;
	}
	
	public function addProxy(YandexProxyInterface $proxyObject){
		$this->proxy_list[] = $proxyObject;
		return $this;
	}
	
	public function switchProxy(){
		if(!$this->proxy_list || !count($this->proxy_list)) return $this;
		$key = array_rand($this->proxy_list, 1);
		$this->proxy = $this->proxy_list[$key];
	}
	
	public function get($domain, $keyword, $lr = 0){
		
		$lr = (int)$lr;
		
		$crc = self::getCRC($domain, $keyword, $lr);
		
		#<redis>
		if($this->redis){
			$position = $this->redis->get($crc);
			if($position !== false && $position !== null){
				return $position;
			}
		}
		#</redis>

		if($this->lastXML = $this->getXML($keyword, $lr)){
		
			$position = self::getPositionXML($this->lastXML, $domain, $this, $keyword, $lr);
			
			#<redis>
			if(is_int($position) && $this->redis)
				$this->redis->set($crc, $position, 3600 * self::CACHE_HOURS);
			#</redis>
			
			return $position;
			
		}
		
		return false;
		
	}
	
	public function getXML($keyword, $lr = 0, $max = 100, $page = 0){

		$this->lastXML = '';

		$lr = (int)$lr;
		$max = (int)$max;
		$max = $max? $max : 100;
		
		if(!$this->proxy) $this->switchProxy();
		
		if(!$this->proxy){
			$this->addLog('Haven\'t some proxy');
			return false;
		}
		
		$crc = crc32(sprintf('yandex-xml-content-%s-%d', urlencode($keyword), $lr));
		
		/*if($this->redis){
			$content = $this->redis->get($crc);
			
			if($content && strpos($content, '<yandexsearch') !== false){
				return $content;
			}
		}*/

		sleep(1);
		
		$url  = 'https://yandex.ru/search/xml?query='.urlencode($keyword);
		$url .= '&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D'.$max.'.docs-in-group%3D1';
		$url .= '&page='.$page;
		$url .= '&user='.$this->proxy->getUser();
		$url .= '&key='.$this->proxy->getKey();
		$url .= '&noreask=1';
		if($lr)
			$url .= '&lr='.$lr;

		$content = $this->getContentXML($url);
		
		if($this->redis && strpos($content, '<yandexsearch') !== false){
			$this->redis->set($crc, $content, 3600 * 6);
		}
		
		return $content;
	}
	
	private function getContentXML($url){
		curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1); 
		
		# LIMIT
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5); // Макс. кол. секунд для соединения
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 10); // Макс. количество секунд для выполнения cURL-функций.
		
		if(!preg_match('#^(127\.0\.0\.1|localhost)$#i', $this->proxy->getIp())){
			curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy->getAddress());
			curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $this->proxy->getAccess());
		}else{
			curl_setopt($this->ch, CURLOPT_PROXY, NULL);
			curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, NULL);
		}
		return curl_exec($this->ch);
	}
	
	public static function normalizeDomain($url){
		$url = preg_replace('/(http|https|ftp)\:\/\//', '', strtolower($url));
		$exploded = explode('/', $url);
		$url = array_shift($exploded);
		$url = str_replace('www.', '', $url);
		return $url;
	}
	
	public function getRelative($domain, $keyword, $lr = 213){
		if(!$this->proxy) $this->switchProxy();
		
		if(!$this->proxy){
			$this->addLog('Haven\'t some proxy');
			return false;
		}
		
		$domain = self::normalizeDomain($domain);
		
		$url   = 'https://yandex.ru/search/xml?user='.$this->proxy->getUser();
		$url  .= '&key='.$this->proxy->getKey();
		$url  .= '&query='.urlencode($keyword).'+site%3A'.urlencode($domain);
		$url  .= '&l10n=ru';
		$url  .= '&lr='.$lr;

		// Закомментировал сортировку и фильтр, они не нужны здесь
		// $url  .= '&sortby=rlv';
		// $url  .= '&filter=moderate';
		
		$url  .= '&groupby=attr%3D%22%22.mode%3Dflat.groups-on-page%3D1.docs-in-group%3D1';
		
		$xml = $this->getContentXML($url);
		
		if(preg_match('#<url>([^<]+)</url>#i', $xml, $match)){
			return $match[1];
		}
		
		return false;
		
	}

	public static function getPositionXML($xml, $domain, &$inst, $keyword, $lr){
		$position = 0;
		
		$domain = self::normalizeDomain($domain);
	
		if(preg_match_all('#<domain>([^<]+)<\/domain>#i', $xml, $matches)){
			
			if(isset($matches[1]) && count($matches[1])){
			
				$sites = $matches[1];
				
				foreach($sites as $__i => $__domain){
					$__pos = $__i + 1;
					$__domain = self::normalizeDomain($__domain);
					if($__domain == $domain){
						$position = $__pos;
						break;
					}else{
						#<redis>
						if($inst->redis){
							$crc = self::getCRC($__domain, $keyword, $lr);
							if(!isset($inst->binded_crc[$crc])){
								$inst->redis->set($crc, $__pos, 3600 * self::CACHE_HOURS);
								$inst->binded_crc[$crc] = true;
							}
						}
						#</redis>
					}
				}
				
				if(!$position) return 0;
				
			}else{
				if(self::STATIC_LOG) echo('Not found in site\'s stack' . "\r\n");
				return false;
			}
			
		}else{
			if(self::STATIC_LOG) echo('Regular Expression do not found' . "\r\n");
			
			if(self::STATIC_LOG) echo('Error logged in `'.$log_dir.'`' . "\r\n");
			
			//var_dump($inst->proxy);
			
			return false;
		}
		
		return $position;
	}
	
	public static function getCRC($domain, $keyword, $region){
		$region = (int) $region;
		$keyword = str_replace(' ', '-', $keyword);
		return crc32($domain .'_'. $keyword .'_'. $region);
	}
	
}