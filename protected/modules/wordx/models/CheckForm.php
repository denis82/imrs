<?php

class CheckForm extends CFormModel {
    public $text;
    public $search_id;

    public function rules() {
        return array(
            array('text', 'required'),
            array('search_id', 'safe'),
        );
    }

    public function attributeLabels() {
        return array(
            'text' => 'Текст для проверки',
        );
    }

    public function save() {
        /*$phrase = array();

        $lines = explode("\n", $this->text);

        foreach ($lines as $l) {
            $words = explode(',', $l);

            foreach ($words as $w) {
                $s = trim($w);

                if (strlen($s)) {
                    $phrase[] = mb_strtolower($s, 'utf-8');
                }
            }
        }

        if (count($phrase)) {
            $sql = "insert ignore into tbl_semantic (project_id, phrase, created_date) values ";

            $parameters = array(
                'pid' => $this->project->id,
            );

            foreach ($phrase as $i => $j) {
                if ($i > 0) $sql .= ', ';

                $sql.= '(:pid, :ph' . $i . ', NOW())';

                $parameters['ph' . $i] = $j;
            }

            Yii::app()->db->createCommand($sql)->execute($parameters);
        }*/

        return true;
    }

    public function checkRus() {

    	if ($this->text) {
            $text = str_replace(array(',', '+', ':', ';'), ' ', $this->text);

            $mystem = new Mystem( $text );
            $mystem->checkZ();

            $result = array();
            $words = array();
            $words_g = array();

            $w_prev = $w_next = array();

            $prev = '';
            $prev_word = false;
            $this_word = array();

            $prev_wform = false;
            $this_wform = array();

            $text_id = time();

            $n = 0;

            foreach ($mystem->result as $chunk) {

            	$prev_word = $this_word;
	            $this_word = array();

	            $prev_wform = $this_wform;
	            $this_wform = array();

            	if (is_array($chunk->analysis)) {
            		foreach ($chunk->analysis as $el) {
            			$word = WordxWord::model()->findByAttributes(array('word' => $el->lex));

            			if (!$word) {
            				$transaction = Yii::app()->db->beginTransaction();

            				$word = new WordxWord;
            				$word->word = $el->lex;
            				$word->save();

            				$transaction->commit();
            			}

            			$gram_all = array();
            			list($gr_a, $gr_b) = explode('=', $el->gr, 2);

            			if (substr($gr_b, 0, 1) == '(' and substr($gr_b, -1) ==')') {
            				$gr_c = explode('|', substr($gr_b, 1, -1));

            				foreach ($gr_c as $gr_d) {
            					$gram_all[] = $gr_a . '=' . $gr_d;
            				}
            			}
            			else {
            				$gram_all[] = $gr_a . '=' . $gr_b;
            			}

            			foreach ($gram_all as $q) {
	            			$gram = WordxGram::model()->findByAttributes(array('gram' => $q));

	            			if (!$gram) {
	            				$transaction = Yii::app()->db->beginTransaction();

	            				$gram = new WordxGram;
	            				$gram->gram = $q;
	            				$gram->save();

	            				$transaction->commit();
	            			}

	            			$wform = WordxForm::model()->findByAttributes(array('word_id' => $word->id, 'gram_id' => $gram->id));

	            			if (!$wform) {
	            				$transaction = Yii::app()->db->beginTransaction();

	            				$wform = new WordxForm;
	            				$wform->word_id = $word->id;
	            				$wform->gram_id = $gram->id;
	            				$wform->word = $chunk->text;
	            				$wform->save();

	            				$transaction->commit();
	            			}

	            			$this_wform[] = $wform;
            			}

            			$gram = WordxGram::model()->findByAttributes(array('gram' => $gram_all[0]));
	            		$wform = WordxForm::model()->findByAttributes(array('word_id' => $word->id, 'gram_id' => $gram->id));

            			$this_word[] = $word;
            		}
            	}

            	foreach ($prev_word as $j) {
            		foreach ($this_word as $i) {
        				$transaction = Yii::app()->db->beginTransaction();

            			if ($pos = WordxPos::model()->findByAttributes(array('first_id' => $j->id, 'second_id' => $i->id))) {
            				$pos->total++;
            				$pos->save();
            			}
            			else {
            				$pos = new WordxPos;
            				$pos->first_id = $j->id;
            				$pos->second_id = $i->id;
            				$pos->total = 1;
            				$pos->save();
            			}

        				$transaction->commit();
            		}
            	}

            	foreach ($prev_wform as $j) {
            		foreach ($this_wform as $i) {
        				$transaction = Yii::app()->db->beginTransaction();

        				$pos = new WordxChain;
        				$pos->text_id = $text_id;
        				$pos->prev_id = $n;
        				$pos->word1 = $j->id;
        				$pos->gram1 = $j->gram_id;
        				$pos->word2 = $i->id;
        				$pos->gram2 = $i->gram_id;
        				$pos->save();

        				$transaction->commit();
            		}
            	}

            	$n++;

            	$result[ $chunk->word ]++;
            	$grammar[ $chunk->word ] = $chunk->gr;

                $words[] = mb_strtolower( $chunk->word, 'utf-8' );
                $words_n[] = mb_strtolower( $chunk->text, 'utf-8' );
                $words_g[] = $chunk->gr;

                if (count($words) > 1) {
                	$w = implode(' ', array_slice($words, -2));

	            	$result[ $w ]++;

	            	if (!isset($grammar[ $w ])) {
	            		$grammar[ $w ] = implode('+', array_slice($words_g, -2));
	            	}
                }

                if (count($words) > 2) {
                	$w = implode(' ', array_slice($words, -3));

	            	$result[ $w ]++;

	            	if (!isset($grammar[ $w ])) {
	            		$grammar[ $w ] = implode('+', array_slice($words_g, -3));
	            	}
                }
            }

            foreach ($words as $j => $i) {
            	if ($j-1 > 0) {
            		if ($words_g[$j-1] == 'S') {
            			$w_prev[ $i ][] = '<a href="#'.md5($words[$j-1]).'">' . $words[$j-1] . '</a>';
            		}
            		else {
            			$w_prev[ $i ][] = $words[$j-1];
            		}
            	}
            	if ($j+1 < count($words)) {
            		if ($words_g[$j+1] == 'S') {
	            		$w_next[ $i ][] = '<a href="#'.md5($words[$j+1]).'">' . $words[$j+1] . '</a>';
            		}
            		else {
	            		$w_next[ $i ][] = $words[$j+1] ;
            		}
            	}
            }

            $line = ''; $k = 0;

            foreach ($words as $j => $i) {
            	if ($prev != $words_g[$j]) {
            		if ($prev) {
            			/*$out.= '<div style="line-height: 0; height: 10px; "><span style="font-size: 7px; opacity: .7;">' .$line . ' - ' . TxtHelper::phraseGrammarParts($prev) . '</span></div>';

            			while ($k > 0) {
	            			$out.= '<div style="line-height: 0; height: 10px; "><span style="font-size: 9px; opacity: .9;">' . implode(' | ', $w_prev[$words[$j-$k]]) . ' = ' . $words[$j-$k] . ' = ' . implode(' | ', $w_next[$words[$j-$k]]) . '</span></div>';
            				$k--;
            			}*/

            			$line = '';
            			$k = 0;
            		}
            	}

            	$prev = $words_g[$j];
            	/*$out.= $i . (($prev == 'S') ? '(' . $result[$i] . ')' : '') . ' ';*/
            	$line.= $words_n[$j] . ' ';
            	$k++;
            }

            $used = array();

            $out .= '<table class="table table-bordered">';
            foreach ($words as $j => $i) {
            	if (in_array($i, $used)) {
            		continue;
            	}

            	//if ($words_g[$j] == 'S') {
            		$used[] = $i;

	            	$out.= '<tr>';
		            	$out.= '<td>'.(is_array($w_prev[$i]) ? implode('<br>', $w_prev[$i]) : '').'</td>';
		            	$out.= '<td style="vertical-align: top;"><a name="'.md5($i).'">'.$i.'</a></td>';
		            	$out.= '<td>'.(is_array($w_next[$i]) ? implode('<br>', $w_next[$i]) : '').'</td>';
	            	$out.= '<tr>';
            	//}
            }
            $out .= '</table>';

            return $out;
    	}
    }

