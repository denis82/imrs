<?php

/**
 * This is the model class for table "{{yandex}}".
 *
 * The followings are the available columns in table '{{yandex}}':
 * @property integer $id
 * @property integer $domain_id
 * @property string $name
 * @property string $regions
 * @property string $created
 */
class Audit extends CActiveRecord {
	
	public $keywords;
	
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Audit the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{audit}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('domain_id, name, created, regions', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, domain_id, regions, created', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
			'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'domain_id' => 'Domain',
			'domain' => 'Домен',
			'name' => 'Наименование',
			'regions' => 'Регионы',
            'created' => 'Дата создания',
			'keywords' => 'Ключевые фразы'
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
        $criteria->compare('domain_id', $this->domain_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('regions', $this->regions, true);
        $criteria->compare('created', $this->created, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
	
    public function beforeSave() {
        if (is_array($this->regions))
            $this->regions = implode(',', $this->regions);
        return parent::beforeSave();
    }

    public function afterFind() {
        if ($this->regions)
            $this->regions = explode(',', $this->regions);
        return parent::afterFind();
    }
	
    public function getDomain() {
        $domain = Domain::model()->findByPk($this->domain_id);
        return $domain;
    }
	
	public function analisisYandexPosition($keyword, $region_id) {
		//$position = 1;
		//$maxpage = 10;
		
		$keyword_id = 0;
		$date = date('Y-m-d');
		$domain_id = $this->domain_id;
		
		//$exist = false;
		
		$keywordModel = Keywords::addOnce($keyword);
		
		if(!$keywordModel){
			//echo 'KW';
			return false;
		}
		
		if($keywordModel!==NULL){
			$keyword_id = $keywordModel->id;
			$keyword = $keywordModel->keyword;
		}else return false;
		
		$yp = YPositions::check($date, $domain_id, $keyword, $region_id, false, false, $keyword_id);
		
		if($yp) return true;
		
		//echo 'YP_FALSE';
		
		return false;
		
		/*
        for ($page = 0; $page < $maxpage; $page++){
            $ysearch = YSearch::model()->check(date('Y-m-d'), $keyword, $region_id, $page);
            if ($ysearch) {
                $exist = false;
                $dom = new DOMDocument();
                $dom->loadXML($ysearch->xml);
                foreach ($dom->getElementsByTagName('group') as $groupDom) {
                    $group = simplexml_import_dom($groupDom);
                    $domain = Domain::check(CUtils::normalizeDomain($group->doc->domain, false));
                    $yp = YPositions::check(date('Y-m-d'), $domain->id, $keyword, $region_id, $position, $dom->saveXML($groupDom));
                    if ($yp->domain_id == $this->domain_id) {
                        $exist = true;
                    }
                    $position++;
					$yp = NULL;
                }
                if ($exist) {
                    break;
                }
            }
			$ysearch = NULL;
        }
		
		if(!$exist && isset($this->domain_id) && $this->domain_id){
		
			$domain = Domain::model()->findByPk($this->domain_id);
			
			if($domain!==NULL){
				
				$groupDom = '<group>
					<categ attr="d" name="'.$domain->domain.'" />          
					<doccount>0</doccount>
					<relevance priority="all" />
					<doc>
						<relevance priority="all" />            
						<url></url>
						<domain>'.$domain->domain.'</domain>
						<title></title>
						<modtime>0</modtime>
						<size>0</size>
						<charset>utf-8</charset>
						<passages></passages>
						<properties>
							<_PassagesType>0</_PassagesType>
							<lang>ru</lang>
						</properties>
						<mime-type>text/html</mime-type>
						<saved-copy-url></saved-copy-url>
					</doc>
				</group>';
				
				$groupDom = str_replace(array("\n","\t","\r"), '', $groupDom);
				
				$dom = new DOMDocument();
				
				$dom->loadXML($groupDom);
				
				//$groupDom = $dom->getElementsByTagName('group');
				
				$date = date('Y-m-d');
				
				$condition = array('region_id' => $region_id, 'domain_id' => $domain->id, 'keyword' => $keyword, 'checkdate' => $date);
				
				if (YPositions::model()->findByAttributes($condition)) return true;
				
				$yp = new YPositions;
				$yp->checkdate = $date;
				$yp->created = date('Y-m-d H:i:s');
				$yp->keyword = $keyword;
				$yp->keyword_id = 
				$yp->domain_id = $domain->id;
				$yp->url = '0';
				$yp->title = '0';
				$yp->description = '0';
				$yp->position = 101;
				$yp->region_id = $region_id;
				$yp->xml = $groupDom;
				
				if (!$yp->save()) return false;
				
				//$yp = YPositions::check(date('Y-m-d'), $domain->id, $keyword, $region_id, 101, $dom->saveXML());
				
			}
			
		}
		*/
		
		return true;
		
    }

}