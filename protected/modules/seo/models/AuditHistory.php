<?php

/**
 * This is the model class for table "{{domains}}".
 *
 * The followings are the available columns in table '{{domains}}':
 * @property integer $id
 * @property integer $audit_id
 * @property string $date
 */
class AuditHistory extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Domain the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{audit_history}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('audit_id, date', 'required'),
            array('date', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, audit_id, date', 'safe', 'on' => 'search'),
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
            'audit_id' => 'Audit ID',
            'date' => 'Date',
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
        $criteria->compare('audit_id', $this->audit_id, true);
        $criteria->compare('date', $this->date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
	
	public static function get($audit_id, $date){
		return self::model()->find('audit_id=:audit_id and date=:date',array(':audit_id'=>$audit_id,':date'=>$date));
	}
	
	public function audit($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'audit_id=:audit_id',
            'params'=>array(':audit_id'=>$id),
        ));
        return $this;
    }
	
	public static function create($audit_id, $date){
		if(self::get($audit_id, $date)==NULL){
			$auditHistory = new self;
			$auditHistory->audit_id = $audit_id;
			$auditHistory->date = $date;
			$auditHistory->save();
			return $auditHistory;
		}
	}

}