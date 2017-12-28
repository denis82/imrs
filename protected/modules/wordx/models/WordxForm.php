<?php

class WordxForm extends CActiveRecord {

    public function tableName() {
        return '{{wordx_form}}';
    }

    public function rules() {
        return array(
            array('word_id, gram_id, word', 'required'),
            array('id, word_id, gram_id, word', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
			'gram' => array(self::BELONGS_TO, 'WordxGram', 'gram_id'),
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
