<?php

class Sitemap extends CActiveRecord {

    public function tableName() {
        return '{{sitemap}}';
    }

    public function rules() {
        return array(
            array('domain_id, url', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('id, domain_id, url, status, title, check_date', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'page' => array(self::HAS_ONE, 'Page', 'sitemap_id', 'order' => 'id desc'),
            'pages' => array(self::HAS_MANY, 'Page', 'sitemap_id', 'order' => 'id desc'),
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
        $criteria->compare('domain_id', $this->domain_id);
        $criteria->compare('url', $this->url);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
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
