<?php

class Mention extends CActiveRecord {

    public function tableName() {
        return '{{mention}}';
    }

    public function rules() {
        return array(
            array('check_id, title, url', 'required'),
            array('check_id', 'numerical', 'integerOnly' => true),
            array('id, check_id, url, title, text', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'check' => array(self::BELONGS_TO, 'MentionCheck', 'check_id'),
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
