<?php

interface YandexProxyInterface {
	public function getAddress();
	public function getIP();
	public function getPort();
	public function getAccess();
	public function getLogin();
	public function getPassword();
	public function getUser();
	public function getKey();
}

class YandexProxy implements YandexProxyInterface {

	public $ip;
	public $port;
	public $login;
	public $password;
	public $user;
	public $key;
	
	public function __construct(){}
	
	public static function create($ADDRESS, $AUTH, $USER, $KEY){
		$inst = new self();
		
		$address_chunks	= explode(':',$ADDRESS);
		$auth_chunks	= explode(':',$AUTH);
		
		$inst->ip = $address_chunks[0];
		$inst->port = isset($address_chunks[1])? $address_chunks[1] : '';
		$inst->login = $auth_chunks[0];
		$inst->password = isset($auth_chunks[1])? $auth_chunks[1] : '';
		$inst->user = $USER;
		$inst->key = $KEY;
		
		return $inst;
	}
	
	public function getAddress(){ return $this->ip . ':' . $this->port; }
	public function getAccess(){ return $this->login . ':' . $this->password; }
	public function getIP(){ return $this->ip; }
	public function getPort(){ return $this->port; }
	public function getLogin(){ return $this->login; }
	public function getPassword(){ return $this->password; }
	public function getUser(){ return $this->user; }
	public function getKey(){ return $this->key; }
	
}

class YandexXML {
	
	const STATIC_LOG = true;
	const CACHE_HOURS = 12;
	
	public $log = array();
	
	protected $proxy_list = array();
	protected $proxy_using = false;
	protected $proxy = false;
	
	public $redis = false;
	private $binded_crc = array();
	
	public $errors = array();
	
	public $error_codes = array(
		1 		=> 'Синтаксическая ошибка', 
		2 		=> 'Задан пустой поисковый запрос', 
		15 		=> 'Искомая комбинация слов нигде не встречается', 
		18 		=> 'Некорректные параметры запроса', 
		19 		=> 'Несовместимые параметры', 
		20 		=> 'Причина ошибки не может быть установлена', 
		31 		=> 'Пользователь не зарегистрирован на сервисе', 
		32		=> 'Превышено ограничение на количество допустимых суточных запросов', 
		33		=> 'IP-адрес не совпадает с заданным при регистрации', 
		34		=> 'Пользователь не зарегистрирован в Яндекс.Паспорте', 
		37		=> 'Ошибка в параметрах запроса', 
		42		=> 'Ключ содержит ошибку', 
		43		=> 'Версия ключа содержит ошибку', 
		44		=> 'Адрес более не поддерживается', 
		48		=> 'Тип поиска, указанный при регистрации, не совпадает с типом поиска, используемым для запроса данных', 
		100 	=> 'Запрос отправлен роботом.'
	);
	
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
	
	public static function normalizeKeyword($keyword){
		return mb_strtolower(preg_replace('/\s{2, }/', ' ', $keyword), 'UTF-8');
	}
	
	public function getInfo($domain, $keyword, $lr = false){
	
		if($xml = $this->getXML($keyword, $lr)){
			
			if(preg_match('/<error code="([0-9])+">/i', $xml, $matchError)){
				$this->errors[] = (int)$matchError[1];
				return false;
			}
			
			$domain = self::normalizeDomain($domain);
			//echo $xml;
			
			$stats = '';
			
			$chunks = explode('</group>', $xml);
			
			if($chunks > 1){
			
				$first = explode('<group>', $chunks[0]);
				$stats = $first[0];
				if(isset($first[1])) $chunks[0] = $first[1];
				else echo $chunks[0];
				
				$position = 0;
				
				foreach($chunks as $chunk){
				
					$chunk = preg_replace('/<\/?hlword>/i', '', $chunk);
					
					$position++;
					
					$info = array();
					
					if(!preg_match('/<domain>([^<]+)<\/domain>/i', $chunk, $domainMatch)) 
						continue;
					
					$currentDomain = $domainMatch[1];
					
					$info['domain'] = $currentDomain;
					$info['position'] = $position;
					
					$currentDomain = self::normalizeDomain($currentDomain);
					
					if($currentDomain != $domain) continue;
					
					if(preg_match('/<url>([^<]+)<\/url>/i', $chunk, $urlMatch))
						$info['url'] = $urlMatch[1];
					else
						$info['url'] = sprintf('http://%s/', $info['domain']);
						
					
					if(preg_match('/<title>([^<]+)<\/title>/i', $chunk, $titleMatch))
						$info['title'] = $titleMatch[1];
					else 
						$info['title'] = '';
					
					if(preg_match('/<passage>([^<]+)<\/passage>/i', $chunk, $passageMatch))
						$info['passage'] = $passageMatch[1];
					else 
						$info['passage'] = '';
					
					if(preg_match('/<modtime>([^<]+)<\/modtime>/i', $chunk, $modtimeMatch)){
						$info['modtime'] = $modtimeMatch[1];
						$info['time'] = (int)DateTime::createFromFormat('Ymd\THis', $info['modtime'])->getTimestamp();
					}else{
						$info['modtime'] = '';
						$info['time'] = 0;
					}
					
					//date_parse_from_format('Ymd\T')
					
					//var_dump($info);
					
					return $info;
				}
				
			}
			
		}
		
		return false;
	}
	
