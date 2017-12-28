<?php

/**
 * This is the model class for table "{{ypositions}}".
 *
 * The followings are the available columns in table '{{ypositions}}':
 * @property integer $id
 * @property string $url
 * @property string $checkdate
 * @property string $created
 * @property string $keyword
 * @property integer $keyword_id
 * @property integer $position
 * @property string $description
 * @property string $xml
 * @property string $title
 * @property integer $region_id
 * @property integer $domain_id 
 */
class YPositions extends CActiveRecord {

    public $countkeys;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{ypositions}}';
    }
	
	protected function afterConstruct(){
		$this->keyword_id = 0;
		parent::afterConstruct();
	}

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('url, checkdate, created, keyword, position, title, region_id, domain_id', 'required'),
            array('position, region_id, domain_id, keyword_id', 'numerical', 'integerOnly' => true),
            array('keyword, title', 'length', 'max' => 250),
            array('countkeys', 'safe'),
            // The following rule is used by search().            
            // @todo Please remove those attributes that should not be searched.
            array('id, url, checkdate, created, keyword, position, description, xml, title, region_id, domain_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
			'query' => array(self::BELONGS_TO, 'Keywords', 'keyword_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'url' => 'Url',
            'checkdate' => 'Date',
            'created' => 'Created',
            'keyword' => 'Ключевое слово',
            'keyword_id' => 'Идентификатор фразы',
            'position' => 'Позиция',
            'description' => 'Description',
            'xml' => 'Xml',
            'title' => 'Title',
            'region_id' => 'Region',
            'domain_id' => 'Domain',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('url', $this->url, true);
        $criteria->compare('checkdate', $this->date, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('keyword', $this->keyword, true);
        $criteria->compare('keyword_id', $this->keyword_id);
        $criteria->compare('position', $this->position);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('xml', $this->xml, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('region_id', $this->region_id);
        $criteria->compare('domain_id', $this->domain_id);


        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return YPositions the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function check($date, $domain_id, $keyword, $region_id, $position = false, $xml = false, $keyword_id = 0){
		
		if(!$keywordObject = Keywords::addOnce($keyword)) return false;
		
		$keyword = $keywordObject->keyword;
		
		$keyword_id = $keywordObject->id;
		
		$domain = Domain::model()->findByPk($domain_id);
		
		$yp = YPositions::model()->findByAttributes(array(
			'region_id' => $region_id, 
			'domain_id' => $domain_id, 
			'keyword' => $keyword, 
			'checkdate' => $date
		));
		
		if($yp) return $yp;
		
		$YAXML = new YandexXML();
		
		$YAXML->addProxy(YandexProxy::create(
			'127.0.0.1:3128',
			'paul:zawert',
			'xsite',
			'03.37624:68dcfd904d9cac84ac2e25bf79b104af'
		));
		
		if(!$info = $YAXML->getInfo($domain->domain, $keyword, $region_id)){
			$info = array(
				'url' => 'NULL',
				'title' => 'NULL',
				'position' => 0
			);
		}
		
		$yp = new YPositions();
		
		$yp->url = $info['url'];
		$yp->checkdate = $date;
		$yp->created = date('Y-m-d H:i:s');
		$yp->keyword = $keyword;
		$yp->keyword_id = $keyword_id;
		$yp->position = $info['position'];
		$yp->description = '';
		$yp->xml = '';
		$yp->title = $info['title'];
		$yp->region_id = $region_id;
		$yp->domain_id = $domain_id;
		
		
			
		if ($yp->save())
			return $yp;
		//else echo 'DONT SAVED';
			
		CUtils::traceValidationErrors($yp);
		
		return 1;
		
		/*
        $group = simplexml_load_string($xml);
        $yp = new YPositions();
        $yp->checkdate = $date;
        $yp->created = date('Y-m-d H:i:s');
        $yp->keyword = $keyword;
        $yp->keyword_id = $keyword_id;
        $yp->domain_id = $domain_id;
        $yp->url = strtolower($group->doc->url);
        $dom = new DOMDocument;
        $dom->loadXML($xml);
        foreach ($dom->getElementsByTagName('title') as $title){
            $yp->title = strip_tags($dom->saveXML($title));
        }
        $yp->description = $group->doc->headline;
        $yp->position = $position;
        $yp->region_id = $region_id;
        $yp->xml = $xml;
        if ($yp->save())
            return $yp;
        CUtils::traceValidationErrors($yp);
		*/
    }

    public function getPassage() {
        $dom = new DOMDocument;
        //file_put_contents('./getPassageXML.xml', $this->xml);
        $dom->loadXML($this->xml);
        foreach ($dom->getElementsByTagName('passage') as $passage){
            return strip_tags($dom->saveXML($passage));
        }
        return '';
    }

    public function getDomain(){
        $domain = Domain::model()->cache(3600)->findByPk($this->domain_id);
        return $domain->ru_domain;
    }
	
	public function orderByPositions()
    {
        $this->getDbCriteria()->mergeWith(array(
            'order'=>'position ASC'
        ));
        return $this;
    }
	
	public function beforeValidate(){
		
		$keyword = Keywords::addOnce($this->keyword);
		
		if($keyword) $this->keyword_id = intval(@$keyword->id);
		
		return parent::beforeValidate();
	}
	
	
	public static function generateXMLTable($audit_id){
	
		$audit = Audit::model()->findByPk($audit_id);
		$region = Yii::app()->request->getParam('region', current($audit->regions));
		
		$positions = YPositions::model()->orderByPositions()->findAll(
			'domain_id=:domain_id and checkdate=:date and region_id=:region',
			array(
				':date' => date('Y-m-d'),
				':region' => $region,
				':domain_id' => $audit->domain_id
			)
		);
		
		usort($positions, function($a, $b){
			if ($a->position == $b->position){
				return 0;
			}
			if($a->position == 0) return 1;
			if($b->position == 0) return -1;
			return ($a->position < $b->position) ? -1 : 1;
		});
		
		Block::prefix('w:');

		$table = Block::create('tbl');

		$table->insert(
			Block::create('tblPr')
				->insert(Block::create('tblW',array('w'=>'8800','type'=>'dxa')))
				->insert(Block::create('tblInd',array('w'=>'-106','type'=>'dxa')))
				->insert(Block::create('tblLook',array(
					'firstRow'=>1,
					'lastRow'=>0,
					'firstColumn'=>1,
					'lastColumn'=>0,
					'noHBand'=>0,
					'noVBand'=>0
				)))
		)->insert(
			Block::create('tblGrid')
				->insert(Block::create('gridCol',array('w'=>4700)))
				->insert(Block::create('gridCol',array('w'=>2200)))
		)->insert(
			Block::create('tr')
				->insert(Block::create('trPr')->insert(
					Block::create('trHeight',array('val'=>300))
				))
				->insert(
					Block::create('tc')->insert(
						Block::create('tcPr')->insert(
							Block::create('tcW',array('w'=>4700,'type'=>'dxa'))
						)->insert(
							Block::create('tcBorders')->insert(
								Block::create('top',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto'))
							)->insert(
								Block::create('left',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto'))
							)->insert(
								Block::create('bottom',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto'))
							)->insert(
								Block::create('right',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto'))
							)
						)
					)->insert(
						Block::create('p')->insert(
							Block::create('pPr')->insert(
								Block::create('spacing',array('after'=>0,'line'=>240,'lineRule'=>'auto'))
							)->insert(
								Block::create('rPr')
									->insert(Block::create('b'))
									->insert(Block::create('bCs'))
									->insert(Block::create('color',array('val'=>'000000')))
									->insert(Block::create('lang',array('eastAsia'=>'ru-RU')))
							)
						)->insert(
							Block::create('r')->insert(Block::text('t',array(),'Запрос'))
						)
					)
				)->insert(
					Block::create('tc')->insert(
						Block::create('tcPr')->insert(
							Block::create('tcW',array('w'=>2200,'type'=>'dxa'))
						)->insert(
							Block::create('tcBorders')->insert(
								Block::create('top',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto'))
							)->insert(
								Block::create('left',array('val'=>'nil'))
							)->insert(
								Block::create('bottom',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto'))
							)->insert(
								Block::create('right',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto'))
							)
						)
					)->insert(
						Block::create('p')->insert(
							Block::create('pPr')->insert(
								Block::create('spacing',array('after'=>0,'line'=>240,'lineRule'=>'auto'))
							)->insert(
								Block::create('rPr')
									->insert(Block::create('b'))
									->insert(Block::create('bCs'))
									->insert(Block::create('color',array('val'=>'000000')))
									->insert(Block::create('lang',array('eastAsia'=>'ru-RU')))
							)
						)->insert(
							Block::create('r')->insert(Block::text('t',array(),'Яндекс'))
						)
					)
				)
		);
		
		foreach($positions as $pos){
		
			$tr = Block::create('tr');
			
			$tr->insert(Block::create('trPr')->insert(Block::create('trHeight',array('val'=>300))));
			
			$tc = Block::create('tc');
			
			$tc->insert(
				Block::create('tcPr')->insert(
					Block::create('tcW',array('w'=>4700,'type'=>'dxa'))
				)->insert(
					Block::create('tcBorders')->insert(
						Block::create('top',array('val'=>'nil'))
					)->insert(
						Block::create('left',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto'))
					)->insert(
						Block::create('bottom',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto'))
					)->insert(
						Block::create('right',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto'))
					)
				)
			);
			$tc->insert(
				Block::create('p')->insert(
					Block::create('pPr')->insert(
						Block::create('spacing',array('after'=>0,'line'=>240,'lineRule'=>'auto'))
					)->insert(
						Block::create('rPr')
							->insert(Block::create('b'))
							->insert(Block::create('bCs'))
							->insert(Block::create('color',array('val'=>'000000')))
							->insert(Block::create('lang',array('eastAsia'=>'ru-RU')))
					)
				)->insert(
					Block::create('r')->insert(Block::text('t',array(),strip_tags($pos->keyword)))
				)
			);
			
			$tr->insert($tc);
			
			$tc = Block::create('tc');
			
			$tcProperty = Block::create('tcPr');
			
			$tcProperty->insert(Block::create('tcW',array('w'=>2200,'type'=>'dxa')));
			$tcProperty->insert(
				Block::create('tcBorders')
					->insert(Block::create('top',array('val'=>'nil')))
					->insert(Block::create('left',array('val'=>'nil')))
					->insert(Block::create('bottom',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto')))
					->insert(Block::create('right',array('val'=>'single','sz'=>4,'space'=>0,'color'=>'auto')))
			);
			
			if(is_numeric($pos->position)){
				if($pos->position != 0 && $pos->position <= 10) $colorFill = '00B050';
				if($pos->position != 0 && $pos->position > 10 && $pos->position <= 50) $colorFill = 'FFFF00';
				if($pos->position > 50 || $pos->position <= 0) $colorFill = 'FF0000';
				$tcProperty->insert(Block::create('shd',array('val'=>'clear','color'=>'000000','fill'=>$colorFill)));
			}
			
			$tcProperty->insert(Block::create('noWrap'));
			$tcProperty->insert(Block::create('vAlign',array('val'=>'bottom')));
			
			$tc->insert($tcProperty);
			
			$tc->insert(
				Block::create('p')->insert(
					Block::create('pPr')->insert(
						Block::create('spacing',array('after'=>0,'line'=>240,'lineRule'=>'auto'))
					)->insert(
						Block::create('rPr')
							->insert(Block::create('b'))
							->insert(Block::create('bCs'))
							->insert(Block::create('color',array('val'=>'000000')))
							->insert(Block::create('lang',array('eastAsia'=>'ru-RU')))
					)
				)->insert(
					Block::create('r')->insert(Block::text('t',array(),(!is_numeric($pos->position))?'':(($pos->position<101 && $pos->position > 0)?$pos->position:'более 100')))
				)
			);
			
			$tr->insert($tc);
			
			$table->insert($tr);
		}
		
		
		return $table->compile();
	}


}
