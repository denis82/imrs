<?php

class PagesSpell extends CActiveRecord {
	public static $ERROR_DESC = array(
		1 => 'Слова нет в словаре.',
		2 => 'Повтор слова.',
		3 => 'Неверное употребление прописных и строчных букв.',
		4 => 'Текст содержит слишком много ошибок.'
	);

	private $_spell_result;

    public function tableName() {
        return '{{pages_spell}}';
    }

    public function rules() {
        return array(
            array('page_id, text', 'required'),
            array('page_id', 'numerical', 'integerOnly' => true),
            array('id, page_id, text', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'page' => array(self::BELONGS_TO, 'Page', 'page_id'),
        );
    }

    public function attributeLabels() {
        return array(
			'id' => 'ID',
        );
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    private function spellResult() {
    	if (!$this->_spell_result) {
    		$this->_spell_result = json_decode($this->text);
    	}

		return $this->_spell_result;
    }

    public function hasErrors() {
    	$data = $this->spellResult();
    	return ($data and is_array($data) and count($data));
    }

    public function shortText() {
    	$text = $this->page->getText();

    	$data = $this->spellResult();

    	$result = '';

    	if (is_array($data)) {
    		foreach ($data as $r) {
				$pos = $r->pos - 25;
				while ($pos > 0 and !in_array(mb_substr($text, $pos, 1), array(' ', '.', ',', "\n"))) {
					$pos--;
				}
				if ($pos < 0) {
					$pos = -1;
				}
				$pos++;

				$len = $r->len + ($r->pos - $pos) + 25;
				while ($pos + $len < mb_strlen($text) and !in_array(mb_substr($text, $pos + $len, 1), array(' ', '.', ',', "\n"))) {
					$len++;
				}

    			$part = mb_substr($text, $pos, $len);

    			$part1 = mb_substr($part, 0, $r->pos - $pos);
    			$word = mb_substr($part, $r->pos - $pos, $r->len);
    			$part2 = mb_substr($part, $r->pos - $pos + $r->len);

    			$result .= "..." . $part1 . '<b class="hlword" title="' . 
    				( ($r->s and is_array($r->s) and count($r->s)) ? implode(', ', $r->s) : self::$ERROR_DESC[ $r->code ] ) . 
    				'">' . $word . '</b>' . $part2 . "...\n";
    		}
    	}

    	return $result;
    }

}
