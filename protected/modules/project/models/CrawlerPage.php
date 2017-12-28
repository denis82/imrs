<?php

class CrawlerPage extends CActiveRecord {
	
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{crawler_page}}';
    }

    public function rules() {
        return array(
            array('url', 'required'),
            array('id, domain_id, url, url_hash, check, code, page_crc32, created_date, updated_date', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
        );
    }

    public function attributeLabels(){
        return array(
            'id' => 'ID',
        );
    }

    public function beforeSave() {
        if ($this->isNewRecord) {
        	$this->created_date = new CDbExpression('NOW()');
        }

        $this->url_hash = md5( $this->url );
    	$this->updated_date = new CDbExpression('NOW()');

        return parent::beforeSave();
    }

    public function findByUrl( $url ) {
    	return $this->findByAttributes(array('url_hash' => self::urlHash($url)));
    }

    public static function urlHash( $url ) {
    	return md5( $url );
    }
    
}