<?php

class TplStaff extends CActiveRecord {

	private $_model = null;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{tpl_staff}}';
    }

    public function rules() {
    	return array(
            array('id, name, staff_id, timer, period, multiple, text', 'safe'),
        );
    }

    public function relations() {
        return array(
            'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
        );
    }

    public function attributeLabels(){
        return array(
            'id' => 'ID',
        );
    }

}