<?php

/**
 * This is the model class for table "{{domains}}".
 *
 * The followings are the available columns in table '{{domains}}':
 * @property integer $id
 * @property string $domain
 * @property string $ru_domain
 * @property string $page
 */
class Domain extends CActiveRecord {
	
	private static $idna_convert = false;
	
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Domain the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{domains}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('domain', 'required'),
            array('domain, ru_domain, page', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, domain, ru_domain, page', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'whois' => array(self::HAS_MANY, 'DomainsWhois', 'domain_id', 'order' => 'id asc'),
            'whois_history' => array(self::HAS_MANY, 'DomainsWhoisFull', 'domain_id', 'order' => 'id desc'),
            'wayback' => array(self::HAS_MANY, 'DomainsWayback', 'domain_id', 'order' => 'date desc'),
            'ip' => array(self::HAS_MANY, 'DomainsIp', 'domain_id', 'order' => 'date desc'),
            'headers' => array(self::HAS_MANY, 'DomainsHeaders', 'domain_id', 'order' => 'date desc'),
            'sitemap' => array(self::HAS_MANY, 'Sitemap', 'domain_id', 'order' => 'url asc'),
	    'yandexstruct' => array(self::HAS_MANY, 'YandexStructure', 'domain_id', 'order' => 'url asc'),
            'mainpage' => array(self::HAS_ONE, 'DomainsMainpage', 'domain_id', 'order' => 'id desc'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'domain' => 'Domain',
            'ru_domain' => 'Ru Domain',
            'page' => 'Main Page',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('domain', $this->domain, true);
        $criteria->compare('ru_domain', $this->ru_domain, true);
        $criteria->compare('page', $this->ru_domain);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

	public function afterFind(){
		$this->domain = trim($this->domain);
	}

	public function host() {
		$out = $this->domain;

		if (strpos($this->domain, '://') !== false) {
			$out = parse_url($this->domain, PHP_URL_HOST);
		}

		return $out;
	}
	
	public function url() {
		$out = '';

		if (strpos($this->domain, '://') === false) {
			$out.= 'http://';
		}

		$out.= $this->domain;

		return $out;
	}
	
	public static function checkRealdomain($domain){
	
		//$convert = new idna_convert();
		
		if(!self::$idna_convert) self::$idna_convert = new idna_convert();
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, self::$idna_convert->encode($domain));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36');
		$result = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);
		
		$curl = NULL;
		
		//var_dump($info);
		
		if($info['http_code'] > 300 && $info['http_code'] < 320 && isset($info['redirect_url'])){
			$domain = $info['redirect_url'];
			
			$info = NULL;
			
			$domain = str_replace('http://', '', $domain);
			$domain = str_replace('https://', '', $domain);
			
			$domainChunks = explode('/', $domain);
			
			$domain = $domainChunks[0];
			
			$mainPage = '';
			
			if(isset($domainChunks[1]))
				for($i = 1; $i < count($domainChunks); $i++)
					$mainPage .= '/' . $domainChunks[$i];
			
			$domainChunks = NULL;
			
			return array('domain' => $domain, 'page' => $mainPage);
		}
		
		return array('domain' => $domain, 'page' => '');
		
	}

    public static function check($domain,$hard=false) {
		
		$domain = (string)$domain;

		$domain = trim($domain);

        if ($d = Domain::model()->findByAttributes(array('domain' => $domain))) { /** @var Domain $d */
			
			if($hard==true){
			
				$checked_domain = self::checkRealdomain($domain);
			
				if($checked_domain != $domain){
				
					if(!self::$idna_convert) self::$idna_convert = new idna_convert();

					$checked_domain['domain'] = trim($checked_domain['domain']);

					$d->domain = self::$idna_convert->encode($checked_domain['domain']);
					$d->ru_domain = self::$idna_convert->decode($checked_domain['domain']);
					$d->page = ($checked_domain['page'] == '/')? '' : $checked_domain['page'];
					
					$d->update(array('domain','ru_domain','page'));
					
				}
				
				$checked_domain = NULL;
				
			}
			
            return $d;
        }
		
		/*$checked_domain = self::checkRealdomain($domain);*/

		$checked_domain = array(
			'domain' => $domain,
			'page' => ''
		);
		
		if (!self::$idna_convert) self::$idna_convert = new idna_convert();
		
		$domain = self::$idna_convert->encode($checked_domain['domain']);

		$domain = trim($domain);
		
		if ($d = Domain::model()->findByAttributes(array('domain' => $domain))){
		
			$d->ru_domain = self::$idna_convert->decode($checked_domain['domain']);
			$d->page = $checked_domain['page'];
			$d->update(array('ru_domain','page'));
		
			return $d;
		}
		
		$d = new Domain();
		
		$d->domain = $domain;
		$d->ru_domain = self::$idna_convert->decode($checked_domain['domain']);
		$d->page = $checked_domain['page'];
		
        $d->save();
		
        return $d;
    }

}