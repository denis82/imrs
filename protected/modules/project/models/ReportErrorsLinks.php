<?php

class ReportErrorsLinks extends CActiveRecord {

    public $email;
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
        /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{report_errors_links}}';
    }

        /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('domain_id', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('domain_id, robots , sitemap,robots_status,sitemap_status', 'safe'),
        );
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
			'domain_id' => 'Идентификатор домена',
			'path' => 'Путь',
			'robots' => 'Файл роботс',
			'sitemap' => 'Файл сайтмап',
        );
    }
}