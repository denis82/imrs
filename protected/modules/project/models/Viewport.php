<?php

class Viewport {
	public $_data = array();

	public static $NAMES = array(
		'width' => 'Ширина',
		'height' => 'Высота',
		'initial-scale' => 'Масштаб начальный',
		'user-scalable' => 'Изменение масштаба',
		'minimum-scale' => 'Масштаб минимальный',
		'maximum-scale' => 'Масштаб максимальный',
	);

	public function __construct($value) {

        $arr = explode(',', $value);

        foreach ($arr as $j => $i) {
            list($a, $b) = explode('=', trim($i), 2);
            $this->$a = $b;
        }

	}

	public static function model($value) {
		return new self($value);
	}

	public function __get($j) {
		return $this->_data[$j];
	}

	public function __set($j, $i) {
		return $this->_data[$j] = $i;
	}

	public function textValues() {
		$r = array();

		foreach ($this->_data as $j => $i) {
			$r[] = array(
				'name' => $j,
				'title' => self::$NAMES[ $j ],
				'value' => $i,
				'text' => $this->describeValue($j),
			);
		}

		return $r;
	}

	public function describeValue($j) {
		switch ($j) {
			case 'width':
				if ($this->$j == 'device-width') {
					$r = 'ширина устройства';
				}
				else {
					$r = $this->$j;
				}

				break;
			case 'height':
				if ($this->$j == 'device-height') {
					$r = 'высота устройства';
				}
				else {
					$r = $this->$j;
				}

				break;
			case 'user-scalable':
				if ($this->$j == 'yes') $r = 'разрешено';
				elseif ($this->$j == 'no') $r = 'запрещено';
				else $r = '';

				break;
			default:
				$r = '';
				break;
		}

		return $r;
	}

}