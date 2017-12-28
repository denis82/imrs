<?php

namespace Whois\Info\Element;

use Whois\Info\Element;

class State extends Element {

	private $states = [];

	public function __construct($value){

		$value = trim($value);

		$this->value = $value;

		$value = strtolower($value);

		$this->states = explode(' ', preg_replace('/\s{2,}/', ' ', preg_replace('/[^a-z\s]+/i', ' ', $value)));
	}

	public function getStates(){
		return strtoupper(join(', ', $this->states));
	}

	public function isRegistered(){
		return in_array('registered', $this->states);
	}

	public function isDelegated(){
		return in_array('delegated', $this->states);
	}

	public function isVerified(){
		return in_array('verified', $this->states);
	}

}