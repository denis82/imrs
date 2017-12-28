<?php

class Mystem {
	private $fname = '';

	public $result = array();

	public function __construct( $text ) {
		$this->fname = Yii::app()->params['Mystem']['tmp'] . crc32( $text );

		file_put_contents( $this->fname, $text);
	}

	/*public function check() {
		@exec( Yii::app()->params['Mystem']['path'] . ' ' . $this->fname . ' -n -l', $output);

		if ($output and is_array($output)) {
			foreach ($output as $line) {
				list($word, $tmp) = explode('|', $line, 2);

				$word = str_replace('?', '', $word);

				$this->result[$word]++;
			}
		}
	}*/

	public function check() {
		@exec( Yii::app()->params['Mystem']['path'] . ' ' . $this->fname . ' -lig --format=json', $output);

		if ($output and is_array($output)) {
			foreach ($output as $line) {
				$data = json_decode($line);

				if ($data) {

					if (is_array($data)) {
						foreach ($data as $j => $i) {
							if ($i->analysis and $i->analysis[0]) {
								$gr = explode(',', $i->analysis[0]->gr);
								$gr = explode('=', $gr[0]);
								$i->word = $i->analysis[0]->lex;
								$i->gr = $gr[0];
							}
							else {
								$i->word = mb_strtolower( $i->text, 'utf-8' );
								$i->gr = '';
							}

							$this->result[] = $i;
						}
					}

					/*list($word, $tmp) = explode('|', $line, 2);

					$word = str_replace('?', '', $word);

					$this->result[$word]++;*/
				}
			}
		}
	}

	public function checkZ() {
		@exec( Yii::app()->params['Mystem']['path'] . ' ' . $this->fname . ' -lig --format=json', $output);

		if ($output and is_array($output)) {
			foreach ($output as $line) {
				$data = json_decode($line);

				if ($data) {

					if (is_array($data)) {
						foreach ($data as $j => $i) {
							if ($i->analysis and $i->analysis[0]) {
								$gr = explode(',', $i->analysis[0]->gr);
								$gr = explode('=', $gr[0]);
								$i->word = $i->analysis[0]->lex;
								$i->gr = $gr[0];
							}
							else {
								$i->word = mb_strtolower( $i->text, 'utf-8' );
								$i->gr = '';
							}

							$this->result[] = $i;
						}
					}

					/*list($word, $tmp) = explode('|', $line, 2);

					$word = str_replace('?', '', $word);

					$this->result[$word]++;*/
				}
			}
		}
	}

	public function end() {
		@unlink($this->fname);
	}

}