<?php

class DomainsWhoisParam extends CActiveRecord {

    public function tableName() {
        return '{{domains_whois_param}}';
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

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
		$criteria->compare('domain_id', $this->domain_id);
        $criteria->compare('name', $this->name);
        $criteria->compare('value', $this->value);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