    public function check() {

    	if ($this->text) {
            $text = str_replace(array(',', '+', ':', ';', ".", "!", "?", "»", "«", "–"), ' ', $this->text);
            $text = str_replace(array("\n", "\r"), ' ', $text);

            $all = explode(' ', $text);

            $result = array();
            $words = array();
            $words_g = array();

            $w_prev = $w_next = array();

            $prev = '';
            $prev_word = false;
            $this_word = array();

            $prev_wform = false;
            $this_wform = array();

            $text_id = time();

            $n = 0;

            foreach ($all as $chunk) {
            	$chunk = trim($chunk);

            	if (strlen($chunk) == 0) continue;


            	$n++;

            	$result[ $chunk ]++;
            	$grammar[ $chunk ] = $chunk;

                /*$words[] = mb_strtolower( $chunk, 'utf-8' );
                $words_n[] = mb_strtolower( $chunk, 'utf-8' );*/
                $words[] = $chunk;
                $words_n[] = $chunk;
                $words_g[] = $chunk;

                if (count($words) > 1) {
                	$w = implode(' ', array_slice($words, -2));

	            	$result[ $w ]++;

	            	if (!isset($grammar[ $w ])) {
	            		$grammar[ $w ] = implode('+', array_slice($words_g, -2));
	            	}
                }

                if (count($words) > 2) {
                	$w = implode(' ', array_slice($words, -3));

	            	$result[ $w ]++;

	            	if (!isset($grammar[ $w ])) {
	            		$grammar[ $w ] = implode('+', array_slice($words_g, -3));
	            	}
                }
            }

            foreach ($words as $j => $i) {
            	if ($j-1 > 0) {
            		if (true or $words_g[$j-1] == 'S') {
            			$w_prev[ $i ][] = '<a href="#'.md5($words[$j-1]).'">' . $words[$j-1] . '</a>';
            		}
            		else {
            			$w_prev[ $i ][] = $words[$j-1];
            		}
            	}
            	if ($j+1 < count($words)) {
            		if (true or $words_g[$j+1] == 'S') {
	            		$w_next[ $i ][] = '<a href="#'.md5($words[$j+1]).'">' . $words[$j+1] . '</a>';
            		}
            		else {
	            		$w_next[ $i ][] = $words[$j+1] ;
            		}
            	}
            }

            $line = ''; $k = 0;

            foreach ($words as $j => $i) {
            	if ($prev != $words_g[$j]) {
            		if ($prev) {
            			/*$out.= '<div style="line-height: 0; height: 10px; "><span style="font-size: 7px; opacity: .7;">' .$line . ' - ' . TxtHelper::phraseGrammarParts($prev) . '</span></div>';

            			while ($k > 0) {
	            			$out.= '<div style="line-height: 0; height: 10px; "><span style="font-size: 9px; opacity: .9;">' . implode(' | ', $w_prev[$words[$j-$k]]) . ' = ' . $words[$j-$k] . ' = ' . implode(' | ', $w_next[$words[$j-$k]]) . '</span></div>';
            				$k--;
            			}*/

            			$line = '';
            			$k = 0;
            		}
            	}

            	$prev = $words_g[$j];
            	/*$out.= $i . (($prev == 'S') ? '(' . $result[$i] . ')' : '') . ' ';*/
            	$line.= $words_n[$j] . ' ';
            	$k++;
            }

            $used = array();

            $out .= '<table class="table table-bordered">';
            foreach ($words as $j => $i) {
            	if (in_array($i, $used)) {
            		continue;
            	}

            	//if ($words_g[$j] == 'S') {
            	/*if (in_array(substr($i, 0, 1), array(
            		'A',
            		'B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'


            		))) */{
            		$used[] = $i;

	            	$out.= '<tr>';
		            	$out.= '<td>'.(is_array($w_prev[$i]) ? implode('<br>', $w_prev[$i]) : '').'</td>';
		            	$out.= '<td style="vertical-align: top;"><a name="'.md5($i).'">'.$i.'</a></td>';
		            	$out.= '<td>'.(is_array($w_next[$i]) ? implode('<br>', $w_next[$i]) : '').'</td>';
	            	$out.= '<tr>';
            	}
            }
            $out .= '</table>';

            return $out;
    	}
    }

