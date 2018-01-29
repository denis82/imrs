<?php

class ValidateUrl 
{
    public $domain_id;
    function __construct( $domain_id, $url) {
    
//       $type = 'DomainsHeaders';
// 
//         if (is_numeric($model)) {
//             $model = $type::model()->findByPk( $model );
//         }
//         $this->domain_id = $model->id;
        $domainsHeadersModel = DomainsHeaders::model()->findByAttributes(array('domain_id' => $domain_id));
    }
    
    public function http() {
	$domainsHeadersModel = $this->request();
	return $domainsHeadersModel->current_https;
	
    }

    public function www() {
	$domainsHeadersModel = $this->request();
	return $domainsHeadersModel->current_www;
    }
    
    public function slash() {
	//$this->request();
	//return $domainsHeadersModel->current_http;
    }
    
    private function check() {
	$domainsHeadersModel = DomainsHeaders::model()->findByAttributes(array('domain_id' => $this->domain_id));
	return $domainsHeadersModel;
    }
}