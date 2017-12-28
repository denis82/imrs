<?php

class FontFace {
	public $family = '';
	public $src = array();
	public $style = 'normal';
	public $weight = 'normal';
	public $stretch = 'normal';
	public $unicode_range = null;

	private $_hash = null;
	private $_class_name = null;

	public function __construct( $text = null ) {
		if ($text) {
			$this->parse($text);
		}
	}

	public function parse($text) {

		preg_match_all('/([a-z\-]*)\s*:\s*(.*?);/si', $text, $matches);

		foreach ($matches[1] as $j => $i) {
			$name = strtolower(trim($i));
			$value = trim($matches[2][$j]);

			switch ($name) {
				case 'font-family':
					if ($value[0] == '"' or $value[0] == "'") {
						$value = substr($value, 1, -1);
					}

					$this->family = $value;
					break;

				case 'font-style':
					$this->style = $value;
					break;

				case 'font-weight':
					$this->weight = $value;
					break;

				case 'font-stretch':
					$this->stretch = $value;
					break;

				case 'unicode-range':
					$this->unicode_range = $value;
					break;

				case 'src':
					$this->src[] = $value;
					break;
			}

		}

	}

	public function style( $prefix ) {
		$css = '
			font-family: "' . $prefix . $this->family . '";
			font-style: ' . $this->style . ';
			font-weight: ' . $this->weight . ';
			font-stretch: ' . $this->stretch . ';
			' . ($this->unicode_range ? 'unicode-range: ' . $this->unicode_range . ';' : '') . '
		';

		foreach ($this->src as $src) {
			$css.= ' src: ' . $src . ' ; ';
		}

		return '@font-face { ' . $css . ' } ';
	}

	public function exampleStyle( $prefix ) {

		$css = '.' . $this->className($prefix) . ' {
			font-family: "' . $prefix . $this->family . '", serif;
			font-style: ' . $this->style . ';
			font-weight: ' . $this->weight . ';
			font-stretch: ' . $this->stretch . ';
		} ';

		return $css;
	}

	public function getClassHash() {
		if (!$this->_hash) {
			$this->_hash = md5( $prefix . 
				$this->family . 
				$this->style . 
				$this->weight .
				$this->stretch
			);
		}

		return $this->_hash;
	}

	public function className( $prefix ) {
		if (!$this->_class_name) {
			$this->_class_name = uniqid( $this->getClassHash() );
		}

		return $prefix . $this->_class_name;
	}


}