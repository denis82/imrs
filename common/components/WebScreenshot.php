<?php

class WebScreenshot {
	private $url = '';
	private $width = 1024;

	private $_image = null;

	public function __construct( $params = array() ) {
		foreach ($params as $j => $i) {
			$this->$j = $i;
		}
	}

	public static function model( $params = array() ) {
		return new self($params);
	}

	public function grab() {
		if (!$this->url) {
			return false;
		}

        $url = 'http://mini.s-shot.ru/'.$this->width.'/'.$this->width.'/png/?' . $this->url;

        if ($screen = @file_get_contents($url)) {
        	$this->_image = $screen;
        }

        return $this->_image;
	}

	public function save( $path ) {
        return file_put_contents($path, $this->grab());
	}

}