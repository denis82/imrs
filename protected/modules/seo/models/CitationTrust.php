<?php

/**
 * This is the model class for table "{{citationtrust}}".
 *
 * The followings are the available columns in table '{{citationtrust}}':
 * @property integer $id
 * @property integer $domain_id
 * @property string $checkdate
 * @property integer $citation
 * @property integer $trust
 */
class CitationTrust extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{citationtrust}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('domain_id, checkdate, citation, trust', 'required'),
            array('domain_id, citation, trust', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, domain_id, checkdate, citation, trust', 'safe', 'on' => 'search'),
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
            'domain_id' => 'Domain',
            'checkdate' => 'Checkdate',
            'citation' => 'Citation',
            'trust' => 'Trust',
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
        $criteria->compare('domain_id', $this->domain_id);
        $criteria->compare('checkdate', $this->checkdate, true);
        $criteria->compare('citation', $this->citation);
        $criteria->compare('trust', $this->trust);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CitationTrust the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getDomain() {
        $domain = Domain::model()->findByPk($this->domain_id);
        return $domain;
    }

    public static function check($domain_id, $date = null) {
        $date = $date ? $date : date('Y-m-d');
        if ($d = CitationTrust::model()->findByAttributes(array('domain_id' => $domain_id, 'checkdate' => $date))) {
            return $d;
        }
        $model = new CitationTrust();
        $model->domain_id = $domain_id;
        $url = 'http://ru.majesticseo.com/reports/site-explorer/summary/' . $model->domain->domain . "?IndexDataSource=F";
        $html = CUtils::getContent($url);
        $html = str_replace("\n", '', $html);
        $html = str_replace("\r", '', $html);
        $html = str_replace("\t", '', $html);
		
		if(preg_match('/<p>.*?Поток <br>цитирования \(Citation Flow\).*?<\/p><p.*?><b>(.*?)<\/b><\/p>/', $html, $match))
			$citation = $match[1];
		else
			$citation = 0;
			
		if(preg_match('/<p>.*?Поток <br>доверия \(Trust flow\).*?<\/p><p.*?><b>(.*?)<\/b><\/p>/', $html, $match))
			$trust = $match[1];
		else
			$trust = 0;
			
		$model->citation = intval($citation);
		$model->trust = intval($trust);
		$model->checkdate = $date;
		
		if ($model->save()) {
			return $model;
		}
		CUtils::traceValidationErrors($model);
		var_dump($model->errors);
		die();
    }

}
