<?php

class CrawlerLinks extends CActiveRecord {
	
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{crawler_links}}';
    }

    public function rules() {
        return array(
            array('page_id, link_id', 'required'),
            array('id, page_id, link_id, anchor', 'safe', 'on' => 'search'),
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

}