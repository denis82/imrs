<?php

class ShinglesResult extends CActiveRecord {

    public function tableName() {
        return '{{shingles_result}}';
    }

    public function rules() {
        return array(
            array('shingle_id, url, title, text', 'required'),
            array('shingle_id, uniq', 'numerical', 'integerOnly' => true),
            array('id, shingle_id, url, title, text, uniq', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'shingle' => array(self::BELONGS_TO, 'Shingle', 'shingle_id'),
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
