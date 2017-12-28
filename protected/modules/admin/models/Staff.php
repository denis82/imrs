<?php

class Staff extends CActiveRecord {

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{staff}}';
	}

	public function rules() {
		return array(
			array('name', 'required'),
			array('name, price', 'safe'),
		);
	}

	public function relations() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'name' => 'Наименование',
			'price' => 'Стоимость часа',
		);
	}
	
}