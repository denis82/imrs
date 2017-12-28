<?php

namespace LinkPad\XML;

class Element {

	public $value;

	public function __construct($xml){

		if(preg_match('/>([^<]+)</si', $xml, $match)){
			$this->value = $match[1];

			if(is_numeric($this->value)){
				$this->value = (int)$this->value;
			}
		}

		preg_match_all('/\s([a-z0-9_]+)="([^"]*?)"/si', $xml, $matches);

		if(count($matches[1]) > 0){

			foreach($matches[1] as $k => $v){

				$this->{$v} = $matches[2][$k];

				if(is_numeric($this->{$v})){
					$this->{$v} = (int)$this->{$v};
				}

			}

		}

	}
	
	public function __toString(){
		return (string)$this->value;
	}

}