    public function search() {

    	if ($this->text) {

    		/*$ws = WordxSearch::model()->findByAttributes(array('phrase' => $this->text));
    		if ($ws) {
    			$ws->qty = $ws->qty + 1;
    		}
    		else {
    			$ws = new WordxSearch;
    			$ws->phrase = $this->text;
    			$ws->qty = 1;
    		}
    		$ws->save();*/

            $text = str_replace(array(',', '+', ':', ';'), ' ', $this->text);

            $mystem = new Mystem( $text );
            $mystem->checkZ();

            $words = array();
            $words_g = array();

            $nowords = false;

            foreach ($mystem->result as $j => $chunk) {
            	/*$chunk->(text|word|gr)*/

            	$words[$j] = array();

            	foreach ($chunk->analysis as $al) {
            		foreach (WordxWord::model()->findAllByAttributes(array('word' => $al->lex)) as $w) {
            			if (!in_array($w->id, $words[$j])) {
            				$words[$j][] = $w->id;
            			}
            		}
            	}

            	if (count($words[$j]) == 0) {
            		$nowords = $chunk->word;
            	}
            }

            if (!$nowords) {
            	$chain = array();

	            for ($j = 1; $j < count($words); $j++) {

	            	$chain[$j] = array();

	            	$w1 = $words[$j-1];
	            	$w2 = $words[$j];

	            	$query = 'SELECT c.*, f1.word as form1, f2.word as form2 FROM `tbl_wordx_chain` as c
	            		LEFT JOIN `tbl_wordx_form` as f1 on (c.word1 = f1.id)
	            		LEFT JOIN `tbl_wordx_form` as f2 on (c.word2 = f2.id)
	            		WHERE
	            			f1.word_id ' . (count($w1) == 1 ? ' = ' . $w1[0] : ' in (' . implode(', ', $w1) . ')' ) . ' and 
	            			f2.word_id ' . (count($w2) == 1 ? ' = ' . $w2[0] : ' in ('.implode(', ', $w2).')')
	            	;

	            	if ($j > 1) {
	            		$prev = array();
	            		$prev_index = array();

	            		if (count($chain[$j-1])) {
		            		foreach ($chain[$j-1] as $jj => $ii) {
		            			if ($ii) {
		            				$prev[] = '(c.text_id = '.$ii['text_id'].' and c.prev_id = ' . ($ii['prev_id'] + 1) . ')';
		            				$prev_index[ $ii['text_id'] . '_' . ($ii['prev_id']+1) ] = $jj;
		            			}
		            		}
	            		}
	            		else {
	            			$prev[] = 'c.id = 0';
	            		}

	            		$query.= ' and ('.implode(' or ', $prev).')';
	            	}

					$chain[$j] = Yii::app()->db->createCommand( $query )->queryAll();

					if (count($chain[$j]) == 0) break;
	            }

	            if (count($chain[count($words)-1])) {

	            	$prev = array();
	            	$next = array();
	            	$start = array();

	            	foreach ($chain[1] as $j => $i) {
	            		$l = $i['text_id'] . '_' . ($i['prev_id']-1) . '_' . $i['word1'];
	            		$next[ $l ] = $i['form1'];

		            	$start[ $i['text_id'] . '_' . ($i['prev_id']-1) ] = $i['text_id'] . '_' . ($i['prev_id']-1);
	            	}

	            	foreach ($chain as $k => $c) {
	            		$prev = $next;
	            		$next = array();

	            		foreach ($c as $j => $i) {
		            		$a = $i['text_id'] . '_' . ($i['prev_id']-1) . '_' . $i['word1'];
		            		$b = $i['text_id'] . '_' . $i['prev_id'] . '_' . $i['word2'];
		            		$next[ $b ] = $prev[$a] .' ' . $i['form2'];

		            		$start[ $i['text_id'] . '_' . $i['prev_id'] ] = $start[ $i['text_id'] . '_' . ($i['prev_id']-1) ];
	            		}
	            	}

	            	$next_hash = array();

	            	foreach ($next as $l => $j) {
	            		list($text_id, $prev_id, $word_id) = explode('_', $l, 3);

	            		$k = md5( $text_id . '_' . $prev_id );

	            		if (!$next_hash[$k]) {

	            			list($tmp_id, $start_id) = explode('_', $start[$text_id . '_' . $prev_id]);

	            			$q = 0; $rev = '';
	            			while ($q < 5) {
	            				$wc = WordxChain::model()->findByAttributes(array('text_id' => $text_id, 'prev_id' => $start_id));
	            				$ww = WordxForm::model()->findByPk( $wc->word1 );

	            				$q++;
	            				$start_id--;

	            				$rev = $ww->word . ' '. $rev;
	            			}

	            			$out.= $rev;

	            			$next_hash[$k] = 1;
	            			$out.= '<b>' . $j . '</b>';

	            			$q = 0;
	            			while ($q < 5) {
	            				$wc = WordxChain::model()->findByAttributes(array('text_id' => $text_id, 'prev_id' => $prev_id+1));
	            				$ww = WordxForm::model()->findByPk( $wc->word2 );

	            				$q++;
	            				$prev_id++;

	            				$out.= ' ' . $ww->word;
	            			}

	            			$out.= '<br>';
	            		}
	            	}

	            }
	            else {
		            $out.= 'Ничего не найдено по этой фразе.';
	            }
            }

            else {
            	$out.= 'У нас в базе нет слова ' . $nowords;
            }

            /*$out .= '<table class="table table-bordered">';
            foreach ($words as $j => $i) {

            	$out.= '<tr>';
	            	$out.= '<td style="vertical-align: top;">'.(is_array($w_prev[$j]) ? implode('<br>', $w_prev[$j]) : '').'</td>';
	            	$out.= '<td style="vertical-align: top;"><a name="'.md5($i).'">'.$i.'</a></td>';
	            	$out.= '<td style="vertical-align: top;">'.(is_array($w_next[$j]) ? implode('<br>', $w_next[$j]) : '').'</td>';
            	$out.= '<tr>';

            }
            $out .= '</table>';*/

            return $out;
    	}

    }

