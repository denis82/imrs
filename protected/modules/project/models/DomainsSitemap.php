<?php

class DomainsSitemap extends CActiveRecord {

    public function tableName() {
        return '{{domains_sitemap}}';
    }

    public function rules() {
        return array(
            array('domain_id, url', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('id, domain_id, url, robots, text', 'safe', 'on' => 'search'),
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
