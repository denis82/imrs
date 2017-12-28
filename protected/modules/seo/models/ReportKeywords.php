<?php

/**
 * This is the model class for table "{{report}}".
 *
 * The followings are the available columns in table '{{report}}':
 * @property integer $id
 * @property string $date
 * @property string $keyword
 * @property integer $region_id
 * @property integer $project_id
 * @property integer $domain_id
 * @property integer $position
 * @property integer $pr
 * @property integer $tic
 * @property integer $yac
 * @property integer $index
 * @property integer $age
 * @property integer $donors
 * @property string $snp_text
 * @property integer $hout
 * @property integer $citation
 * @property integer $trust
 * @property integer $hin
 * @property integer $ankors
 * @property integer $top50
 * @property string $snp_title
 */
class ReportKeywords extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ReportKeywords the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{reportkeywords}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date, keyword, region_id, domain_id, project_id, position', 'required'),
            array('region_id, project_id, domain_id, position, pr, tic, yac, index, age, donors, hout, citation, trust, hin, ankors, top50', 'numerical', 'integerOnly' => true),
            array('keyword, snp_text, snp_title', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, date, keyword, region_id, domain_id, position, pr, tic, yac, index, age, donors, snp_text, hout, citation, trust, hin, ankors, top50, snp_title,project_id', 'safe', 'on' => 'search'),
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
            'date' => 'Date',
            'keyword' => 'Keyword',
            'region_id' => 'Region',
            'domain' => 'Домен',
            'position' => 'Позиции',
            'vposition' => 'Позиции',
            'pr' => 'PR',
            'tic' => 'Тиц',
            'yac' => 'ЯК',
            'yacLabel' => 'ЯК',
            'index' => 'Страницы',
            'age' => 'Возраст',
            'donors' => 'Доноры',
            'snp_text' => 'Текст сниппета',
            'hout' => 'Ссылки на сайте',
            'citation' => 'Поток цитирования',
            'trust' => 'Поток доверия',
            'hin' => 'Внешние ссылки',
            'ankors' => 'Анкоры',
            'top50' => 'Видимость сайта (ТОП50)',
            'snp_title' => 'Заголовок сниппета',
        );
    }

    public function attributeMain() {
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

    public function attributeExt() {
        return array(
            'pr',
            'hout',
            'hin',
            'ankors',
            'top50',
            'snp_title',
            'snp_text',
        );
    }

    public function attributeNeedMediana() {
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

    public function attributeSort() {
        return array(
            'position' => 'По позициям',
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
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('date', $this->date, true);
        $criteria->compare('keyword', $this->keyword, true);
        $criteria->compare('region_id', $this->region_id);
        $criteria->compare('domain_id', $this->domain_id);
        $criteria->compare('position', $this->position);
        $criteria->compare('pr', $this->pr);
        $criteria->compare('tic', $this->tic);
        $criteria->compare('yac', $this->yac);
        $criteria->compare('index', $this->index);
        $criteria->compare('age', $this->age);
        $criteria->compare('donors', $this->donors);
        $criteria->compare('snp_text', $this->snp_text, true);
        $criteria->compare('hout', $this->hout);
        $criteria->compare('citation', $this->citation);
        $criteria->compare('trust', $this->trust);
        $criteria->compare('hin', $this->hin);
        $criteria->compare('ankors', $this->ankors);
        $criteria->compare('top50', $this->top50);
        $criteria->compare('snp_title', $this->snp_title, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getDomain() {
        $domain = Domain::model()->findByPk($this->domain_id);
        return $domain->ru_domain;
    }

    public function getVposition() {

        return $this->position == 9999 ? null : $this->position;
    }

    public function getYacLabel() {
        return $this->attributes["yac"] ? "да" : "нет";
    }

}