    public function ask() {

    	if ($this->text == 'удали' and $this->search_id) {

    		$ws = WordxSearch::model()->findByPk( $this->search_id );
    		if ($ws) {
    			$ws->delete();
    			return 'Я удалила ваш вопрос.';
    		}

    		return 'Я не могу найти вопрос.';
    	}

    	elseif ($this->search_id) {

    		if ($ws = WordxSearch::model()->findByPk( $this->search_id )) {
    			$this->saveWordx( $ws->phrase );
    			$ws->delete();
    		}

    		$this->saveWordx( $this->text );

    		return 'Ваш ответ сохранен.';
    	}

    	elseif ($this->text) {

    		/*$ws = WordxSearch::model()->findByAttributes(array('phrase' => $this->text));
    		if ($ws) {
    			$ws->qty = $ws->qty + 1;
    		}
    		else {
    			$ws = new WordxSearch;
    			$ws->phrase = $this->text;
    			$ws->qty = 1;
    		}
    		$ws->save();*/

            $text = str_replace(array(',', '+', ':', ';'), ' ', $this->text);

            $mystem = new Mystem( $text );
            $mystem->checkZ();

            $words = array();
            $words_g = array();

            $yandex = true;

            $nowords = false;

            foreach ($mystem->result as $j => $chunk) {
            	/*$chunk->(text|word|gr)*/

            	$words[$j] = array();

            	foreach ($chunk->analysis as $al) {
            		foreach (WordxWord::model()->findAllByAttributes(array('word' => $al->lex)) as $w) {
            			if (!in_array($w->id, $words[$j])) {
            				$words[$j][] = $w->id;
            			}
            		}
            	}

            	if (count($words[$j]) == 0) {
            		$nowords = $chunk->word;
            	}
            }

            if (!$nowords) {
            	$chain = array();

	            for ($j = 1; $j < count($words); $j++) {

	            	$chain[$j] = array();

	            	$w1 = $words[$j-1];
	            	$w2 = $words[$j];

	            	$query = 'SELECT c.*, f1.word as form1, f2.word as form2 FROM `tbl_wordx_chain` as c
	            		LEFT JOIN `tbl_wordx_form` as f1 on (c.word1 = f1.id)
	            		LEFT JOIN `tbl_wordx_form` as f2 on (c.word2 = f2.id)
	            		WHERE
	            			f1.word_id ' . (count($w1) == 1 ? ' = ' . $w1[0] : ' in (' . implode(', ', $w1) . ')' ) . ' and 
	            			f2.word_id ' . (count($w2) == 1 ? ' = ' . $w2[0] : ' in ('.implode(', ', $w2).')')
	            	;

	            	if ($j > 1) {
	            		$prev = array();
	            		$prev_index = array();

	            		if (count($chain[$j-1])) {
		            		foreach ($chain[$j-1] as $jj => $ii) {
		            			if ($ii) {
		            				$prev[] = '(c.text_id = '.$ii['text_id'].' and c.prev_id = ' . ($ii['prev_id'] + 1) . ')';
		            				$prev_index[ $ii['text_id'] . '_' . ($ii['prev_id']+1) ] = $jj;
		            			}
		            		}
	            		}
	            		else {
	            			$prev[] = 'c.id = 0';
	            		}

	            		$query.= ' and ('.implode(' or ', $prev).')';
	            	}

					$chain[$j] = Yii::app()->db->createCommand( $query )->queryAll();

					if (count($chain[$j]) == 0) break;
	            }

	            if (count($chain[count($words)-1])) {

	            	$prev = array();
	            	$next = array();
	            	$start = array();

	            	foreach ($chain[1] as $j => $i) {
	            		$l = $i['text_id'] . '_' . ($i['prev_id']-1) . '_' . $i['word1'];
	            		$next[ $l ] = $i['form1'];

		            	$start[ $i['text_id'] . '_' . ($i['prev_id']-1) ] = $i['text_id'] . '_' . ($i['prev_id']-1);
	            	}

	            	foreach ($chain as $k => $c) {
	            		$prev = $next;
	            		$next = array();

	            		foreach ($c as $j => $i) {
		            		$a = $i['text_id'] . '_' . ($i['prev_id']-1) . '_' . $i['word1'];
		            		$b = $i['text_id'] . '_' . $i['prev_id'] . '_' . $i['word2'];
		            		$next[ $b ] = $prev[$a] .' ' . $i['form2'];

		            		$start[ $i['text_id'] . '_' . $i['prev_id'] ] = $start[ $i['text_id'] . '_' . ($i['prev_id']-1) ];
	            		}
	            	}

	            	$next_hash = array();

	            	$lines = array();

	            	foreach ($next as $l => $j) {
	            		list($text_id, $prev_id, $word_id) = explode('_', $l, 3);

	            		$k = md5( $text_id . '_' . $prev_id );

	            		if (!$next_hash[$k]) {

	            			list($tmp_id, $start_id) = explode('_', $start[$text_id . '_' . $prev_id]);

	            			$q = 0; $rev = '';
	            			while ($q < 5) {
	            				$wc = WordxChain::model()->findByAttributes(array('text_id' => $text_id, 'prev_id' => $start_id));
	            				$ww = WordxForm::model()->findByPk( $wc->word1 );

	            				$q++;
	            				$start_id--;

	            				if ($ww->word) {
	            					$rev = $ww->word . ' '. $rev;
	            				}
	            			}

	            			$line = $rev;

	            			$next_hash[$k] = 1;
	            			$line.= '<b>' . $j . '</b>';

	            			$q = 0;
	            			while ($q < 5) {
	            				$wc = WordxChain::model()->findByAttributes(array('text_id' => $text_id, 'prev_id' => $prev_id+1));
	            				$ww = WordxForm::model()->findByPk( $wc->word2 );

	            				$q++;
	            				$prev_id++;

	            				if ($ww->word) {
	            					$line.= ' ' . $ww->word;
	            				}
	            			}

	            			$lines[] = $line;
	            		}
	            	}

	            	$out.= implode("\n<br>", $lines);

	            	$yandex = false;

	            }
	            else {
		            $out.= 'Ничего не найдено по этой фразе.';

		    		$ws = WordxSearch::model()->findByAttributes(array('phrase' => $this->text));
		    		if ($ws) {
		    			$ws->qty = $ws->qty + 1;
		    		}
		    		else {
		    			$ws = new WordxSearch;
		    			$ws->phrase = $this->text;
		    			$ws->qty = 1;
		    		}
		    		$ws->save();

	            }
            }

            else {
            	$out.= 'У нас в базе нет слова ' . $nowords;

	    		$ws = WordxSearch::model()->findByAttributes(array('phrase' => $this->text));
	    		if ($ws) {
	    			$ws->qty = $ws->qty + 1;
	    		}
	    		else {
	    			$ws = new WordxSearch;
	    			$ws->phrase = $this->text;
	    			$ws->qty = 1;
	    		}
	    		$ws->save();

            }

/*            if ($yandex) {
		        $YAXML = new YandexXML();

		        $YAXML->addProxy(YandexProxy::create(
		            Yii::app()->params['yandexXML']['proxy_address'],
		            Yii::app()->params['yandexXML']['proxy_auth'],
		            Yii::app()->params['yandexXML']['user'],
		            Yii::app()->params['yandexXML']['key']
		        ));

		        $YAXML->switchProxy();

		        $xml = $YAXML->getXML('"' . $this->text . '"', NULL, 100, 0);

	            if ($results = YandexXMLResult::parse($xml)) {
	                foreach($results->list as $doc){
	                    $out.= 'Найдено в Яндексе: ' . implode('<br>', $doc->passages);
	                }
	            }

            }*/

            /*$out .= '<table class="table table-bordered">';
            foreach ($words as $j => $i) {

            	$out.= '<tr>';
	            	$out.= '<td style="vertical-align: top;">'.(is_array($w_prev[$j]) ? implode('<br>', $w_prev[$j]) : '').'</td>';
	            	$out.= '<td style="vertical-align: top;"><a name="'.md5($i).'">'.$i.'</a></td>';
	            	$out.= '<td style="vertical-align: top;">'.(is_array($w_next[$j]) ? implode('<br>', $w_next[$j]) : '').'</td>';
            	$out.= '<tr>';

            }
            $out .= '</table>';*/

            return $out;
    	}

    }

