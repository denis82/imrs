<?php

class Block {
	
	private static $__prefix = '';
	
	private $name = 'default';
	private $attributes = array();
	private $short = true;
	private $blocks = array();
	private $text = false;
	
	private function __construct(){}
	
	public static function prefix($prefix){
		self::$__prefix = $prefix;
	}
	
	public static function create($name,array $attributes = array(),$no_short=false){
		$_block = new self();
		$_block->name = $name;
		$_block->attributes = $attributes;
		if($no_short) $this->short = false;
		return $_block;
	}
	
	public static function text($name,array $attributes = array(),$text = ''){
		return self::create($name,$attributes,false)->setText($text);
	}
	
	public function setText($string){
		$this->text = $string;
		$this->short = false;
		return $this;
	}
	
	public function insert($block){
		if(get_class($block) != __CLASS__) return $this;
		$this->blocks[] = $block;
		$this->short = false;
		return $this;
	}	
	
	public function compile(){
		$str  = '';
		$str .= '<';
		$str .= self::$__prefix . $this->name;
		foreach($this->attributes as $key => $val) 
			$str .= ' '. self::$__prefix . $key.'="'.$val.'"';
		if($this->short) $str .= '/>';
		else{
			$str .= '>';
			if(is_string($this->text)) $str .= $this->text;
			else
				foreach($this->blocks as $block) 
					if(get_class($block) == __CLASS__)
						$str .= $block->compile();
			$str .= '</';
			$str .= self::$__prefix . $this->name;
			$str .= '>';
		}
		return $str;
	}
	
	public static function treeCombiner(array $array){
		
	}
	
}