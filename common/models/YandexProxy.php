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

