<?php

/**
 * @property integer $id 
 * @property integer $domain_id
 * @property integer $user_id
 * @property string $name
 * @property string $host 
 * @property string $keywords
 * @property string $regions 
 * @property string $lastcheck 
 * @property string $wordstat_date 
 */
class Project extends CActiveRecord {

    public $alltags;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Block the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{projects}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, host, regions', 'required'),
            array('name, host', 'length', 'max' => 250),
            array('user_id', 'numerical', 'integerOnly' => true),
            array('lastcheck, alltags', 'safe'),
            array('id, user_id, name, host, keywords, regions, lastcheck, wordstat_date, domain_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'keywordsid' => array(self::HAS_MANY, 'ProjectsKeywords', 'project_id'),
            'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
            'screenshot' => array(self::HAS_MANY, 'ProjectsScreenshot', 'project_id', 'order' => 'id desc'),

            'semantic_report' => array(self::HAS_ONE, 'YandexDirectReport', 'project_id', 'order' => 'id desc'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Системное название сайта (любое удобное)',
            'host' => 'Адрес сайта (начиная с http:// или https://)',
            'keywords' => 'Ключевые слова',
            'regions' => 'Регионы Яндекса (выберите из списка один)',
            'alltags' => 'Введите ключевые фразы (каждая в новой строке)',
			'wordstat_date' => 'Wordstat Date'
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
        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('host', $this->host, true);
        $criteria->compare('keywords', $this->keywords, true);
        $criteria->compare('regions', $this->regions, true);
        $criteria->compare('lastcheck', $this->lastcheck, true);
        $criteria->compare('wordstat_date', $this->wordstat_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function beforeValidate() {
	
		$this->host = SeoUtils::normalizeHost( $this->host );

        $host_level = count(explode('.', parse_url($this->host, PHP_URL_HOST)));
        if ($host_level > 3) {
            /* $this->addError('host', 'Введен домен более чем третьего уровня, система не может его принять по техническим причинам.');
            return false; */
        }

        $domain = Domain::check(  $this->host );
        $this->domain_id = $domain->id;
	
        /*if (trim($this->keywords) != '')
            $keywords = explode(',', $this->keywords);
        else
            $keywords = array();
		
		$ahrefsTools = AhrefsTools::init();
		$anchorCloud = $ahrefsTools->getAnchorsCloud( $domain->ru_domain );
		
		if( isset( $anchorCloud->Data ) and count( $anchorCloud->Data ) ){
			foreach( $anchorCloud->Data as $anchorInfo )
				$keywords[] = $anchorInfo->Data;
		}*/
		
		/*
        foreach (explode("\n", trim($this->alltags)) as $tag) {
            if (trim($tag) != "") {
                if (!in_array($tag, $keywords)){
                    $keywords[] = $tag;
                }
            }
        }
		*/
		
		/*foreach($keywords as $kw){
			$keywordObj = Keywords::addOnce($kw);
			if($keywordObj != NULL){
				$projectKeyword = new ProjectsKeywords;
				$projectKeyword->project_id = $this->id;
				$projectKeyword->keyword_id = $keywordObj->id;
				$projectKeyword->save();
				
				$projectKeyword = NULL;
			}
			
			$keywordObj = NULL;
		}*/
		
        $this->keywords = is_array($keywords) ? implode(',', $keywords) : '';

        if (parent::beforeValidate()) {
            $code = SeoUtils::testUrl( $this->domain->url() );

            if ($code == 200) {
                return true;
            }
            else {
                $this->addError('host', 'Сайт недоступен. Сервер вернул ошибку ' . $code);
            }
        }

        return false;
    }
	
	public function notUpdated(){
	
		$dateWordstat = date('Y-m-01');
	
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'wordstat_date!=:wordstat_date',
            'params'=>array(':wordstat_date'=>$dateWordstat),
        ));
		
        return $this;
    }

    public function beforeSave() {
        if (is_array($this->regions))
            $this->regions = implode(',', $this->regions);
			
		/* ПЕРЕНЕСЕНО В beforeValidate() */
		/*	
        $this->host = SeoUtils::normalizeDomain($this->host);
        $domain = Domain::check(SeoUtils::normalizeDomain($this->host, false));
        $this->domain_id = $domain->id;
		*/
        return parent::beforeSave();
    }

    public function afterFind() {
        if ($this->regions)
            $this->regions = explode(',', $this->regions);
        return parent::afterFind();
    }

    public function getDomain() {
        return str_replace('www.', '', basename($this->host));
    }
	
	public function getKeywordsData(){
	
		if(!$this->id) return NULL;
		
		if(!isset($this->keywordsid)){
			$this->keywordsid = self::getKeywordsById($this->id);
		}
		
		if(count($this->keywordsid)){
			$keywords = array();
			
			foreach($this->keywordsid as $i => $keywordIdObj){
				$keywords[$keywordIdObj->keyword->id] = $keywordIdObj->keyword->keyword;
				
				$this->keywordsid[$i] = NULL; // optimization memory
				
			}
			
			return $keywords;
		}
		
		return NULL;
	}
	
	public function getKeywords(){
	
		if(!$this->id) return NULL;
		
		return self::getKeywordsById($this->id);
	}
	
	public static function getKeywordsById($id){
		return ProjectsKeywords::model()->width('keyword')->findAllByAttributes(array('project_id'=>$id));
	}

    public function analisisYandexPosition($keyword, $region_id) {
        YPositions::check(date('Y-m-d'), $this->domain_id, $keyword, $region_id, false, false);
		/*
		$position = 1;
        $maxpage = 10;
        for ($page = 0; $page < $maxpage; $page++) {
            $ysearch = YSearch::model()->check(date("Y-m-d"), $keyword, $region_id, $page);
            if ($ysearch) {
                $exist = false;
                $dom = new DOMDocument();
                $dom->loadXML($ysearch->xml);
                foreach ($dom->getElementsByTagName('group') as $groupDom) {
                    $group = simplexml_import_dom($groupDom);
                    $domain = Domain::check(CUtils::normalizeDomain($group->doc->domain, false));
                    $yp = YPositions::check(date("Y-m-d"), $domain->id, $keyword, $region_id, $position, $dom->saveXML($groupDom));
                    if ($yp->domain_id == $this->domain_id) {
                        $exist = true;
                    }
                    $position++;
                }
                if ($exist) {
                    break;
                }
            }
        }
		*/
    }

    public function analisisRivals($keyword, $region_id, $actual = true, $date = null) {

        $date = $date ? $date : date('Y-m-d');
        //Получим топ10 по выдаче яндекса
        $criteria = new CDbCriteria();
        $criteria->condition = 'keyword=:keyword and checkdate=:date and region_id=:region and position<=9';
        $criteria->params = array('keyword' => $keyword, 'date' => date('Y-m-d', strtotime($date)), 'region' => $region_id);
        $criteria->order = 'position';
        $criteria->limit = 10;
        $rivals = YPositions::model()->findAll($criteria);
        $domains = array();
        foreach ($rivals as $query) {
            $domains[] = $query->domain_id;
        }
        if (!in_array($this->domain_id, $domains))
            $domains[] = $this->domain_id;
        
        foreach ($domains as $domain) {
            Solomono::check($domain);
            Whois::check($domain);
            Yandex::check($domain);
            CitationTrust::check($domain);
        }
    }

    public function favicon() {
        return ProjectsFavicon::model()->findByAttributes(array('project_id' => $this->id), array('order' => 'id desc'));
    }

    public function screenshot( $width = 1024 ) {
        return ProjectsScreenshot::model()->findByAttributes(array('project_id' => $this->id, 'width' => $width), array('order' => 'id desc'));
    }

    public function fullCheck() {

    	foreach (Queue::$STAGE as $j => $i) {
			$q = new Queue;
			$q->object_type = 'Project';
			$q->object_id = $this->id;
			$q->stage = $j;
			$q->save();
    	}

    }


}