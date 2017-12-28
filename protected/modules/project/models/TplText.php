<?php

class TplText extends CActiveRecord {

	private $_model = null;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{tpl_text}}';
    }

    public function rules() {
    	return array(
            array('id, name, text', 'safe'),
        );
    }

    public function relations() {
        return array(
        );
    }

    public function attributeLabels(){
        return array(
            'id' => 'ID',
        );
    }

}