	/**
	 * Return position keyword of domain or false
	 * @param string $domain Name of domain
	 * @param string $keyword Search keyword
	 * @param int|boolean $lr
	 * @return int|boolean
	 */
	public function get($domain, $keyword, $lr = false){
		
		$crc = self::getCRC($domain, $keyword, $lr);
		
		#<redis>
		if($this->redis){
			$position = $this->redis->get($crc);
			if($position !== false && $position !== null){
				return $position;
			}
		}
		#</redis>
		
		if($xml = $this->getXML($keyword, $lr)){
		
			$position = self::getPositionXML($xml, $domain, $this, $keyword, $lr);
			
			#<redis>
			if(is_int($position) && $this->redis)
				$this->redis->set($crc, $position, 3600 * self::CACHE_HOURS);
			#</redis>
			
			return $position;
			
		}
		
		return false;
		
	}
	
	public function getXML($keyword, $lr = false){
	
		if(!$this->proxy) $this->switchProxy();
		
		if(!$this->proxy){
			$this->addLog('Haven\'t some proxy');
			return false;
		}
		
		sleep(1);
		
		$url  = 'https://xmlsearch.yandex.ru/xmlsearch?query='.urlencode($keyword);
		$url .= '&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D100.docs-in-group%3D1';
		$url .= '&user='.$this->proxy->getUser();
		$url .= '&key='.$this->proxy->getKey();
		if($lr)
			$url .= '&lr='.$lr;
	
		curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1); 
		
		# LIMIT ----!!!!
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5); // Макс. кол. секунд для соединения
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 10); // Макс. количество секунд для выполнения cURL-функций.
		
		//curl_setopt($this->ch, CURLOPT_COOKIEJAR,'/var/www/cookie.txt');
		//curl_setopt($this->ch, CURLOPT_COOKIEFILE,'/var/www/cookie.txt');
		
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
				
				//if(!$position && self::STATIC_LOG) echo('Position of `'.$domain.'` not found in stack size of `'.count($sites).'`' . "\r\n");
				
			}else{
				if(self::STATIC_LOG) echo('Not found in site\'s stack' . "\r\n");
				return false;
			}
			
		}else{
			if(self::STATIC_LOG) echo('Regular Expression do not found' . "\r\n");
			
			$log_dir = '/var/www/xmlerror/xml_yandex_'.date('Y_m_d').'_'.time().'.xml';
			
			file_put_contents($log_dir, $xml);
			
			if(self::STATIC_LOG) echo('Error logged in `'.$log_dir.'`' . "\r\n");
			
			var_dump($inst->proxy);
			
			//exit();
			
			//if(preg_match('/\<error\scode=\"([0-9]+)\">')
			
			
			return false;
		}
		
		//echo $position . "\r\n";
		
		return $position;
	}
	
	public static function getCRC($domain, $keyword, $region){
		$region = (int) $region;
		$keyword = str_replace(' ', '-', $keyword);
		return crc32($domain .'_'. $keyword .'_'. $region);
	}
	
}