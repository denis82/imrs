<?php

namespace LinkPad\XML;

class Index extends Element {
	public $date;
	public $time;

	public function __construct($xml){
		parent::__construct($xml);

		if($this->date && preg_match('/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/', $this->date, $match)){
			$this->time = mktime(0, 0, 0, (int)$match[2], (int)$match[1], (int)$match[3]);
		}
	}
}