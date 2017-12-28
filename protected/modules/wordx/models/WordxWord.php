<?php

class WordxWord extends CActiveRecord {

    public function tableName() {
        return '{{wordx_word}}';
    }

    public function rules() {
        return array(
            array('word', 'required'),
            array('id, word', 'safe', 'on' => 'search'),
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
