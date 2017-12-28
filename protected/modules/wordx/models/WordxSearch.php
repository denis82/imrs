<?php

class WordxSearch extends CActiveRecord {

    public function tableName() {
        return '{{wordx_search}}';
    }

    public function rules() {
        return array(
            array('phrase', 'required'),
            array('id, phrase, qty', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
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
