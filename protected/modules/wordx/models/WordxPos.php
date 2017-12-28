<?php

class WordxPos extends CActiveRecord {

    public function tableName() {
        return '{{wordx_pos}}';
    }

    public function rules() {
        return array(
            array('first_id, second_id', 'required'),
            array('id, first_id, second_id, total', 'safe', 'on' => 'search'),
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
