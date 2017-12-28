<?php

/**
 * @property integer $id
 * @property integer $ahrefs_id
 * @property string $anchor
 * @property string $date
 */
class AnchorsCloud extends CActiveRecord {

	private $domain;

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
		return '{{anchors_cloud}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('domain_id, anchor, count, percent, date', 'required'),
			array('anchor', 'length', 'max' => 255),
			array('count, percent', 'safe'),
			array('domain_id, anchor, count, percent, date', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'stats' => array( self::BELONGS_TO, 'AnchorsCloudStats', 'anchor_id' )
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'domain_id' => 'Идентификатор домена',
			'anchor' => 'Анкор',
			'date' => 'Дата'
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
		$criteria->compare('domain_id', $this->domain_id, true);
		$criteria->compare('anchor', $this->anchor, true);
		$criteria->compare('date', $this->date, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	public function beforeValidate() {
		return parent::beforeValidate();
	}

	public function beforeSave() {
		return parent::beforeSave();
	}

	public function afterFind() {
		return parent::afterFind();
	}


}