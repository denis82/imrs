<?php

namespace Whois\Info\Element;

use Whois\Info\Element;

class NServer extends Element {

	private $servers = [];

	private $iteratorCounter = -1;

	public function __construct(){

	}

	public function addServer($value){
		$value = trim($value);

		$this->servers[] = $value;
	}

	public function getServer($index){
		return isset($this->servers[$index])? $this->servers[$index] : false;
	}

	public function getServersArray(){
		return $this->servers;
	}

	public function nextServer(){
		$this->iteratorCounter++;

		return $this->getServer($this->iteratorCounter);
	}

	public function reset(){
		$this->iteratorCounter = -1;
	}

}