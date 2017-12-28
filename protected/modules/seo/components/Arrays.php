<?php

class Arrays {
	
	static function getValue(&$array, $key){
		if(!array_key_exists($key,$array)) return NULL;
		return $array[$key];
	}
	
}