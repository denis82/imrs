<?php

class Changelog extends CActiveRecord {
    const T_CREATE = 0;
    const T_EDIT = 1;
    const T_DELETE = 2;

    public function tableName() {
        return '{{changelog}}';
    }

    public function rules() {
        return array(
            array('module_id, type', 'required'),
            array('module_id, type', 'numerical', 'integerOnly' => true),
            array('id, model, model_id, type, params, content', 'safe', 'on' => 'search'),
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

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('model', $this->model);
        $criteria->compare('model_id', $this->model_id);
        $criteria->compare('type', $this->type);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    protected function beforeSave() {
        if (parent::beforeSave() === false) {
            return false;
        }

        if (is_array($this->params)) {
            $this->params = serialize($this->params);
        }

        return true;
    }

    protected function afterSave() {
        parent::afterSave();

        $data = @unserialize($this->params);

        if ($data !== false) {
            $this->params = $data;
        }
    }

    protected function afterFind() {
        $data = @unserialize($this->params);

        if ($data !== false) {
            $this->params = $data;
        }

        return parent::afterFind();
    }

}
