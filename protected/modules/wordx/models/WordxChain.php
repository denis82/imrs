<?php

class WordxChain extends CActiveRecord {

    public function tableName() {
        return '{{wordx_chain}}';
    }

    public function rules() {
        return array(
            array('word1, word2', 'required'),
            array('id, text_id, prev_id, word1, word2, gram1, gram2', 'safe', 'on' => 'search'),
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
