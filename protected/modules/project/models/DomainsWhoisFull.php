<?php

class DomainsWhoisFull extends CActiveRecord {

    public function tableName() {
        return '{{domains_whois_full}}';
    }

    public function rules() {
        return array(
            array('domain_id, text', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('id, domain_id, text', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
            'items' => array(self::HAS_MANY, 'DomainsWhois', 'full_id', 'order' => 'id asc'),
            'params' => array(self::HAS_MANY, 'DomainsWhoisParam', 'full_id', 'order' => 'id asc'),
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

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
