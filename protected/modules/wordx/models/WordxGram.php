<?php

class WordxGram extends CActiveRecord {

    public function tableName() {
        return '{{wordx_gram}}';
    }

    public function rules() {
        return array(
            array('gram', 'required'),
            array('id, gram, hash', 'safe', 'on' => 'search'),
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
