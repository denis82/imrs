<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class DomainsMainpage extends CActiveRecord {

    public function tableName() {
        return '{{domains_mainpage}}';
    }

    public function rules() {
        return array(
            array('domain_id', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('id, domain_id, text', 'safe', 'on' => 'search'),
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

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function download( $model ) {
    	if (is_numeric($model)) {
    		$model = Domain::model()->findByPk( $model );
    	}

    	if (!$model or !($model instanceof Domain)) {
    		return false;
    	}

        $opts = array('http' =>
            array(
                'method'  		=> 'GET',
                'user_agent' 	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                'timeout' 		=> 60,
            )
        );
                                
        $context  = stream_context_create($opts);
        $text = @file_get_contents($model->url(), false, $context);

    	if (is_array($http_response_header)) {
            foreach ($http_response_header as $h) {
                $m = array();

                if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $h, $m )) {
                    $status = intval($m[1]);
                    break;
                }
            }
    	}

    	if ($status !== 200) {
    		$text = '';
    	}

		$page = new self;
		$page->domain_id = $model->id;
		$page->text = $text;
		$page->save();

		return $page;
    }

}
