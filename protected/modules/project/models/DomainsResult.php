<?php

class DomainsResult extends CActiveRecord {

    public function tableName() {
        return '{{domains_result}}';
    }

    public function rules() {
        return array(
            array('domain_id, name', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('id, domain_id, name, value', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
			'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
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
