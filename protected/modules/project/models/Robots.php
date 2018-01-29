<?php

class Robots extends CActiveRecord {
    
    public $both_www = false;
    public $both_http = false;
    
    public function tableName() {
        return '{{robots}}';
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

    public function url() {
        return $this->domain->url() . '/robots.txt';
    }

    public function sitemaps() {
        $result = array();

        $lines = explode("\n", $this->text);

        foreach ($lines as $l) {
            list($j, $i) = explode(':', trim($l), 2);

            if (strtolower(trim($j)) == 'sitemap') {
            	$i = trim($i);

            	if (!in_array($i, $result)) {
                	$result[] = $i;
            	}
            }
        }

        return $result;
    }

    public function last( $model ) {
        $type = 'Domain';

        if (is_numeric($model)) {
            $model = $type::model()->findByPk( $model );
        }

        if ($model and $model instanceof $type) {

            $el = self::model()->findByAttributes(array('domain_id' => $model->id));

            if ($el and $el->id) {
                return $el;
            }
            else {
                return $this->download( $model );
            }

        }

        return false;
    }

    public function download( $model ) {
        $type = 'Domain';

        if (is_numeric($model)) {
            $model = $type::model()->findByPk( $model );
        }

        $this->domain_id = $model->id;

        if ($model and $model instanceof $type) {

            $data = @file_get_contents( $model->url() . '/robots.txt' );

            if (strlen($data)) {
                $h = new self;
                $h->domain_id = $model->id;
                $h->host = $this->hostWww($data);
                $h->protocol = $this->hostHttp($data);
                $h->text = $data;
                $h->save();
		
		$domainsHeadersModel = DomainsHeaders::model()->findByAttributes(array('domain_id' => $domain_id));
		if ( $this->both_www ) {
		    $domainsHeadersModel->current_www = $this->hostWww($data); // если редирект настроен
		}
		if ( $this->both_http ) {
		    $domainsHeadersModel->current_https = $this->hostHttp($data); // если редирект настроен 
		}
		$domainsHeadersModel->save();
		
                return $h;
            }
        }

        return false;
    }
    /*
      Проверяет значение директивы host домен с www или без www
      $var [string] -  текст robots 
      return [string]
    */
    private function hostWww($var) {
	

	// если открывается оба урла  www / без www
	    preg_match('/[H,h][O,o][S,s][T,t]:[w]{0,3}.+/', $var, $matche);
	    
	    if (!empty($matche)) {
	      $explode = explode(':' , $matche[0]);
	      if (preg_match('/[w]{3}./', trim ( $explode[1] ), $matche_explode)) {
		  $host = 'www';
	      }
	    }
	   
	return $host;
    }
    
        /*
      Проверяет значение директивы host домен с http или https
      $var [string] -  текст robots 
      return [string]
    */
    private function hostHttp($var) {
	
	 // если открывается оба урла  http / https
	    
	    preg_match('/[H,h][O,o][S,s][T,t]:[w]{0,3}.+/', $var, $matche);
	
	    if (!empty($matche)) {
		$protocol = parse_url ( $matche[0] ,PHP_URL_SCHEME);
		if('http' == $protocol or 'https' == $protocol) {
		    return $protocol;
		} else {
		    return '';
		}
	    } 

	return $http;
	
    }

}

