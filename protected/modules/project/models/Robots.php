<?php

class Robots extends CActiveRecord {

    public function tableName() {
        return '{{robots}}';
    }

    public function rules() {
        return array(
            array('domain_id', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('id, domain_id, text', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('domain_id', $this->domain_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function url() {
        return $this->domain->url() . '/robots.txt';
    }

    public function sitemaps() {
        $result = array();

        $lines = explode("\n", $this->text);

        foreach ($lines as $l) {
            list($j, $i) = explode(':', trim($l), 2);

            if (strtolower(trim($j)) == 'sitemap') {
            	$i = trim($i);

            	if (!in_array($i, $result)) {
                	$result[] = $i;
            	}
            }
        }

        return $result;
    }

    public function last( $model ) {
        $type = 'Domain';

        if (is_numeric($model)) {
            $model = $type::model()->findByPk( $model );
        }

        if ($model and $model instanceof $type) {

            $el = self::model()->findByAttributes(array('domain_id' => $model->id));

            if ($el and $el->id) {
                return $el;
            }
            else {
                return $this->download( $model );
            }

        }

        return false;
    }

    public function download( $model ) {
        $type = 'Domain';

        if (is_numeric($model)) {
            $model = $type::model()->findByPk( $model );
        }

        $this->domain_id = $model->id;

        if ($model and $model instanceof $type) {

            $data = @file_get_contents( $model->url() . '/robots.txt' );

            if (strlen($data)) {
                $h = new self;
                $h->domain_id = $model->id;
                $h->text = $data;
                $h->save();

                return $h;
            }
        }

        return false;
    }

}
