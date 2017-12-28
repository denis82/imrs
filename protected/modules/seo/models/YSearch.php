<?php

/**
 * This is the model class for table "{{ysearch}}".
 *
 * The followings are the available columns in table '{{ysearch}}':
 * @property integer $id
 * @property string $checkdate
 * @property string $keyword
 * @property integer $region_id
 * @property string $xml
 * (-DEPRECATED) property integer $page
 */
class YSearch extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{ysearch}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('checkdate, keyword, region_id, xml', 'required'),
            array('region_id', 'numerical', 'integerOnly' => true),
            array('keyword', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, checkdate, keyword, region_id, xml', 'safe', 'on' => 'search'),
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
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'checkdate' => 'Checkdate',
            'keyword' => 'Keyword',
            'region_id' => 'Region',
            'xml' => 'Xml',
           // 'page' => 'Page',
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
        $criteria->compare('checkdate', $this->checkdate, true);
        $criteria->compare('keyword', $this->keyword, true);
        $criteria->compare('region_id', $this->region_id);
        $criteria->compare('xml', $this->xml, true);
        //$criteria->compare('page', $this->page);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return YSearch the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function findByParams($date, $keyword, $region, $page){
        return YSearch::model()->findByAttributes(array('region_id' => $region, 'page' => $page, 'keyword' => $keyword, 'checkdate' => $date));
    }

    public function check($date, $keyword, $region, $page = false) {
        if ($ysearch = self::model()->findByParams($date, $keyword, $region, $page)) {
            return $ysearch;
        }
		
		$YAXML = new YandexXML();
		
		$yaxml->addProxy(YandexProxy::create(
			'127.0.0.1:3128',
			'paul:zawert',
			'xsite',
			'03.37624:68dcfd904d9cac84ac2e25bf79b104af'
		));
		
		$query = 'http://xmlsearch.yandex.ru/xmlsearch?user={USER}&key={KEY}&lr={REGION}&page={PAGE}&query={QUERY}';
		
		$query = str_replace('{USER}', 'xsite', $query);
		$query = str_replace('{KEY}', '03.37624:68dcfd904d9cac84ac2e25bf79b104af', $query);
		$query = str_replace('{REGION}', $region, $query);
		$query = str_replace('{PAGE}', $page, $query);
		$query = str_replace('{QUERY}', urlencode($keyword), $query);
		
		//$response = CUtils::getContent($query);
		
		$url = $query;
		
		// NIC: 194.85.92.47
		// GERMANY: 148.251.9.147
		
		//$proxy = '148.251.9.147:3128';
		//$proxyauth = 'paul:zawert';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		//curl_setopt($ch, CURLOPT_PROXY, $proxy);
		//curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		
		
		$dom = new DOMDocument();
		$dom->loadXML($response);
        foreach ($dom->getElementsByTagName('error') as $error){
            $error = simplexml_import_dom($error);
			//mail('ivan@seo-experts.com', 'Ошибка поиска по позициям', (string) $error . "\r\n\r\n" . htmlspecialchars($response));
            return false;
        }

        $ysearch = new YSearch();
        $ysearch->checkdate = $date;
        $ysearch->keyword = $keyword;
        $ysearch->region_id = $region;
        $ysearch->page = $page;
        $ysearch->xml = $response;
        if ($ysearch->save())
            return $ysearch;
        CUtils::traceValidationErrors($ysearch);
    }

}
