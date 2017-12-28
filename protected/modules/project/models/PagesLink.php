<?php

class PagesLink extends CActiveRecord {

    public function tableName() {
        return '{{pages_links}}';
    }

    public function rules() {
        return array(
            array('page_id', 'required'),
            array('page_id', 'numerical', 'integerOnly' => true),
            array('id, page_id, html, href, anchor', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'page' => array(self::BELONGS_TO, 'Page', 'page_id'),
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
        $criteria->compare('page_id', $this->page_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
