<?php

class Clickjacking {
	static $services = array(
		'socfishing.ru' => 'SocFishing',
		'userclick.su' => 'UserClick',
		'lptracker.ru' => 'LP Tracker',
		'soceffect.ru' => 'SocEffect',
		'convead.ru' => 'Convead',
		'smmoke.ru' => 'SMMoke',
		'smmscan.ru' => 'SMM Scaner',
		'soc-spy.ru' => 'SocSpy',
		'sochunter.com' => 'СоцХантер',
		'soctracker.ru' => 'SocTracker',
		'soctraffic.ru' => 'SocTraffic',
		'vboro.de' => 'Бородач',
		'callbackhub.com' => 'Callback Hub',
	);

	public static function Test( $js ) {
		$r = array();

		foreach (self::$services as $j => $i) {
			$name = 'test' . str_replace(array('.', '-'), '', $j);

			if (self::$name($js)) {
				$r[$j] = true;
			}
		}

		return $r;
	}

	private static function base64( $js ) {
		$code = '';

		if ( preg_match('/base64,[a-zA-Z0-9\/\r\n+]*={0,2}/', $js, $m) ) {
			foreach ($m as $i) {
				$b = substr($i, 7);
				$a = base64_decode($b);

				$code .= ' ' . $a;
			}
		}

		return $code;
	}

	public static function testsocfishingru( $js ) {
		$code = self::base64( $js );
		return (strpos($code, 'eval(') !== false);
	}

	public static function testuserclicksu( $js ) {
		return (strpos($js, 'userclick.su') !== false);
	}

	public static function testlptrackerru( $js ) {
		return (strpos($js, 'lptracker') !== false);
	}

	public static function testsoceffectru( $js ) {
		return (strpos($js, 'soceffect.ru') !== false);
	}

	public static function testconveadru( $js ) {
		return (strpos($js, 'convead.ru') !== false);
	}

	public static function testsmmokeru( $js ) {
		$code = self::base64( $js );
		return (strpos($code, 'smmoke.ru') !== false);
	}

	public static function testsmmscanru( $js ) {
		return (strpos($js, 'smmscan.ru') !== false);
	}

	public static function testsocspyru( $js ) {
		return (strpos($js, 'soc-spy.ru') !== false);
	}

	public static function testsochuntercom( $js ) {
		return (strpos($js, 'sochunter.com') !== false);
	}

	public static function testsoctrackerru( $js ) {
		return (strpos($js, 'soctracker.ru') !== false);
	}

	public static function testsoctrafficru( $js ) {
		$code = self::base64( $js );
		return (strpos($code, 'soctraffic.ru') !== false);
	}

	public static function testvborode( $js ) {
		$code = self::base64( $js );
		return (strpos($code, 'vboro.de') !== false);
	}

	public static function testcallbackhubcom( $js ) {
		return (strpos($js, 'callbackhub.com') !== false);
	}

}