    /*public function searchRev1() {

    	if ($this->text) {

    		$ws = WordxSearch::model()->findByAttributes(array('phrase' => $this->text));
    		if ($ws) {
    			$ws->qty = $ws->qty + 1;
    		}
    		else {
    			$ws = new WordxSearch;
    			$ws->phrase = $this->text;
    			$ws->qty = 1;
    		}
    		$ws->save();

            $text = str_replace(array(',', '+', ':', ';'), ' ', $this->text);

            $mystem = new Mystem( $text );
            $mystem->checkZ();

            $result = array();
            $words = array();
            $words_g = array();

            $w_prev = $w_next = array();

            $prev = '';

            $text_id = time();

            $n = 0;

            foreach ($mystem->result as $chunk) {

            	foreach (WordxForm::model()->findAllByAttributes(array('word' => $chunk->text)) as $w) {
            		$out .= $w->word . ' ' . $w->gram->gram . '<br>';
            	}

            	$word = WordxForm::model()->findByAttributes( array('word' => $chunk->text) );

            	$words[$n] = $word->word;

            	$w_prev[$n] = array();

            	foreach (WordxChain::model()->findAllByAttributes(array('word2' => $word->id)) as $wc) {
            		$z = WordxForm::model()->findByPk( $wc->word1 );
            		$w_prev[$n][] = $z->word;
            	}

            	$w_next[$n] = array();

            	foreach (WordxChain::model()->findAllByAttributes(array('word1' => $word->id)) as $wc) {
            		$z = WordxForm::model()->findByPk( $wc->word2 );
            		$w_next[$n][] = $z->word;
            	}

            	$n++;

            }

            $out .= '<table class="table table-bordered">';
            foreach ($words as $j => $i) {

            	$out.= '<tr>';
	            	$out.= '<td style="vertical-align: top;">'.(is_array($w_prev[$j]) ? implode('<br>', $w_prev[$j]) : '').'</td>';
	            	$out.= '<td style="vertical-align: top;"><a name="'.md5($i).'">'.$i.'</a></td>';
	            	$out.= '<td style="vertical-align: top;">'.(is_array($w_next[$j]) ? implode('<br>', $w_next[$j]) : '').'</td>';
            	$out.= '<tr>';

            }
            $out .= '</table>';

            return $out;
    	}

    }*/

