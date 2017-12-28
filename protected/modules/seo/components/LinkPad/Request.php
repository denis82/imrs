<?php

namespace LinkPad;

class Request {

	private $options = [
		'http' => [
			'header'  => '',
			'method'  => 'GET',
			'content' => '',
			'user_agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0',

			// corporate proxy thing ...
			//'proxy' => 'proxy-ip:proxy-port',
			'request_fulluri' => true,
		]
	];

	private $headers = [];

	private $domain = false;

	private $serviceUrl = 'http://xml.linkpad.ru/?url=%s';

	public function __construct($domain){
		$this->setDomain($domain);
	}

	public function setDomain($domain){
		$this->domain = $domain;
	}

	public function send(){
		$serviceUrl = sprintf($this->serviceUrl, $this->domain);
		$context = stream_context_create($this->options);
		$file = fopen($serviceUrl, 'r', null, $context);
		return stream_get_contents($file);
	}

	public function setServiceUrl($url){
		$this->serviceUrl = $url;
	}

	/**
	 * Set UserAgent string for request
	 * @param string $ua
	 */
	public function setUserAgent($ua){
		$this->options['http']['user_agent'] = $ua;
	}

	public function setMethod($method){
		$method = strtoupper(trim($method));

		if(!in_array($method, ['POST', 'GET', 'PUT', 'DELETE', 'HEAD'])){
			$method = 'GET';
		}

		if(in_array($method, ['POST', 'PUT'])){
			$this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
		}else{
			$this->setHeader('Content-Type', '');
		}

		$this->options['http']['method'] = $method;
	}

	/**
	 * @param string $name
	 * @param string $value Value of header, set empty to delete
	 */
	public function setHeader($name, $value){
		$name = join('-', array_map('ucfirst', array_filter(explode('-', strtolower($name)))));

		$this->headers[$name] = trim($value);

		$this->options['http']['header'] = $this->buildHeaders();
	}

	private function buildHeaders(){
		if(count($this->headers) == 0) return '';

		$headerStrings = [];

		foreach($this->headers as $name => $value){
			if(!$value) continue;

			$headerStrings[] = sprintf('%s: %s', $name, $value);
		}

		return join("\r\n", $headerStrings) . "\r\n";
	}

	/**
	 * @param string|array $content
	 */
	public function setContent($content){
		if(is_array($content)){
			$content = http_build_query($content);
		}

		$this->options['http']['content'] = (string)$content;
	}

	public function setProxy($login, $password){
		$this->options['http']['proxy'] = sprintf('%s:%s', $login, $password);
	}

}