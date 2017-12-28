<?php

class ProjectsOrgPhone extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{projects_org_phone}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('org_id', 'required'),
            array('org_id', 'numerical', 'integerOnly' => true),
            array('id, org_id, country, code, number, extra, name, type', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
			'id' => 'ID',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return YSearch the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
