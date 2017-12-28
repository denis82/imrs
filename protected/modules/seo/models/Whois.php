<?php

/**
 * This is the model class for table "{{whois}}".
 *
 * The followings are the available columns in table '{{whois}}':
 * @property integer $id
 * @property integer $domain_id
 * @property string $checkdate
 * @property string $created
 * @property string $updated
 * @property string $expiration
 * @property string $ns
 * @property string $registrar
 *
 * @property Domain $domain
 */
class Whois extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Whois the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{whois}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
			array('domain_id, checkdate, created, updated, ns, registrar', 'required'),
			//array('domain_id, checkdate, created', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('ns, registrar', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, domain_id, checkdate, created, updated, expiration, ns, registrar', 'safe', 'on' => 'search'),
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
            'created' => 'Created',
            'update' => 'Update',
            'expiration' => 'Expiration',
            'ns' => 'Ns',
            'registrar' => 'Registrar',
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
        $criteria->compare('created', $this->created, true);
        $criteria->compare('update', $this->update, true);
        $criteria->compare('expiration', $this->expiration, true);
        $criteria->compare('ns', $this->ns, true);
        $criteria->compare('registrar', $this->registrar, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getDomain() {
        $domain = Domain::model()->findByPk($this->domain_id);
        return $domain;
    }

    public static function check($domain_id, $date = null, $force = false){
        $date = $date? $date : date('Y-m-d');

		$domain = Domain::model()->findByPk($domain_id); /** @var Domain $domain */

		if(!$domain) return false;

		$model = Whois::model()->findByAttributes(['domain_id' => $domain_id]); /** @var $model Whois */

		if($model && $model->checkdate == $date && $force == false) return $model;

		if(!$model){
			$model = new Whois();
			$model->domain_id = $domain_id;
		}

		$componentsPath = Yii::getpathOfAlias('application.modules.seo.components');

		include_once($componentsPath . '/Whois/Info.php');
		include_once($componentsPath . '/Whois/Info/Element.php');
		include_once($componentsPath . '/Whois/Info/Element/Date.php');
		include_once($componentsPath . '/Whois/Info/Element/NServer.php');
		include_once($componentsPath . '/Whois/Info/Element/State.php');
		include_once($componentsPath . '/Whois/Service.php');
		include_once($componentsPath . '/Whois/Services/RIPN.php');

		$whois = new Whois\Info($domain->domain);

		if($whois->get()){
			
			if($whois->created)
				$model->created = $whois->created->date;

			if($whois->ns)
				$model->ns = join(', ', array_slice($whois->ns->getServersArray(), 0, 3));
			else
				$model->ns = '';

			if($whois->updated)
				$model->updated = $whois->updated->date;

			if($whois->expiration)
				$model->expiration = $whois->expiration->date;

			if($whois->registrar)
				$model->registrar = $whois->registrar;
			else
				$model->registrar = '';

		}else{
			return false;
		}

		/*
        $content = CUtils::getContent("http://whoiz.herokuapp.com/lookup.json?url=" . $model->domain->domain);
        if (strpos($content, "No entries found for the selected source")) {
            $domainParts = explode(".", $model->domain->domain);
            unset($domainParts[0]);
            $domain = implode(".", $domainParts);
            $content = CUtils::getContent("http://whoiz.herokuapp.com/lookup.json?url=" . $domain);
        }
        $rows = explode('\n', $content);
		*/

        $model->updated = $model->updated ? $model->updated : $model->created;

		$model->checkdate = $date;

        if ($model->save()) {
            return $model;
        }

        CUtils::traceValidationErrors($model);
    }

}