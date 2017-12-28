<?php

class DomainsIp extends CActiveRecord {

    public function tableName() {
        return '{{domains_ip}}';
    }

    public function rules() {
        return array(
            array('domain_id', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('id, domain_id, ip', 'safe', 'on' => 'search'),
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
        $criteria->compare('ip', $this->ip);

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

            $data = @shell_exec('host ' . $model->host());

            if (strlen($data)) {
                $lines = explode("\n", $data);

                foreach ($lines as $l) {
                    $l = trim($l);

                    if ($n = strpos($l, ' has address ')) {
                        list($tmp, $ip) = explode(' has address ', $l);
                    }
                }

                if ($ip) {
                    $new = new self;
                    $new->domain_id = $model->id;
                    $new->ip = $ip;
                    $new->save();

                    return $new;
                }
            }
        }

        return false;
    }

}
