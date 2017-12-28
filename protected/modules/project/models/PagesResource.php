<?php

class PagesResource extends CActiveRecord {
    const T_IMAGE = 1;
    const T_CSS = 2;
    const T_SCRIPT = 3;
    const T_FORM = 4;

    public function tableName() {
        return '{{pages_resources}}';
    }

    public function rules() {
        return array(
            array('page_id, type', 'required'),
            array('page_id, domain_resource_id, type', 'numerical', 'integerOnly' => true),
            array('id, page_id, domain_resource_id, type, html, url', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'domain_resource_id' => array(self::BELONGS_TO, 'DomainsResource', 'domain_resource_id'),
            'page' => array(self::BELONGS_TO, 'Page', 'page_id'),
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
        $criteria->compare('page_id', $this->page_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function hash() {
    	return md5( $this->html );
    }

    public function url() {
    	if (!$this->url) {
    		return '';
    	}

    	$a = parse_url($this->url);

    	if ($a['host']) {
    		return $this->url;
    	}

    	$b = parse_url($this->page->sitemap->url);

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
