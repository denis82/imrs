<?php

namespace Whois;

/**
 * Class Info
 * @package Whois
 *
 * @property Info\Element\Date|boolean $created
 * @property Info\Element\Date|boolean $updated
 * @property Info\Element\Date|boolean $expiration
 * @property Info\Element\NServer|boolean $ns
 * @property Info\Element $registrar
 *
 * @property Info\Element[] $data
 */
class Info {

	private $domain;

	public $data = [];

	private $bindNames = [
		'nserver' => 'ns',
		'name server' => 'ns',
		//'free-date' => 'free-date',
		'paid-till' => 'expiration',
		'updated date' => 'updated',
		'domain last updated date' => 'updated',
		'last updated on' => 'updated',
		'creation date' => 'created',
		'creation' => 'created',
		'expiration date' => 'expiration',
		'domain expiration date' => 'expiration',
		'registrar registration expiration date' => 'expiration',
		'org' => 'organization',
		'organisation' => 'organization',
		'registrant name' => 'registrar'
	];

	public function __construct($domain){
		$this->setDomain($domain);
	}

	/**
	 * Request whois information
	 * @return bool
	 */
	public function get(){

		$whoisInfo = false;

		if(function_exists('shell_exec')) $whoisInfo = shell_exec(sprintf('whois %s', $this->domain));

		if(!preg_match('/domain:/i', $whoisInfo)) $whoisInfo = false;

		if($whoisInfo == false){

			$context = stream_context_create([
				'http' => [
					'header' => "Referer: https://www.nic.ru/whois/?wi=1\r\n",
					'method' => 'GET',
					'follow_location' => 1,
					'timeout' => 10,
					'user_agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36'
				]
			]);

			$whois = file_get_contents(sprintf('https://www.nic.ru/whois/?query=%s', urlencode($this->domain)), false, $context);

			$whois = iconv('windows-1251', 'utf-8', $whois);

			if(strpos($whois, '<div class="b-whois-info">')==false){
				return false;
			}

			$whoisInfo = array_shift(explode('<div class="b-whois-info__divider">', array_pop(explode('<div class="b-whois-info">', $whois))));

		}

		$whoisInfo = preg_replace('/\s{2,}/', ' ', str_replace('&nbsp;', ' ', $whoisInfo));

		$whoisInfo = preg_replace('/\n{2,}/', "\n", str_replace('<br>', "\n", $whoisInfo));

		preg_match_all('/([~a-z0-9\-\s]+):([^\n<>]+)\n/is', $whoisInfo, $match);

		if(count($match[1])){

			foreach($match[1] as $k => $name){

				$name = strtolower(trim($name));

				if(isset($this->bindNames[$name]))
					$name = $this->bindNames[$name];

				switch($name){
					case 'created':
					case 'updated':
					case 'expiration':
							$this->data[$name] = new Info\Element\Date($match[2][$k]);
						break;
					case 'ns':
							if(!isset($this->data[$name]) || $this->data[$name] == false){
								$this->data[$name] = new Info\Element\NServer();
							}

							$this->data[$name]->addServer($match[2][$k]);
						break;
					case 'state':
							$this->data[$name] = new Info\Element\State($match[2][$k]);
						break;
					default:
							$this->data[$name] = new Info\Element($match[2][$k]);
						break;
				}

			}

		}

		return true;
	}

	public function __get($name){
		if(isset($this->data[$name]))
			return $this->data[$name];

		return null;
	}

	public function setDomain($domain){
		$domain = preg_replace('/(https?:\/\/|https?:\/\/www\.)/i', '', $domain);

		$this->domain = $domain;
	}

	public function getDomain(){
		return $this->domain;
	}

}