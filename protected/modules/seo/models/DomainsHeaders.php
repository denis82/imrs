<?php

class DomainsHeaders extends CActiveRecord {

    public function tableName() {
        return '{{domains_headers}}';
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

    public function download( $model ) {
        $type = 'Domain';

        if (is_numeric($model)) {
            $model = $type::model()->findByPk( $model );
        }

        $this->domain_id = $model->id;

        if ($model and $model instanceof $type) {

            $data = @shell_exec('curl -I ' . $model->url());

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
