<?php

namespace Whois\Info\Element;

use Whois\Info\Element;

/**
 * Class Date
 * @package Whois\Info\Element
 *
 * @property \DateTime $dateTime
 */
class Date extends Element {

	public $date;

	public $dateTime;

	public $dateInterval;

	public $gone;

	public $time = 0;

	private $bindShortMonths = [
		'jan' => 1,
		'feb' => 2,
		'mar' => 3,
		'apr' => 4,
		'may' => 5,
		'jun' => 6,
		'jul' => 7,
		'aug' => 8,
		'sep' => 9,
		'oct' => 10,
		'nov' => 11,
		'dec' => 12
	];

	public function __construct($value){

		$value = strtolower(trim($value));

		if(preg_match('/([0-9]{4})\-([0-9]{2})\-([0-9]{2})/' ,$value, $match)){
			$this->time = mktime(0, 0, 0, $match[2], $match[3], $match[1]);
		}

		if(preg_match('/([0-9]{2})\-([a-z]{3})\-([0-9]{4})/i', $value, $match)){
			$month = strtolower($match[2]);
			$monthNumber = (int)$this->bindShortMonths[$month];
			$this->time = mktime(0, 0, 0, $monthNumber, (int)$match[1], (int)$match[3]);
		}

		if(preg_match('/([0-9]{2})\.([0-9]{2})\.([0-9]{4})/' ,$value, $match)){
			$this->time = mktime(0, 0, 0, $match[2], $match[1], $match[3]);
		}

		if(preg_match('/([0-9]{4})\.([0-9]{2})\.([0-9]{2})/' ,$value, $match)){
			$this->time = mktime(0, 0, 0, $match[2], $match[3], $match[1]);
		}

		if($this->time) {

			$this->date = date('Y-m-d', $this->time);

			if(class_exists('DateTime')){

				$this->dateTime = new \DateTime($this->date);

				$todayDate = new \DateTime();

				$this->dateInterval = $this->dateTime->diff($todayDate);

				$goneArray = [];

				if($this->dateInterval->y > 0)
					$goneArray[] = sprintf('%d %s', $this->dateInterval->y, self::declensionEnding($this->dateInterval->y, ['год','года','лет']));

				if($this->dateInterval->m > 0)
					$goneArray[] = sprintf('%d %s', $this->dateInterval->y, self::declensionEnding($this->dateInterval->y, ['месяц','месяца','месяцев']));

				if($this->dateInterval->d > 0)
					$goneArray[] = sprintf('%d %s', $this->dateInterval->y, self::declensionEnding($this->dateInterval->y, ['день','дня','дней']));

				$this->gone = join(' ', $goneArray);

			}

		}

		$this->value = $value;
	}

	public static function declensionEnding($number, array $ending){
		$number = $number % 100;
		if ($number>=11 && $number<=19) {
			$result = $ending[2];
		}
		else {
			$i = $number % 10;
			switch ($i)
			{
				case (1): $result = $ending[0]; break;
				case (2):
				case (3):
				case (4): $result = $ending[1]; break;
				default: $result = $ending[2];
			}
		}
		return $result;
	}

}