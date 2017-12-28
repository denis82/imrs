<?php

class Shingle extends CActiveRecord {

    public function tableName() {
        return '{{shingles}}';
    }

    public function rules() {
        return array(
            array('page_id, text', 'required'),
            array('page_id, checked, oncheck, result', 'numerical', 'integerOnly' => true),
            array('id, page_id, text, checked, oncheck, result', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'page' => array(self::BELONGS_TO, 'Page', 'page_id'),
            'result' => array(self::HAS_MANY, 'ShinglesResult', 'shingle_id', 'order' => 'id asc'),
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
