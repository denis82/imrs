<?php

namespace LinkPad {

	class XML
	{

		/**
		 * @var string $host
		 */
		public $host;
		public $mr;
		public $ip;
		public $dout;
		public $anchors;
		public $anchors_out;
		public $igood;
		public $referring_ips;
		public $referring_subnets;

		/**
		 * @var XML\Index $index
		 */
		public $index;

		/**
		 * @var XML\Counter $hin
		 */
		public $hin;

		/**
		 * @var XML\Counter $din
		 */
		public $din;

		/**
		 * @var XML\Counter $hout
		 */
		public $hout;

		public function __construct()
		{

		}
		/*
		 <?xml version="1.0" encoding="UTF-8"?>
		<data>
		<host>historie.ru</host>
		<index date="20.01.2015">300</index>
		<mr>0</mr>
		<ip>399</ip>
		<hin l1="3" l2="9" l3="82" l4="136">230</hin>
		<din l1="2" l2="9" l3="47" l4="23">74</din>
		<hout l1="3" l2="413" l3="361" l4="361">825</hout>
		<dout>30</dout>
		<anchors>58</anchors>
		<anchors_out>24</anchors_out>
		<igood>1246537/915840</igood>
		<referring_ips>48</referring_ips>
		<referring_subnets>48</referring_subnets>
		</data>
		*/

		/**
		 * Create exemplar of class with data by parsing linkpad/solomono answer
		 * @param string $xml
		 * @return bool|XML
		 */
		public static function createFromXML($xml)
		{

			if (!preg_match('/<host>[^<]+<\/host>[^<]*<index date="[0-9\.]+">[0-9]+<\/index>/si', $xml))
				return false;

			$class = new XML();

			preg_match_all('/<([a-z_]+)(|\s[^>]+)>([^<]*?)<\/[a-z_]+>/si', $xml, $match);

			if (count($match[1]) > 0) {

				foreach ($match[1] as $k => $v) {
					if ($v == 'index') {

						$class->index = new XML\Index($match[0][$k]);

					} elseif (in_array($v, ['hin', 'din', 'hout'])) {

						$class->{$v} = new XML\Counter($match[0][$k]);

					} else {

						$class->{$v} = preg_match('/^[0-9]+$/', $match[3][$k]) ? (int)$match[3][$k] : $match[3][$k];

					}

				}

			}

			return $class;
		}

	}
}