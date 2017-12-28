<?php

class Semantic extends CActiveRecord {

    public function tableName() {
        return '{{semantic}}';
    }

    public function rules() {
        return array(
            array('project_id, phrase', 'required'),
            array('project_id, phrase', 'numerical', 'integerOnly' => true),
            array('id, project_id, phrase, created_date', 'safe', 'on' => 'search'),
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

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeSave(){
        if ($this->isNewRecord) {
            $this->created_date = new CDbExpression('NOW()');
        }

        return parent::beforeSave();
    }

    public function stat() {
        $stat = Wordstat::model()->findByAttributes(array(
            'word' => $this->phrase,
        ));

        if ($stat) {
            return $stat->stat;
        }

        return false;
    }

}