    private function saveWordx( $text ) {

    	if ($text) {
            $text = str_replace(array(',', '+', ':', ';'), ' ', $text);

            $mystem = new Mystem( $text );
            $mystem->checkZ();

            $result = array();
            $words = array();
            $words_g = array();

            $w_prev = $w_next = array();

            $prev = '';
            $prev_word = false;
            $this_word = array();

            $prev_wform = false;
            $this_wform = array();

            $wtxt = new WordxText;
            $wtxt->date = date('Y-m-d H:i:s');
            $wtxt->save();

            $text_id = $wtxt->id;

            $n = 0;

            $m = count($mystem->result);

            /*print 'Total mystem result: ' . $m . "\n";*/

            foreach ($mystem->result as $chunk) {

            	/*print "Try " . $n . "/". $m ." " . $chunk->text . " (".$chunk->word.", ".$chunk->gr.") \n";*/

            	$prev_word = $this_word;
	            $this_word = array();

	            $prev_wform = $this_wform;
	            $this_wform = array();

            	if (is_array($chunk->analysis)) {
            		foreach ($chunk->analysis as $el) {
            			$word = WordxWord::model()->findByAttributes(array('word' => $el->lex));

            			if (!$word) {
            				$transaction = Yii::app()->db->beginTransaction();

            				$word = new WordxWord;
            				$word->word = $el->lex;
            				$word->save();

            				$transaction->commit();
            			}

            			$gram_all = array();
            			list($gr_a, $gr_b) = explode('=', $el->gr, 2);

            			if (substr($gr_b, 0, 1) == '(' and substr($gr_b, -1) ==')') {
            				$gr_c = explode('|', substr($gr_b, 1, -1));

            				foreach ($gr_c as $gr_d) {
            					$gram_all[] = $gr_a . '=' . $gr_d;
            				}
            			}
            			else {
            				$gram_all[] = $gr_a . '=' . $gr_b;
            			}

            			foreach ($gram_all as $q) {
	            			$gram = WordxGram::model()->findByAttributes(array('gram' => $q));

	            			if (!$gram) {
	            				$transaction = Yii::app()->db->beginTransaction();

	            				$gram = new WordxGram;
	            				$gram->gram = $q;
	            				$gram->save();

	            				$transaction->commit();
	            			}

	            			$wform = WordxForm::model()->findByAttributes(array('word_id' => $word->id, 'gram_id' => $gram->id));

	            			if (!$wform) {
	            				$transaction = Yii::app()->db->beginTransaction();

	            				$wform = new WordxForm;
	            				$wform->word_id = $word->id;
	            				$wform->gram_id = $gram->id;
	            				$wform->word = $chunk->text;
	            				$wform->save();

	            				$transaction->commit();
	            			}

	            			$this_wform[] = $wform;
            			}

            			$gram = WordxGram::model()->findByAttributes(array('gram' => $gram_all[0]));
	            		$wform = WordxForm::model()->findByAttributes(array('word_id' => $word->id, 'gram_id' => $gram->id));

            			$this_word[] = $word;
            		}
            	}

            	foreach ($prev_word as $j) {
            		foreach ($this_word as $i) {
        				$transaction = Yii::app()->db->beginTransaction();

            			if ($pos = WordxPos::model()->findByAttributes(array('first_id' => $j->id, 'second_id' => $i->id))) {
            				$pos->total++;
            				$pos->save();
            			}
            			else {
            				$pos = new WordxPos;
            				$pos->first_id = $j->id;
            				$pos->second_id = $i->id;
            				$pos->total = 1;
            				$pos->save();
            			}

        				$transaction->commit();
            		}
            	}

            	foreach ($prev_wform as $j) {
            		foreach ($this_wform as $i) {
        				$transaction = Yii::app()->db->beginTransaction();

        				$pos = new WordxChain;
        				$pos->text_id = $text_id;
        				$pos->prev_id = $n;
        				$pos->word1 = $j->id;
        				$pos->gram1 = $j->gram_id;
        				$pos->word2 = $i->id;
        				$pos->gram2 = $i->gram_id;
        				$pos->save();

        				$transaction->commit();
            		}
            	}

            	$n++;

            	$result[ $chunk->word ]++;
            	$grammar[ $chunk->word ] = $chunk->gr;

                $words[] = mb_strtolower( $chunk->word, 'utf-8' );
                $words_n[] = mb_strtolower( $chunk->text, 'utf-8' );
                $words_g[] = $chunk->gr;

                if (count($words) > 1) {
                	$w = implode(' ', array_slice($words, -2));

	            	$result[ $w ]++;

	            	if (!isset($grammar[ $w ])) {
	            		$grammar[ $w ] = implode('+', array_slice($words_g, -2));
	            	}
                }

                if (count($words) > 2) {
                	$w = implode(' ', array_slice($words, -3));

	            	$result[ $w ]++;

	            	if (!isset($grammar[ $w ])) {
	            		$grammar[ $w ] = implode('+', array_slice($words_g, -3));
	            	}
                }
            }

            /*print "done\n";*/
    	}

    }


