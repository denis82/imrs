<?php

/**
 * This is the model class for table "{{yandex}}".
 *
 * The followings are the available columns in table '{{yandex}}':
 * @property integer $id
 * @property integer $audit_id
 * @property integer $keyword_id
 */
class AuditKeywords extends CActiveRecord {
	
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
        return '{{audit_keywords}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('audit_id, keyword_id', 'required'),
            array('audit_id, keyword_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, audit_id, keyword_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
			'audit' => array(self::HAS_ONE, 'Audit', array('id'=>'audit_id')),
			'keyword' => array(self::HAS_ONE, 'Keywords', array('id'=>'keyword_id')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'audit_id' => 'Audit ID',
            'keyword_id' => 'Keyword ID',
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
        $criteria->compare('audit_id', $this->audit_id);
        $criteria->compare('keyword_id', $this->keyword_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
	
	public static function add($audit_id,$keyword_id){
		$auditKeyword = new self;
		$auditKeyword->audit_id = $audit_id;
		$auditKeyword->keyword_id = $keyword_id;
		$auditKeyword->save();
		return $auditKeyword;
	}
	
	public static function remove($audit_id,$keyword_id=false){
		$condition['audit_id'] = $audit_id;
		if( $keyword_id ) $condition['keyword_id'] = $keyword_id;
		$auditKeyword = Post::model()->findByAttributes($condition);
		if($auditKeyword!==NULL) $auditKeyword->delete();
	}
	
	public function orderById()
    {
        $this->getDbCriteria()->mergeWith(array(
            'order'=>'id ASC'
        ));
        return $this;
    }

}