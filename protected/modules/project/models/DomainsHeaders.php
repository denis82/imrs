<?php

class DomainsHeaders extends CActiveRecord {
    
    const SUCCESS = 200;
    const REDIRECT = 301;
    
    public $both_www = false;
    public $both_http = false;
    private $cur_https = '';
    private $cur_www = '';
    
    public function tableName() {
        return '{{domains_headers}}';
    }

    public function rules() {
        return array(
            array('domain_id', 'required'),
            array('domain_id, if_modified_since', 'numerical', 'integerOnly' => true),
            array('id, domain_id, text', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
			'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
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

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function download( $model ) {
        $type = 'Domain';

        if (is_numeric($model)) {
            $model = $type::model()->findByPk( $model );
        }

        $this->domain_id = $model->id;

        if ($model and $model instanceof $type) {

            $context = stream_context_create(
                array(
                    'http' => array(
                        'follow_location' => false
                    )
                )
            );

            $data = @file_get_contents($model->url(), false, $context);

            $h = new self;
            $h->domain_id = $model->id;
            $h->text = implode("\r\n", $http_response_header);
            $h->if_modified_since = intval( $this->checkIfModifiedSince( $model->url() ) );
            $h->www = $this->getStatusWww($model->domain); // настройка редиректа www/ без www  - [boolean]
            $h->https = $this->getStatusHost($model->domain); // настройка редиректа  https / без https  - [boolean]
            $h->current_www = $this->cur_www; // если редирект настроен
            
                  
/*                  $file = 'foo.txt';
// Открываем файл для получения существующего содержимого
$current = file_get_contents($file);
// Добавляем нового человека в файл
$current .= $this->cur_https . "\n";
// Пишем содержимое обратно в файл
file_put_contents($file, $current);
   */   
            
            $h->current_https = $this->cur_https; // если редирект настроен 
            $h->save();

            return self::model()->findByPk( $h->id );
        }

        return false;
    }

    private function checkIfModifiedSince( $url ) {
        $opts = array('http' =>
            array(
                'method'  => 'GET',
                'header'  => "If-Modified-Since: " . date('r') . "\r\n",
                'timeout' => 60
            )
        );
                                
        $context  = stream_context_create($opts);
        $result = @file_get_contents($url, false, $context);

        foreach ($http_response_header as $h) {
            $m = array();

            if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $h, $m )) {
                $status = intval($m[1]);
                break;
            }
        }

        return ($status == 304);
    }

    public function getCookie() {
    	$result = array();

    	foreach (explode("\n", $this->text) as $line) {
    		$line = trim($line);
    		list($j, $i) = explode(':', $line, 2);

    		if (trim($j) == 'Set-Cookie') {
    			$result[] = $i;
    		}
    	}

    	return $result;
    }
    

    
    /*
      Проверяет настроен ли редирект WWW 
      $url [string] - домен.
      return [bool]
    */
    
    private function getStatusWww($url) {

      preg_match('/[a-z]+:\/\/[w]{3}./', trim ( $url ), $matches);
      if (empty($matches)) {  // без www
	  $pattern = '/:\/\//';
	  $replacement = '://www.';
	  $with_www = preg_replace($pattern, $replacement, $url);
	  $without_www = $url;
      } else {  // c www
	  $pattern = '/:\/\/www./';
	  $replacement = '://';
	  $without_www = preg_replace($pattern, $replacement, $url);
	  $with_www = $url;
	  $this->cur_www = 'www';
      }
      $getStatusWithoutWww = $this->getStatus($without_www);
      $getStatusWithWww = $this->getStatus($with_www);
      if ( (self::REDIRECT == $getStatusWithWww and self::SUCCESS == $getStatusWithoutWww) ||
	     (self::SUCCESS == $getStatusWithWww and self::REDIRECT == $getStatusWithoutWww) ) {
			return true;
		
      } else {
	  return false;
      }
      
      //var_dump($this->getStatus($without_www). "!=" .$this->getStatus($with_www));
      //if ( 200 == $this->getStatus($without_www) and 200 == $this->getStatus($with_www) ) {
//       if ( $this->wwwStatus($without_www) != $this->wwwStatus($with_www) ) {
// 	    return true;
//       } else {
// 	    return false;
//       }

    }

    /*
      Сомнительнительный метод
      $url [string] - домен.
      return [bool] - статус
    */
    
    private function wwwStatus($url) {
	  
	$out = false;
	if( $curl = curl_init() ) {
	    curl_setopt($curl,CURLOPT_URL,$url);
	    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	    curl_setopt($curl,CURLOPT_NOBODY,true);
	    curl_setopt($curl,CURLOPT_HEADER,true);
	    $out = curl_exec($curl);
	    curl_close($curl);
	}
	
	//var_dump($out);
	if (preg_match("!Location: (.*)!", $out, $matches)) {
	
	    return true;
	} else {
	    return false;
	} 
        
    }
    
    
        /*
      Проверяет настроен ли редирект HTTPS/HTTP 
      $url [string] - домен.
      return [bool]
    */
    
    private function getStatusHost($url) {
      
      $arrParseUrl = parse_url($url);
      
//                         $file = 'foo.txt';
// // Открываем файл для получения существующего содержимого
// $current = file_get_contents($file);
// // Добавляем нового человека в файл
// $current .= $arrParseUrl['scheme'] . "!!!\n";
// // Пишем содержимое обратно в файл
// file_put_contents($file, $current);
      
           
      
      $this->cur_https = $arrParseUrl['scheme'];
      $explode = explode('://' , $url);

//       if ( 200 == $this->getStatus('https://' . $explode[1]) and 200 == $this->getStatus('http://' . $explode[1]) ) {
// 	  $this->both_http = true;
//       }
      if ( $this->wwwStatus('https://' . $explode[1]) != $this->wwwStatus( 'http://' . $explode[1]) ) {
	    return true;
      } else {
	    return false;
      }
      
    }
    
    private function getStatus($url){
	
	$status = 0;
	$context = stream_context_create(
            array(
                'http' => array(
                    'follow_location' => false
                )
            )
        );
        //var_dump($url); echo '<br>';
	$out = false;
	if( $curl = curl_init() ) {
	    curl_setopt($curl,CURLOPT_URL,$url);
	    curl_setopt($curl,CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	    curl_setopt($curl,CURLOPT_NOBODY,true);
	    curl_setopt($curl,CURLOPT_HEADER,true);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    $out = curl_exec($curl);
	    if($out === false)
	    {
		echo 'Ошибка curl: ' . curl_error($curl);
	    }
	    $httpcode = curl_getinfo($curl,CURLINFO_HTTP_CODE );
	    curl_close($curl);
	}
	
	
	//@file_get_contents($url, false, $context);
	//echo '<pre>'; var_dump($httpcode); echo '</pre>';
	$status = $httpcode;
	//echo '<pre>'; var_dump($out); echo '</pre>';
	//var_dump($out); echo '<br>';
	if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $out, $m )) {
		    $status = intval($m[1]);
	}
// 	$http_response_header = $out;
// 	if (is_array($http_response_header)) {
// 	    
// 	    foreach ($http_response_header as $h) {
// 		$m = array();
// 
// 		if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $h, $m )) {
// 		    $status = intval($m[1]);
// 		    break;
// 		}
// 	    }
// 	}
	//var_dump($status); echo '<br>';
	return $status;
    }
    
}
