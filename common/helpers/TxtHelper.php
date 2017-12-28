<?php

class TxtHelper {

	public static function DateTimeFormat( $d ) {
		if (!is_numeric($d)) {
			$d = strtotime($d);
		}

		return date('d.m.Y H:i', $d);
	}

	public static function DateFormat( $d ) {
		if (!is_numeric($d)) {
			$d = strtotime($d);
		}

		return date('d.m.Y', $d);
	}

	public static function LivePeriod( $d ) {
		if (is_numeric($d)) {
			$d = date('Y-m-d H:i:s', $d);
		}

		$d1 = date_create($d);
		$d2 = date_create();

		$interval = date_diff($d1, $d2);

		list($y, $m, $d) = explode(' ', $interval->format("%y %m %d"));

		$s = array();

		if ($y) {
			$s[] = $y . ' ' . Yii::t('year', 'год|года|лет', array($y));
		}

		if ($m) {
			$s[] = $m . ' ' . Yii::t('year', 'месяц|месяца|месяцев', array($m));
		}

		if ($d) {
			$s[] = $d . ' ' . Yii::t('year', 'день|дня|дней', array($d));
		}

		return implode(', ', $s);
	}

	public static function googleFormatString($text, $args = null) {

		if (is_array($args)) {
			foreach ($args as $arg) {
				switch ($arg->key) {
					case 'LINK':
						$text = str_replace('{{BEGIN_LINK}}', '<a href="' . $arg->value . '" target="_blank">', $text);
						$text = str_replace('{{END_LINK}}', '</a>', $text);
						break;

					default:
						$text = str_replace('{{' . $arg->key . '}}', str_replace(array('<', '>'), array('&lt;', '&gt;'), $arg->value), $text);
				}
			}
		}

		return $text;
	}

	public static function phraseGrammarParts( $text ) {
		$descr = array(
			'A' 	=>	'прилагательное',
			'ADV'	=>	'наречие',
			'ADVPRO'=>	'местоименное наречие',
			'ANUM'	=>	'числительное-прилагательное',
			'APRO'	=>	'местоимение-прилагательное',
			'COM'	=>	'часть композита - сложного слова',
			'CONJ'	=>	'союз',
			'INTJ'	=>	'междометие',
			'NUM'	=>	'числительное',
			'PART'	=>	'частица',
			'PR'	=>	'предлог',
			'S'		=>	'существительное',
			'SPRO'	=>	'местоимение-существительное',
			'V'		=>	'глагол',
		);

		$descr = array(
			'A' 	=>	'прил',
			'ADV'	=>	'нареч',
			'ADVPRO'=>	'мест нареч',
			'ANUM'	=>	'числ-прил',
			'APRO'	=>	'мест-прил',
			'COM'	=>	'часть композита - сложного слова',
			'CONJ'	=>	'союз',
			'INTJ'	=>	'межд',
			'NUM'	=>	'числ',
			'PART'	=>	'част',
			'PR'	=>	'пред',
			'S'		=>	'сущ',
			'SPRO'	=>	'мест-сущ',
			'V'		=>	'гл',
		);

		$parts = explode('+', $text);

		foreach ($parts as $j => $i) {
			$parts[$j] = $descr[$i] ? $descr[$i] : '?' . $i;
		}

		return implode(' + ', $parts);
	}

	public static function utf8Urldecode($string) {
		return $string;

		$string = "%D0%BA%D0%BE%D0%B4%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D1%82%D1%8C";

		print $string;

		die();
	}

	public static function toDocx( $html ) {
		$html = str_replace('&nbsp;', ' ', $html);
		$html = strip_tags($html);

		$html = html_entity_decode($html);

		return $html;
	}

}