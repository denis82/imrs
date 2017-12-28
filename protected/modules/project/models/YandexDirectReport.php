<?php

class YandexDirectReport extends CActiveRecord {

    public function tableName() {
        return '{{yandex_direct_report}}';
    }

    public function rules() {
        return array(
            array('report_id, project_id', 'required'),
            array('report_id, project_id', 'numerical', 'integerOnly' => true),
            array('report_id, project_id', 'safe'),
            array('id, report_id, project_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
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
		$criteria->compare('report_id', $this->report_id);
        $criteria->compare('project_id', $this->project_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
