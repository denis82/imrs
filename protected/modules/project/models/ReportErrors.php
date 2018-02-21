<?php

class ReportErrors extends CActiveRecord {
    
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{report_errors}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('path , domain_id', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('id, domain_id', 'safe'),
        );
    }

//     /**
//      * @return array relational rules.
//      */
//     public function relations() {
//         // NOTE: you may need to adjust the relation name and the related
//         // class name for the relations automatically generated below.
//         return array(
// 			'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
//         );
//     }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
			'id' => 'ID',
			'domain_id' => 'Идентификатор домена',
			'path' => 'Путь',
			'date' => 'Дата проверки',
			'origin_text' => 'Исходный текст',
			'current_text' => 'Актуальный текст',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return YSearch the static model class
     */
//     public static function model($className = __CLASS__) {
//         return parent::model($className);
//     }

}