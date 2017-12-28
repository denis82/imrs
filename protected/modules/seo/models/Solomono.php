<?php

/**
 * This is the model class for table "{{solomono}}".
 *
 * The followings are the available columns in table '{{solomono}}':
 * @property integer $id
 * @property integer $domain_id
 * @property string $checkdate
 * @property integer $mr_sites
 * @property integer $ip_sites
 * @property integer $index_count
 * @property string $index_date
 * @property integer $din
 * @property integer $din_l1
 * @property integer $din_l2
 * @property integer $din_l3
 * @property integer $din_l4
 * @property integer $hin
 * @property integer $hin_l1
 * @property integer $hin_l2
 * @property integer $hin_l3
 * @property integer $hin_l4
 * @property integer $hout
 * @property integer $hout_l1
 * @property integer $hout_l2
 * @property integer $hout_l3
 * @property integer $hout_l4
 * @property integer $dout
 * @property integer $anchors
 * @property integer $anchors_out
 * @property integer $igood
 * @property integer $top50
 *
 * @property Domain $domain
 */
class Solomono extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Solomono the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{solomono}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('domain_id, checkdate, mr_sites, ip_sites, index_count, index_date, din, din_l1, din_l2, din_l3, din_l4, hin, hin_l1, hin_l2, hin_l3, hin_l4, hout, hout_l1, hout_l2, hout_l3, hout_l4, dout, anchors, anchors_out, igood, top50', 'required'),
            array('domain_id, mr_sites, ip_sites, index_count, din, din_l1, din_l2, din_l3, din_l4, hin, hin_l1, hin_l2, hin_l3, hin_l4, hout, hout_l1, hout_l2, hout_l3, hout_l4, dout, anchors, anchors_out, top50', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, domain_id, checkdate, mr_sites, ip_sites, index_count, index_date, din, din_l1, din_l2, din_l3, din_l4, hin, hin_l1, hin_l2, hin_l3, hin_l4, hout, hout_l1, hout_l2, hout_l3, hout_l4, dout, anchors, anchors_out, igood, top50', 'safe', 'on' => 'search'),
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
            'mr_sites' => 'Mr Sites',
            'ip_sites' => 'Ip Sites',
            'index_count' => 'Index Count',
            'index_date' => 'Index Date',
            'din' => 'Din',
            'din_l1' => 'Din L1',
            'din_l2' => 'Din L2',
            'din_l3' => 'Din L3',
            'din_l4' => 'Din L4',
            'hin' => 'Hin',
            'hin_l1' => 'Hin L1',
            'hin_l2' => 'Hin L2',
            'hin_l3' => 'Hin L3',
            'hin_l4' => 'Hin L4',
            'hout' => 'Hout',
            'hout_l1' => 'Hout L1',
            'hout_l2' => 'Hout L2',
            'hout_l3' => 'Hout L3',
            'hout_l4' => 'Hout L4',
            'dout' => 'Dout',
            'anchors' => 'Anchors',
            'anchors_out' => 'Anchors Out',
            'igood' => 'Igood',
            'top50' => 'Top50',
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
        $criteria->compare('checkdate', $this->checkdate, true);
        $criteria->compare('mr_sites', $this->mr_sites);
        $criteria->compare('ip_sites', $this->ip_sites);
        $criteria->compare('index_count', $this->index_count);
        $criteria->compare('index_date', $this->index_date, true);
        $criteria->compare('din', $this->din);
        $criteria->compare('din_l1', $this->din_l1);
        $criteria->compare('din_l2', $this->din_l2);
        $criteria->compare('din_l3', $this->din_l3);
        $criteria->compare('din_l4', $this->din_l4);
        $criteria->compare('hin', $this->hin);
        $criteria->compare('hin_l1', $this->hin_l1);
        $criteria->compare('hin_l2', $this->hin_l2);
        $criteria->compare('hin_l3', $this->hin_l3);
        $criteria->compare('hin_l4', $this->hin_l4);
        $criteria->compare('hout', $this->hout);
        $criteria->compare('hout_l1', $this->hout_l1);
        $criteria->compare('hout_l2', $this->hout_l2);
        $criteria->compare('hout_l3', $this->hout_l3);
        $criteria->compare('hout_l4', $this->hout_l4);
        $criteria->compare('dout', $this->dout);
        $criteria->compare('anchors', $this->anchors);
        $criteria->compare('anchors_out', $this->anchors_out);
        $criteria->compare('igood', $this->igood);
        $criteria->compare('top50', $this->top50);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getDomain() {
        $domain = Domain::model()->findByPk($this->domain_id);
        return $domain;
    }

    public static function check($domain_id, $date = null, $force = false) {
        $date = $date ? $date : date("Y-m-d");

		$domain = Domain::model()->findByPk($domain_id); /** @var Domain $domain */

		if(!$domain) return false;

		$model = Solomono::model()->findByAttributes(["domain_id" => $domain_id, "checkdate" => $date], ['order'=>'id DESC']);

        if ($model){

			if($force == false) {
				return $model;
			}else{
				$solomono = $model;
			}

		}else{
			$solomono = new Solomono();

			$solomono->domain_id = $domain_id;
		}

        //if(!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $domain->domain))
		//if(!preg_match('/^[a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $domain->domain)) return false;

		$componentsPath = Yii::getpathOfAlias('application.modules.seo.components');
			
		if(!is_dir($componentsPath)){
			$componentsPath = __DIR__ . '/../components';
		}
			
		include_once($componentsPath . '/LinkPad/Request.php');
		include_once($componentsPath . '/LinkPad/XML.php');

		include_once($componentsPath . '/LinkPad/XML/Element.php');
		include_once($componentsPath . '/LinkPad/XML/Counter.php');
		include_once($componentsPath . '/LinkPad/XML/Index.php');

		$request = new LinkPad\Request($domain->domain);

		if($response = $request->send()){
			$xml = LinkPad\XML::createFromXML($response);

			$solomono->mr_sites = $xml->mr;
			$solomono->ip_sites = $xml->ip;

			$solomono->index_count = (int)$xml->index->value;
			$solomono->index_date = date('Y-m-d', $xml->index->time);

			$solomono->din = (int)$xml->din->value;
			$solomono->din_l1 = (int)$xml->din->l1;
			$solomono->din_l2 = (int)$xml->din->l2;
			$solomono->din_l3 = (int)$xml->din->l3;
			$solomono->din_l4 = (int)$xml->din->l4;

			$solomono->hin = (int)$xml->hin->value;
			$solomono->hin_l1 = (int)$xml->hin->l1;
			$solomono->hin_l2 = (int)$xml->hin->l2;
			$solomono->hin_l3 = (int)$xml->hin->l3;
			$solomono->hin_l4 = (int)$xml->hin->l4;

			$solomono->hout = (int)$xml->hout->value;
			$solomono->hout_l1 = (int)$xml->hout->l1;
			$solomono->hout_l2 = (int)$xml->hout->l2;
			$solomono->hout_l3 = (int)$xml->hout->l3;
			$solomono->hout_l4 = (int)$xml->hout->l4;

			$solomono->dout = (int)$xml->dout;
			$solomono->anchors = (int)$xml->anchors;
			$solomono->anchors_out = (int)$xml->anchors_out;
			$solomono->igood = $xml->igood;

			$html = CUtils::getContent("https://www.linkpad.ru/tools/Default.aspx?t=ps&i=" . $domain->domain);

			if(preg_match('/<tr><td>c 1 по 50<\/td><td>(.*?)<\/td><\/tr>/', $html, $match))
				$solomono->top50 = (int)strip_tags($match[1]);
			else
				$solomono->top50 = 0;

			$solomono->checkdate = date("Y-m-d");

			if ($solomono->save()){
				return $solomono;
			}

			CUtils::traceValidationErrors($solomono);
		}

		return false;
    }

}