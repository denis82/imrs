<?php

/**
 * This is the model class for table "{{keywords}}".
 *
 * The followings are the available columns in table '{{keywords}}':
 * @property integer $id
 * @property string $keyword
 */
class Keywords extends CActiveRecord {
	
	public static $exclude_keywords = array(
		'noText', 'ссылка'
	);
	
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Keywords the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{keywords}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('keyword', 'required'),
            array('keyword', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, keyword', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id' => 'ID',
            'keyword' => 'Ключевая фраза',
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
        $criteria->compare('keyword', $this->keyword, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
	
	public static function addOnce($keyword){
		//$keyword = mb_strtolower(trim($keyword), 'UTF-8');
		$keyword = trim(preg_replace('/[^\w\s0-9\-]/ui', '', mb_strtolower($keyword,'UTF-8')));
		
		if(!$keyword) return false;
		
		$object = self::model()->findByAttributes(array('keyword'=>$keyword));
		
		if( $object == NULL ){
		
			$object = new self;
			$object->keyword = $keyword;
			$object->save();
			
		}
		
		return $object;
	}
	
	public static function add($keywords){
		
		if(!is_array($keywords)){
		
			//$keywords = strip_tags($keywords);
			
			$keywords = str_replace("\t",'',$keywords);
			$keywords = str_replace("\n\n",'|',$keywords);
			$keywords = str_replace("\n",'|',$keywords);
			$keywords = str_replace(',','|',$keywords);
			$keywords = str_replace('||','|',$keywords);
			
			//$keywords = preg_replace('/([^a-z0-9\|\\x{0410}-\\x{044F}\s])/ui', '', $keywords);
		
			$arrayKeywords = explode('|',$keywords);
			
		}else $arrayKeywords = $keywords;
		
		foreach($arrayKeywords as $key => $value){
			$kw = trim(preg_replace('/[^\w\s0-9\-]/ui', '', mb_strtolower($value,'UTF-8')));
			if(!in_array($kw, self::$exclude_keywords)) $arrayKeywords[$key] = $kw;
		}
		
		$arrayKeywords = array_filter($arrayKeywords);
		
		$keywordObjects = array();
		
		if(count($arrayKeywords))
			foreach($arrayKeywords as $kw){
				$keyword = self::model()->findByAttributes(array('keyword'=>$kw));
				
				if( $keyword == NULL ){
					$keyword = new self;
					$keyword->keyword = $kw;
					$keyword->save();
				}
				
				$keywordObjects[] = $keyword;
			}
		
		return (count($keywordObjects))? $keywordObjects : NULL;
	}

}