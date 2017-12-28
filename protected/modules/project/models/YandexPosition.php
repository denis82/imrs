<?php

class YandexPosition extends CActiveRecord {

    public function tableName() {
        return '{{yandex_positions}}';
    }

    public function rules() {
        return array(
            array('semantic_id, position, url', 'required'),
            array('semantic_id, position', 'numerical', 'integerOnly' => true),
            array('id, semantic_id, position, title, url', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
			'semantic' => array(self::BELONGS_TO, 'Semantic', 'semantic_id'),
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
