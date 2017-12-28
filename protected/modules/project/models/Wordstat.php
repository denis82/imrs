<?php

class Wordstat extends CActiveRecord {

    public function tableName() {
        return '{{wordstat}}';
    }

    public function rules() {
        return array(
            array('word, stat', 'required'),
            array('region_id, stat, strict', 'numerical', 'integerOnly' => true),
            array('id, word, region_id, stat, strict', 'safe', 'on' => 'search'),
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
    
    public static function saveResult( $params ) {
        $model = self::model()->findByAttributes(array(
            'word' => $params['word'],
            'region_id' => $params['region_id'],
        ));

        if (!$model or !$model->id) {
            $model = new self;
            $model->word = $params['word'];
            $model->region_id = $params['region_id'];
        }

        $model->stat = (int) $params['stat'];
        $model->strict = (int) $params['strict'];
        $model->save();

        return $model;
    }

}
