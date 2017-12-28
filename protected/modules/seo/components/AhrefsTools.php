<?php


define('AHREFS_COMPONENT_DIR', __DIR__.'/ahrefs');
define('AHREFS_COOKIE_FILE', AHREFS_COMPONENT_DIR.'/cookie.txt');

class AhrefsTools {
	
	private static $instance = null;
	private static $curl = false;
	
	private $email = 'ivan@seo-experts.com';
	private $password = '440524q';
	private $cookies = '';
	
	private $status = false;
	
	private static function initCurl(){
		if(!self::$curl) self::$curl = curl_init();
		curl_setopt(self::$curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt(self::$curl, CURLOPT_COOKIEJAR, AHREFS_COOKIE_FILE);
		curl_setopt(self::$curl, CURLOPT_COOKIEFILE, AHREFS_COOKIE_FILE);
		curl_setopt(self::$curl, CURLOPT_HEADER, 1);
		curl_setopt(self::$curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36');
		curl_setopt(self::$curl, CURLOPT_REFERER, 'https://ru.ahrefs.com/');
		curl_setopt(self::$curl, CURLOPT_VERBOSE, true );
		curl_setopt(self::$curl, CURLOPT_POST, false);
	}
	
	private function __construct(){
		self::initCurl();
		
		curl_setopt(self::$curl, CURLOPT_URL, 'https://ru.ahrefs.com/users/login.php');
		$result = curl_exec(self::$curl);
		
		//file_put_contents(AHREFS_COMPONENT_DIR.'/__ahrefs_auth_check.txt', $result);
		
		if(curl_getinfo(self::$curl, CURLINFO_HTTP_CODE) === 200){
			curl_setopt(self::$curl, CURLOPT_URL, 'https://ru.ahrefs.com/users/login.php');
			curl_setopt(self::$curl, CURLOPT_POST, true);
			curl_setopt(self::$curl, CURLOPT_REFERER, 'https://ru.ahrefs.com/users/login.php');
			curl_setopt(self::$curl, CURLOPT_POSTFIELDS, array(
				'email' => $this->email, 
				'password' => $this->password, 
				'submit' => 'Войти', 
				'return_to' => '/'
			));
			
			$result = curl_exec(self::$curl);
			
			//file_put_contents(AHREFS_COMPONENT_DIR.'/__ahrefs_auth_post.txt', $result);
		}
	}
	
	public static function init(){
		if(self::$instance === null) self::$instance = new self();
		return self::$instance;
	}
	
	public static $chartTypeList = array(
		'anchors_cloud',
		'anchors_terms_cloud',
		'top_anchors_phrases_refpages_bar',
		'top_anchors_phrases_refdomains_bar',
		'top_anchors_words_refdomains_bar'
	);
	
	private $ajaxway = array(
		'main_chart',
		'arank_chart',
		'tlds_chart',
		'all_anchors_chart'
	);
	
	public function getAnchorsCloud($domain){
		return $this->getByType('anchors_cloud', $domain);
	}
	
	public function getAnchorsTermsCloud($domain){
		return $this->getByType('anchors_terms_cloud', $domain);
	}
	
	public function getHash($domain){
		self::initCurl();
		
		$domain_encoded = urlencode(urlencode($domain));
		$url = 'https://ru.ahrefs.com/site-explorer/overview/subdomains/?target='.$domain_encoded;
		curl_setopt(self::$curl, CURLOPT_VERBOSE, true);
		curl_setopt(self::$curl, CURLOPT_URL, $url);
		$result = curl_exec(self::$curl);
		
		////file_put_contents(dirname( __FILE__ ) . '/../files/ahrefs_'.$domain.'.html',$result);
		
		//echo $result;
		//hash: '1359b037e998ec75b27bb9cde654cbfe'
		
		//preg_match("/\?hash=([a-z0-9]+)\&/",$result,$contentFirstHash);
		//return $contentFirstHash[1];
		
		preg_match('/CSHash\s=\s"([a-z0-9]+)"/i',$result,$contentFirstHash);
		return $contentFirstHash[1];
		
	}
	
	public function getByType($chartType,$domain){
		self::initCurl();
		
		$domain_encoded = urlencode(urlencode($domain));
		
		$url = 'https://ru.ahrefs.com/site-explorer/overview/subdomains/?target='.$domain_encoded;
		curl_setopt(self::$curl, CURLOPT_URL, $url );
		$result = curl_exec(self::$curl);
		
		preg_match('/CSHash\s=\s"([a-z0-9]+)"/i',$result,$contentFirstHash);
		$hash = $contentFirstHash[1];
		
		preg_match('/social_media_hash\s=\s"([a-z0-9]+)"/i',$result,$contentSocialHash);
		$social_hash = $contentSocialHash[1];
		
		/*
		var_dump($hash);
		var_dump($social_hash);
		
		self::initCurl();
		$url_soc = sprintf('https://ahrefs.com/site-explorer/ajax/get/social_metrics/%s',$social_hash);
		curl_setopt(self::$curl, CURLOPT_URL, $url_soc );
		curl_setopt(self::$curl, CURLOPT_REFERER, $url );
		$result = curl_exec(self::$curl);
		//file_put_contents(AHREFS_COMPONENT_DIR.'/__ahrefs_social.txt', $result);
		*/
		
		self::initCurl();
		$url_add = sprintf('https://ahrefs.com/site-explorer/overview/subdomains/?target=www.salon-maria.ru&type=text_refdomains_stats&hash=%s', $hash);
		curl_setopt(self::$curl, CURLOPT_URL, $url_add );
		curl_setopt(self::$curl, CURLOPT_REFERER, $url );
		curl_setopt(self::$curl, CURLOPT_HTTPHEADER, array('X-Requested-With' => 'XMLHttpRequest'));
		$result = curl_exec(self::$curl);
		
		//file_put_contents(AHREFS_COMPONENT_DIR.'/__ahrefs_refdomains_stats.txt', $result);
		
		//https://ru.ahrefs.com/site-explorer/ajax/overview/all_anchors_chart/2a6377304de3c1d6b0eea69e882b59f9
		
		foreach ($this->ajaxway as $ajaxkey) {
			self::initCurl();
			$ajaxurl = sprintf('https://ru.ahrefs.com/site-explorer/ajax/overview/%s/%s', $ajaxkey, $hash);
			curl_setopt(self::$curl, CURLOPT_HTTPHEADER, array('X-Requested-With' => 'XMLHttpRequest'));
			curl_setopt(self::$curl, CURLOPT_URL, $ajaxurl);
			curl_setopt(self::$curl, CURLOPT_REFERER, $url);
			$result = curl_exec(self::$curl);
			
			//file_put_contents(AHREFS_COMPONENT_DIR.'/__ahrefs__'.$ajaxkey.'.txt', $result);
		}
		
		
		curl_setopt(self::$curl, CURLOPT_HTTPHEADER, array('X-Requested-With' => 'XMLHttpRequest'));
		curl_setopt(self::$curl, CURLOPT_URL, 'https://ru.ahrefs.com/site-explorer');
		curl_setopt(self::$curl, CURLOPT_REFERER, $ajaxurl);
		$result = curl_exec(self::$curl);
		
		//file_put_contents(AHREFS_COMPONENT_DIR.'/__ahrefs__explorer.txt', $result);
		
		/*
		self::initCurl();
		//$url_json = 'https://ru.ahrefs.com/site-explorer/get_overview_charts_data.php?chart_type=anchors_cloud&hash='.$hash;
		$url_json = sprintf('https://ru.ahrefs.com/site-explorer/ajax/overview/all_anchors_chart/%s', $hash);
		curl_setopt(self::$curl, CURLOPT_HTTPHEADER, array('X-Requested-With' => 'XMLHttpRequest'));
		curl_setopt(self::$curl, CURLOPT_URL, $url_json );
		curl_setopt(self::$curl, CURLOPT_REFERER, $url );
		$result = curl_exec(self::$curl);
		
		//file_put_contents(AHREFS_COMPONENT_DIR.'/__ahrefs_anchors.txt', $result);
		*/
		
		return json_decode($result);
		
	}

}