<?php

/**
 * @property integer $id
 * @property integer $ahrefs_id
 * @property integer $anchor_id
 * @property string $date
 * @property integer $count
 * @property float $percent
 */

class AnchorsCloudStats extends CActiveRecord {

	private $anchor;

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
		return '{{anchors_cloud_stats}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('anchor_id, date, count, percent', 'required'),
			array('date', 'length', 'max' => 255),
			array('anchor_id, date', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'anchor' => array( self::HAS_ONE, 'AnchorsCloud', array( 'id' => 'anchor_id' ) )
		);
	}
	
	public function ahrefs($id,$date=false){
		$this->getDbCriteria()->mergeWith(array(
            'condition'=>'ahrefs_id=:ahrefs_id',
            'params'=>array(':ahrefs_id'=>$id),
        ));
		if( $date ) return $this->date($date);
        return $this;
	}
	
	public function date($date){
	
		if(is_int($date)) $date = date('Y-m-d', $date);
		
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'date=:date',
            'params'=>array(':date'=>$date),
        ));
        return $this;
    }
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'anchor_id' => 'Идентификатор анкора',
			'date' => 'Дата',
			'count' => 'Количество доменов',
			'percent' => 'Проценты'
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
		$criteria->compare('anchor_id', $this->anchor_id, true);
		$criteria->compare('date', $this->date, true);
		$criteria->compare('count', $this->count, true);
		$criteria->compare('percent', $this->percent, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}


}