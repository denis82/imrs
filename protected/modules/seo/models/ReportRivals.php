<?php

/**
 * This is the model class for table "{{reportrivals}}".
 *
 * The followings are the available columns in table '{{reportrivals}}':
 * @property integer $id
 * @property string $date
 * @property integer $domain_id
 * @property integer $position
 * @property integer $pr
 * @property integer $tic
 * @property integer $yac
 * @property integer $index
 * @property integer $age
 * @property integer $donors
 * @property integer $hout
 * @property integer $citation
 * @property integer $trust
 * @property integer $hin
 * @property integer $ankors
 * @property integer $top50
 * @property integer $project_id
 * @property integer $region_id
 */
class ReportRivals extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{reportrivals}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, domain_id, position, project_id, region_id', 'required'),
			array('domain_id, position, pr, tic, yac, index, age, donors, hout, citation, trust, hin, ankors, top50, project_id, region_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, domain_id, position, pr, tic, yac, index, age, donors, hout, citation, trust, hin, ankors, top50, project_id, region_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'date' => 'Date',
            'region_id' => 'Region',
            'domain' => 'Домен',
            'position' => 'Позиции',
            'vposition' => 'Количество',
            'pr' => 'PR',
            'tic' => 'Тиц',
            'yac' => 'ЯК',
            'yacLabel' => 'ЯК',
            'index' => 'Страницы',
            'age' => 'Возраст',
            'donors' => 'Доноры',
            'hout' => 'Ссылки на сайте',
            'citation' => 'Поток цитирования',
            'trust' => 'Поток доверия',
            'hin' => 'Внешние ссылки',
            'ankors' => 'Анкоры',
            'top50' => 'Видимость сайта (ТОП50)',
        );
    }

    public function attributeMain()
    {
        return array(
            'vposition',
            'domain',
            'tic',

            'yacLabel',
            'index',
            'age',
            'donors',

            'citation',
            'trust',
        );
    }

    public function attributeExt()
    {
        return array(
            'pr',
            'hout',
            'hin',
            'ankors',
            'top50',
        );
    }

    public function attributeNeedMediana()
    {
        return array(
            'vposition',
            'tic',
            'pr',
            'yacLabel' => 'yac',
            'index',
            'age',
            'donors',
            'hout',
            'citation',
            'trust',
            'hin',
            'ankors',
            'top50',
        );
    }

    public function attributeSort()
    {
        return array(
            'position' => 'По количеству',
            'tic' => 'По Тиц',
            'pr' => 'По Pr',
            'yac' => 'По ЯК',
            'index' => 'По страницам',
            'age' => 'По возрасту',
            'donors' => 'По Донорам',
            'hout' => 'По внешним ссылкам',
            'citation' => 'По цитированию',
            'trust' => 'По доверию',
            'hin' => 'По ссылкам на сайте',
            'ankors' => 'По анкорам',
            'top50' => 'По ТОП50',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('domain_id',$this->domain_id);
		$criteria->compare('position',$this->position);
		$criteria->compare('pr',$this->pr);
		$criteria->compare('tic',$this->tic);
		$criteria->compare('yac',$this->yac);
		$criteria->compare('index',$this->index);
		$criteria->compare('age',$this->age);
		$criteria->compare('donors',$this->donors);
		$criteria->compare('hout',$this->hout);
		$criteria->compare('citation',$this->citation);
		$criteria->compare('trust',$this->trust);
		$criteria->compare('hin',$this->hin);
		$criteria->compare('ankors',$this->ankors);
		$criteria->compare('top50',$this->top50);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('region_id',$this->region_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ReportRivals the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getDomain()
    {
        $domain = Domain::model()->findByPk($this->domain_id);
        return $domain->ru_domain;
    }

    public function getVposition()
    {

        return $this->position == 9999 ? null : $this->position;
    }

    public function getYacLabel()
    {
        return $this->attributes["yac"] ? "да" : "нет";
    }
}
