<?php

class Page extends CActiveRecord {
	private $base_href = null;

    public function tableName() {
        return '{{pages}}';
    }

    public function rules() {
        return array(
            array('domain_id, sitemap_id', 'required'),
            array('domain_id, sitemap_id, uniq', 'numerical', 'integerOnly' => true),
            array('id, domain_id, sitemap_id, text, uniq', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
            'sitemap' => array(self::BELONGS_TO, 'Sitemap', 'sitemap_id'),
            'shingles' => array(self::HAS_MANY, 'Shingle', 'page_id'),
            'params' => array(self::HAS_MANY, 'PagesParam', 'page_id'),

            'resources' => array(self::HAS_MANY, 'PagesResource', 'page_id'),
            'css' => array(self::HAS_MANY, 'PagesResource', 'page_id', 'condition' => 'type = ' . PagesResource::T_CSS),
            'js' => array(self::HAS_MANY, 'PagesResource', 'page_id', 'condition' => 'type = ' . PagesResource::T_SCRIPT),
            'form' => array(self::HAS_MANY, 'PagesResource', 'page_id', 'condition' => 'type = ' . PagesResource::T_FORM),

            'phrases' => array(self::HAS_MANY, 'PagesPhrase', 'page_id'),

            'spell' => array(self::HAS_ONE, 'PagesSpell', 'page_id', 'order' => 'id desc'),
        );
    }

    public function attributeLabels() {
        return array(
			'id' => 'ID',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('domain_id', $this->domain_id);
        $criteria->compare('sitemap_id', $this->sitemap_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getHead() {
        preg_match('/<head>(.*)<\/head>/si', $this->text, $m);

        $text = $m[1];

        $remove = array(
            '/<script(.+?)<\/script>/si',
            '/<style(.+?)<\/style>/si',
            '/<!--(.+?)-->/si',
        );

        foreach ($remove as $regex) {
            $text = preg_replace($regex, '', $text);
        }

        return $text;
    }

    public function getBody() {
        preg_match('/<body(.+?)>(.*)<\/body>/si', $this->text, $m);

        $text = $m[2];

        $remove = array(
            '/<script(.+?)<\/script>/si',
            '/<style(.+?)<\/style>/si',
            '/<!--(.+?)-->/si',
        );

        foreach ($remove as $regex) {
            $text = preg_replace($regex, '', $text);
        }

        return $text;
    }

    public function getText( $multiline = true ) {
        $text = $this->getBody();

        $remove = array(
            '/<a (.+?)<\/a>/si',
            '/<br(.+?)>/si',
            '/&nbsp;/si',
        );

        foreach ($remove as $regex) {
            $text = preg_replace($regex, ' ', $text);
        }

        $text = strip_tags($text);

        $lines = explode("\n", $text);

        $text = '';

        foreach ($lines as $l) {
            $words = explode(' ', trim($l));
            $line = '';

            foreach ($words as $w) {
                $w = trim($w);

                if (strlen($w)) {
                    $line.= $w . ' ';
                }
            }

            if (strlen($line)) {
                $text.= trim($line) . ($multiline ? PHP_EOL : ' ');
            }
        }

        return trim( htmlspecialchars_decode($text) );
    }

    public function saveAll() {
    	/* первым сохраняем параметры, в том числе base href */
        $this->saveParams();

        $this->saveResources();
        $this->saveLinks();
        $this->saveImages();
    }

    public function saveLinks() {
        $text = $this->getBody();

        foreach (PagesLink::model()->findAllByAttributes(array('page_id' => $this->id)) as $link) {
            $link->delete();
        }

        $matches = array();

        preg_match_all('/<a (.+?)<\/a>/si', $text, $matches);

        foreach ($matches[0] as $a) {
            $anchor = $href = '';

            if (preg_match('/href="(.*?)"/si', $a, $m)) {
                $href = $m[1];
            }
            elseif (preg_match('/href=\'(.*?)\'/si', $a, $m)) {
                $href = $m[1];
            }
            elseif (preg_match('/href=([^ \f\n\r\t\v>]+)/si', $a, $m)) {
                $href = $m[1];

                if ($href[0] == '"' or $href[0] == "'") {
                    $href = substr($href, 1, -1);
                }
            }

            if (preg_match('/>(.*)<\/a>/si', $a, $m)) {
                $anchor = $m[1];
            }

            $link = new PagesLink;
            $link->page_id = $this->id;
            $link->html = $a;
            $link->href = $href;
            $link->anchor = $anchor;
            $link->save();
        }

        return true;
    }

    public function saveImages() {
        $text = $this->getBody();

        foreach (PagesImage::model()->findAllByAttributes(array('page_id' => $this->id)) as $link) {
            $link->delete();
        }

        $matches = array();

        preg_match_all('/<img (.+?)>/si', $text, $matches);

        foreach ($matches[0] as $a) {
            $href = '';

            if (preg_match('/src="(.*?)"/si', $a, $m)) {
                $href = $m[1];
            }
            elseif (preg_match('/src=\'(.*?)\'/si', $a, $m)) {
                $href = $m[1];
            }
            elseif (preg_match('/src=([^ \f\n\r\t\v>]+)/si', $a, $m)) {
                $href = substr($m[1], 1, -1);
            }

            $res_id = DomainsResource::model()->createIfNotExists(array(
            	'domain_id' => $this->domain_id,
            	'type' => DomainsResource::T_IMAGE,
            	'html' => $a,
            	'url' => $this->resourceUrl( $href ),
            ));

            $link = new PagesImage;
            $link->page_id = $this->id;
            $link->html = $a;
            $link->src = $href;
            $link->save();

            $link = new PagesResource;
            $link->page_id = $this->id;
            $link->domain_resource_id = $res_id;
            $link->type = PagesResource::T_IMAGE;
            $link->save();
        }

        return true;
    }

    public function saveResources() {
        $text = $this->text;

        foreach (PagesResource::model()->findAllByAttributes(array('page_id' => $this->id)) as $link) {
            $link->delete();
        }
      
        $matches = array();
        preg_match_all('/<link (.+?)>/si', $text, $matches);
        foreach ($matches[0] as $a) {
            if (preg_match('/type=(\"|\'|)text\/css/si', $a)) {
                $href = '';

                if (preg_match('/href="(.*?)"/si', $a, $m)) {
                    $href = $m[1];
                }
                elseif (preg_match('/href=\'(.*?)\'/si', $a, $m)) {
                    $href = $m[1];
                }
                elseif (preg_match('/href=([^ \f\n\r\t\v>]+)/si', $a, $m)) {
                    $href = substr($m[1], 1, -1);
                }

                $res_id = DomainsResource::model()->createIfNotExists(array(
                	'domain_id' => $this->domain_id,
                	'type' => DomainsResource::T_CSS,
                	'html' => $a,
                	'url' => $this->resourceUrl( $href ),
                ));
	    
                $link = new PagesResource;
                $link->page_id = $this->id;
                $link->domain_resource_id = $res_id;
                $link->type = PagesResource::T_CSS;
                $link->save();
            }
        }

        $matches = array();
        preg_match_all('/<script(.*?)>(.*?)<\/script>/si', $text, $matches);
        foreach ($matches[0] as $a) {
            $href = '';

            if (preg_match('/src="(.*?)"/si', $a, $m)) {
                $href = $m[1];
            }
            elseif (preg_match('/src=\'(.*?)\'/si', $a, $m)) {
                $href = $m[1];
            }
            elseif (preg_match('/src=([^ \f\n\r\t\v>]+)/si', $a, $m)) {
                $href = substr($m[1], 1, -1);
            }

            $res_id = DomainsResource::model()->createIfNotExists(array(
            	'domain_id' => $this->domain_id,
            	'type' => DomainsResource::T_SCRIPT,
            	'html' => $a,
            	'url' => $href ? $this->resourceUrl( $href ) : '',
            ));

            $link = new PagesResource;
            $link->page_id = $this->id;
            $link->domain_resource_id = $res_id;
            $link->type = PagesResource::T_SCRIPT;
            $link->save();

        }

        $matches = array();
        preg_match_all('/<form(.*?)>(.*?)<\/form>/si', $text, $matches);
        foreach ($matches[0] as $a) {
            $href = '';

            if (preg_match('/action="(.*?)"/si', $a, $m)) {
                $href = $m[1];
            }
            elseif (preg_match('/action=\'(.*?)\'/si', $a, $m)) {
                $href = $m[1];
            }
            elseif (preg_match('/action=([^ \f\n\r\t\v>]+)/si', $a, $m)) {
                $href = substr($m[1], 1, -1);
            }

            $res_id = DomainsResource::model()->createIfNotExists(array(
            	'domain_id' => $this->domain_id,
            	'type' => DomainsResource::T_FORM,
            	'html' => $a,
            	'url' => $this->resourceUrl( $href ),
            ));

            $link = new PagesResource;
            $link->page_id = $this->id;
            $link->domain_resource_id = $res_id;
            $link->type = PagesResource::T_FORM;
            $link->save();
        }

        return true;
    }

    public function saveParams() {
        foreach (PagesParam::model()->findAllByAttributes(array('page_id' => $this->id)) as $link) {
            $link->delete();
        }

        $head = $this->getHead();

        $pattern = '
          ~<\s*meta\s

          # using lookahead to capture type to $1
            (?=[^>]*?
            \b(?:name|property|http-equiv)\s*=\s*
            (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
            ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
          )

          # capture content to $2
          [^>]*?\bcontent\s*=\s*
            (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
            ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
          [^>]*>

          ~ix'; 

        $meta = array();       

        if (preg_match_all($pattern, $head, $matches)) {
            $meta = array_combine($matches[1], $matches[2]);

            foreach ($meta as $j => $i) {
                $link = new PagesParam;
                $link->page_id = $this->id;
                $link->name = 'meta-' . strtolower($j);
                $link->value = $i;
                $link->save();
            }
        }

        if (preg_match_all('/<base(.+?)>/si', $head, $matches)) {
            foreach ($matches[0] as $j) {
            	$href = '';

	            if (preg_match('/href="(.*?)"/si', $j, $m)) {
	                $href = $m[1];
	            }
	            elseif (preg_match('/href=\'(.*?)\'/si', $j, $m)) {
	                $href = $m[1];
	            }
	            elseif (preg_match('/href=([^ \f\n\r\t\v>]+)/si', $j, $m)) {
	                $href = $m[1];

	                if ($href[0] == '"' or $href[0] == "'") {
	                    $href = substr($href, 1, -1);
	                }
	            }

	            if ($href) {
	                $link = new PagesParam;
	                $link->page_id = $this->id;
	                $link->name = 'base-href';
	                $link->value = $href;
	                $link->save();

	                $this->base_href = $href;
	            }
            }
        }


        $text = $this->getBody();

        for ($j = 1; $j <= 6; $j++) {
            $matches = array();
            if (preg_match_all('/<h'.$j.'(.*?)>(.+?)<\/h'.$j.'>/si', $text, $matches)) {
                foreach ($matches[2] as $i) {
                    $link = new PagesParam;
                    $link->page_id = $this->id;
                    $link->name = 'h' . $j;
                    $link->value = $i;
                    $link->save();
                }
            }
        }

        if (preg_match_all('/<iframe(.+?)<\/iframe>/si', $text, $matches)) {

            foreach ($matches[0] as $a) {
                $href = '';

                if (preg_match('/src="(.*?)"/si', $a, $m)) {
                    $href = $m[1];
                }
                elseif (preg_match('/src=\'(.*?)\'/si', $a, $m)) {
                    $href = $m[1];
                }
                elseif (preg_match('/src=([^ \f\n\r\t\v>]+)/si', $a, $m)) {
                    $href = substr($m[1], 1, -1);
                }

                $link = new PagesParam;
                $link->page_id = $this->id;
                $link->name = 'video';
                $link->value = $href;
                $link->save();
            }

        }

        if (preg_match_all('/yaCounter([0-9]+?).reachGoal\((.+?)\)/si', $text, $matches)) {

            foreach ($matches[0] as $a) {
                $link = new PagesParam;
                $link->page_id = $this->id;
                $link->name = 'yandex-goal';
                $link->value = $a;
                $link->save();
            }

        }

        $text = $this->getText();

        if (preg_match_all('/ИНН([ \f\n\r\t\v\:]+)([0-9 ]+)/si', $text, $matches)) {
            foreach ($matches[2] as $a) {
                $link = new PagesParam;
                $link->page_id = $this->id;
                $link->name = 'inn';
                $link->value = str_replace(' ', '', trim($a));
                $link->save();
            }
        }

        if (preg_match_all('/ОГРН([ \f\n\r\t\v\:]+)([0-9 ]+)/si', $text, $matches)) {
            foreach ($matches[2] as $a) {
                $link = new PagesParam;
                $link->page_id = $this->id;
                $link->name = 'ogrn';
                $link->value = str_replace(' ', '', trim($a));
                $link->save();
            }
        }

        if (preg_match_all('/([0-9\(\)\+\-\s]+)/si', $text, $matches)) {
        	$tel = array();

			foreach ($matches[0] as $i) {
				$i = trim($i);
				$i = preg_replace('/\s+/si', '', $i);
				$i = str_replace('-', '', $i);

				if (substr($i, 0, 1) == '(') {
					$i = '+7' . $i;
				}

				while (substr($i, -1) == '(') {
					$i = substr($i, 0, -1);
				}

				$j = str_replace(array('(', ')', '+'), '', $i);

				if (strlen($i) >= 10 and (substr($i, 0, 1) == '+' or strlen($j) == 11)) {
					if (!in_array($i, $tel)) {
		                $link = new PagesParam;
		                $link->page_id = $this->id;
		                $link->name = 'phone';
		                $link->value = $i;
		                $link->save();

		                $tel[] = $i;
					}
				}
			}
		}

        return true;
    }

    public function param($v) {
        return PagesParam::model()->findByAttributes(array(
            'page_id' => $this->id,
            'name' => $v,
        ));
    }

    public function params($v) {
        return PagesParam::model()->findAllByAttributes(array(
            'page_id' => $this->id,
            'name' => $v,
        ));
    }

    public function meta() {
        $criteria = new CDbCriteria();
        $criteria->select = '*';
        $criteria->condition = 'page_id = :page and name like "meta-%"';
        $criteria->params = array( 'page' => $this->id );

        return PagesParam::model()->findAll( $criteria );
    }

    public function hTag() {
        $criteria = new CDbCriteria();
        $criteria->select = '*';
        $criteria->condition = 'page_id = :page and name like "h_"';
        $criteria->params = array( 'page' => $this->id );

        return PagesParam::model()->findAll( $criteria );
    }

    public function linkFiles() {
        $ext = array('doc', 'xls', 'docx', 'xlsx', 'pdf', 'zip', 'rar');

        $query = array();
        $params = array('page' => $this->id);

        foreach ($ext as $i) {
            $params[$i] = '%.' . $i;
            $query[] = 'href like :' . $i;
        }

        $criteria = new CDbCriteria();
        $criteria->select = '*';
        $criteria->condition = 'page_id = :page and (' . implode(' or ', $query) . ')';
        $criteria->params = $params;

        return PagesLink::model()->findAll( $criteria );
    }

    public function linkOut() {
        $this_host = parse_url($this->sitemap->url, PHP_URL_HOST);

        $ext = array('http://', 'https://', '//');

        $query = array();
        $params = array('page' => $this->id);

        foreach ($ext as $j => $i) {
            $params['param' . $j] = $i . '%';
            $query[] = 'href like :param' . $j;
        }

        $criteria = new CDbCriteria();
        $criteria->select = '*';
        $criteria->condition = 'page_id = :page and (' . implode(' or ', $query) . ')';
        $criteria->params = $params;

        $result = array();

        foreach (PagesLink::model()->findAll( $criteria ) as $pl) {
            $host = parse_url($pl->href, PHP_URL_HOST);

            if ($host != $this_host) {
                $result[] = $pl;
            }
        }

        return $result;
    }

    public function stopWords() {
		$words = Yii::app()->db->createCommand()
		    ->select('distinct(v.word)')
		    ->from('tbl_pages_phrase as p left join tbl_vocab_stop as v on (p.phrase = v.word)')
		    ->where('p.page_id = :id and !(v.word is null)', array(':id' => $this->id))
		    ->queryColumn();

		return $words;
    }

    public function resourceUrl( $url ) {

    	if (substr($url, 0, 2) == '//') {
    		return $url;
    	}

    	$a = parse_url($url);

    	if ($a['host']) {
    		return $url;
    	}

    	if (is_null($this->base_href)) {
    		$base_href = PagesParam::model()->findByAttributes(array('page_id' => $this->id, 'name' => 'base-href'));
			$this->base_href = $base_href ? $base_href->value : false;
    	}

    	$base_url = $this->base_href ? $this->base_href : $this->sitemap->url;

    	$b = parse_url($base_url);

    	if ($a['path'][0] != '/') {
    		$c = pathinfo( $b['path'] );

    		if (substr($c['dirname'], -1) != '/') {
    			$c['dirname'] .= '/';
    		}

    		$a['path'] = $c['dirname'] . $a['path'];
    	}

    	$url = '';

    	$url .= $b['scheme'] ? $b['scheme'] . ':' : '';
    	$url .= '//' . $b['host'];
    	$url .= $a['path'];
    	$url .= $b['query'] ? '?' . $b['query'] : '';
    	$url .= $b['fragment'] ? '#' . $b['fragment'] : '';

    	return $url;
    }

}
