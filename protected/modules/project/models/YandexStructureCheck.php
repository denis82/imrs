<?php

class YandexStructureCheck extends CActiveRecord {

    public function tableName() {
        return '{{yandex_structure_check}}';
    }

    public function rules() {
        return array(
            array('project_id, domain_id', 'required'),
            array('project_id, domain_id', 'numerical', 'integerOnly' => true),
            array('id, project_id, domain_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
			'urls' => array(self::HAS_MANY, 'YandexStructure', 'check_id'),
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
