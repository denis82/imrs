<?php

class CrawlerPageRank extends CActiveRecord {
	
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{crawler_page_rank}}';
    }

    public function rules() {
        return array(
            array('id, rank1, rank2, rank3', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
        );
    }

    public function attributeLabels(){
        return array(
            'id' => 'ID',
        );
    }

}