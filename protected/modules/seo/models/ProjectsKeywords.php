<?php

/**
 * This is the model class for table "{{projects_keywords}}".
 *
 * The followings are the available columns in table '{{projects_keywords}}':
 * @property integer $id
 * @property integer $project_id
 * @property integer $keyword_id
 */
class ProjectsKeywords extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{projects_keywords}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('keyword_id, project_id', 'required'),
            array('keyword_id, project_id', 'numerical', 'integerOnly' => true),
            array('id, keyword_id, project_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
			'keyword' => array(self::BELONGS_TO, 'Keywords', 'keyword_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
			'id' => 'ID',
			'project_id' => 'Project ID',
			'keyword_id' => 'Keyword ID',
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
		$criteria->compare('project_id', $this->region_id);
        $criteria->compare('keyword_id', $this->keyword_id);

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

}