    public function theory() {

    	if ($this->text) {

            $text = str_replace(array(',', '+', ':', ';'), ' ', $this->text);

            $mystem = new Mystem( $text );
            $mystem->checkZ();

            $words = array();
            $words_g = array();

            $yandex = true;

            $nowords = false;

            foreach ($mystem->result as $j => $chunk) {
            	/*$chunk->(text|word|gr)*/

            	$words[$j] = array();

            	foreach ($chunk->analysis as $al) {

        			$gram_all = array();
        			list($gr_a, $gr_b) = explode('=', $al->gr, 2);

        			if (substr($gr_b, 0, 1) == '(' and substr($gr_b, -1) ==')') {
        				$gr_c = explode('|', substr($gr_b, 1, -1));

        				foreach ($gr_c as $gr_d) {
        					$gram_all[] = $gr_a . '=' . $gr_d;
        				}
        			}
        			else {
        				$gram_all[] = $gr_a . '=' . $gr_b;
        			}

        			foreach ($gram_all as $q) {
	            		foreach (WordxGram::model()->findAllByAttributes(array('gram' => $q)) as $w) {
	            			if (!in_array($w->id, $words[$j])) {
	            				$words[$j][] = $w->id;
	            			}
            			}
            		}

            	}

            	if (count($words[$j]) == 0) {
            		$nowords = $chunk->gr;
            	}
            }

            if (!$nowords) {
            	$chain = array();

	            for ($j = 1; $j < count($words); $j++) {

	            	$chain[$j] = array();

	            	$w1 = $words[$j-1];
	            	$w2 = $words[$j];

	            	$query = 'SELECT c.*, f1.word as form1, f2.word as form2 FROM `tbl_wordx_chain` as c
	            		LEFT JOIN `tbl_wordx_form` as f1 on (c.word1 = f1.id)
	            		LEFT JOIN `tbl_wordx_form` as f2 on (c.word2 = f2.id)
	            		WHERE
	            			c.gram1 ' . (count($w1) == 1 ? ' = ' . $w1[0] : ' in (' . implode(', ', $w1) . ')' ) . ' and 
	            			c.gram2 ' . (count($w2) == 1 ? ' = ' . $w2[0] : ' in ('.implode(', ', $w2).')')
	            	;

	            	if ($j > 1) {
	            		$prev = array();
	            		$prev_index = array();

	            		if (count($chain[$j-1])) {
		            		foreach ($chain[$j-1] as $jj => $ii) {
		            			if ($ii) {
		            				$prev[] = '(c.text_id = '.$ii['text_id'].' and c.prev_id = ' . ($ii['prev_id'] + 1) . ')';
		            				$prev_index[ $ii['text_id'] . '_' . ($ii['prev_id']+1) ] = $jj;
		            			}
		            		}
	            		}
	            		else {
	            			$prev[] = 'c.id = 0';
	            		}

	            		$query.= ' and ('.implode(' or ', $prev).')';

	            	}

					$chain[$j] = Yii::app()->db->createCommand( $query )->queryAll();

					if (count($chain[$j]) == 0) break;
	            }

	            if (count($chain[count($words)-1])) {

	            	$prev = array();
	            	$next = array();
	            	$start = array();

	            	foreach ($chain[1] as $j => $i) {
	            		$l = $i['text_id'] . '_' . ($i['prev_id']-1) . '_' . $i['word1'];
	            		$next[ $l ] = $i['form1'];

		            	$start[ $i['text_id'] . '_' . ($i['prev_id']-1) ] = $i['text_id'] . '_' . ($i['prev_id']-1);
	            	}

	            	foreach ($chain as $k => $c) {
	            		$prev = $next;
	            		$next = array();

	            		foreach ($c as $j => $i) {
		            		$a = $i['text_id'] . '_' . ($i['prev_id']-1) . '_' . $i['word1'];
		            		$b = $i['text_id'] . '_' . $i['prev_id'] . '_' . $i['word2'];
		            		$next[ $b ] = $prev[$a] .' ' . $i['form2'];

		            		$start[ $i['text_id'] . '_' . $i['prev_id'] ] = $start[ $i['text_id'] . '_' . ($i['prev_id']-1) ];
	            		}
	            	}

	            	$next_hash = array();

	            	$lines = array();

	            	foreach ($next as $l => $j) {
	            		list($text_id, $prev_id, $word_id) = explode('_', $l, 3);

	            		$k = md5( $text_id . '_' . $prev_id );

	            		if (!$next_hash[$k]) {

	            			list($tmp_id, $start_id) = explode('_', $start[$text_id . '_' . $prev_id]);

	            			$q = 0; $rev = '';
	            			while ($q < 5) {
	            				$wc = WordxChain::model()->findByAttributes(array('text_id' => $text_id, 'prev_id' => $start_id));
	            				$ww = WordxForm::model()->findByPk( $wc->word1 );

	            				$q++;
	            				$start_id--;

	            				if ($ww->word) {
	            					$rev = $ww->word . ' '. $rev;
	            				}
	            			}

	            			$line = $rev;

	            			$next_hash[$k] = 1;
	            			$line.= '<b>' . $j . '</b>';

	            			$q = 0;
	            			while ($q < 5) {
	            				$wc = WordxChain::model()->findByAttributes(array('text_id' => $text_id, 'prev_id' => $prev_id+1));
	            				$ww = WordxForm::model()->findByPk( $wc->word2 );

	            				$q++;
	            				$prev_id++;

	            				if ($ww->word) {
	            					$line.= ' ' . $ww->word;
	            				}
	            			}

	            			$lines[] = $line;
	            		}
	            	}

	            	$out.= implode('<br>', $lines);

	            	$yandex = false;

	            }
	            else {
		            $out.= 'Ничего не найдено по этой фразе.';
	            }
            }

            else {
            	$out.= 'У нас в базе нет граммемы ' . $nowords;
            }

            return $out;
    	}

    }

}


