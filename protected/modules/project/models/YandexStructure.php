<?php

class YandexStructure extends CActiveRecord {

    public function tableName() {
        return '{{yandex_structure}}';
    }

    public function rules() {
        return array(
            array('project_id, domain_id, check_id, url', 'required'),
            array('project_id, domain_id, check_id', 'numerical', 'integerOnly' => true),
            array('id, project_id, domain_id, check_id, url, title', 'safe', 'on' => 'search'),
        );
    }

        
    public function relations() {
        return array(
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
			'check' => array(self::BELONGS_TO, 'YandexStructureCheck', 'check_id'),
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
    
    public function checkStatus() {
        $context = stream_context_create(
            array(
                'http' => array(
                    'follow_location' => false
                )
            )
        );

        $text = @file_get_contents($this->url, false, $context);

        foreach ($http_response_header as $h) {
            $m = array();

            if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $h, $m )) {
                $this->status = intval($m[1]);
                break;
            }
        }

        if ($text) {

            $transaction = Yii::app()->db->beginTransaction();

            $page = new Page;
            $page->domain_id = $this->domain_id;
            $page->sitemap_id = $this->id;
            $page->text = $text;
            $page->save();

            $transaction->commit();

            if ($page->id) {
                $page->saveAll();
            }

            if (preg_match("/<title>(.*)<\/title>/siU", $text, $title_matches)) {
                $title = preg_replace('/\s+/', ' ', $title_matches[1]);
                $this->title = trim($title);
            }

            
        }

        $this->check_date = new CDbExpression('NOW()');

        $this->save();
    }
}
