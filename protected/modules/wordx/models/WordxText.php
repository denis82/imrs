<?php

class WordxText extends CActiveRecord {

    public function tableName() {
        return '{{wordx_text}}';
    }

    public function rules() {
        return array(
            array('date', 'required'),
            array('id, date', 'safe', 'on' => 'search'),
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
