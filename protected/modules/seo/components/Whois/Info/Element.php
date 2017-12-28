<?php

namespace Whois\Info;

class Element {

	protected $value;

	public function __construct($value){
		$this->setValue($value);
	}

	public function setValue($value){
		$value = trim($value);

		$this->value = $value;
	}

	public function getValue(){
		return $this->value;
	}

	public function __toString()
	{
		return $this->getValue();
	}

}