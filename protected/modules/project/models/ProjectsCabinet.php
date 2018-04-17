<?php

class ProjectsCabinet extends CActiveRecord {

    public function tableName() {
        return '{{projects_cabinet}}';
    }
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
// 
//     public function rules() {
//         return array(
//         );
//     }
// 
    public function relations() {
        return array(
			'projects_cabinet_words' => array(self::HAS_MANY, 'ProjectsCabinetWords', array('site_id' => 'id')),
        );
    }
// 
//     public function attributeLabels() {
//         return array(
//             //'id' => 'ID',
//         );
//     }

}
