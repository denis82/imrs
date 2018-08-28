<?php

class ProjectsCabinetWords extends CActiveRecord {

    public function tableName() {
        return '{{projects_cabinet_words}}';
    }

        public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function rules() {
        return array(
        );
    }


    public function attributeLabels() {
        return array(
            //'id' => 'ID',
        );
    }

}
