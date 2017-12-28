<?php

class PagesPhrase extends CActiveRecord {

    public function tableName() {
        return '{{pages_phrase}}';
    }

    public function rules() {
        return array(
            array('page_id, phrase, qty', 'required'),
            array('page_id', 'numerical', 'integerOnly' => true),
            array('id, page_id, phrase, qty', 'safe', 'on' => 'search'),
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

}
