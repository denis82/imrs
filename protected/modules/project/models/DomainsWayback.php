<?php

class DomainsWayback extends CActiveRecord {

    public function tableName() {
        return '{{domains_wayback}}';
    }

    public function rules() {
        return array(
            array('domain_id, url, date', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('image', 'safe'),
            array('id, domain_id, date', 'safe', 'on' => 'search'),
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
        $criteria->compare('date', $this->date);

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

        $result = array();

        if ($model and $model instanceof $type) {
            $baseDir = Yii::app()->basePath . '/..';
            $dir = '/upload/wayback/';

            $first = self::getSnapshot($model->domain, '1980-01-01 00:00:00');
            $last = self::getSnapshot($model->domain);

            $t1 = strtotime( $first['time'] );
            $t2 = strtotime( $last['time'] );
            $t3 = round(($t1 + $t2) / 2);

            $mid = self::getSnapshot($model->domain, $t3);

            $all = array();
            if ($first) {
                $all[] = $first;
            }
            if ($last) {
                $all[] = $last;
            }
            if ($mid and $mid['time'] != $first['time'] and $mid['time'] != $last['time']) {
                $all[] = $mid;
            }

            foreach ($all as $i) {
		var_dump($i['time']);
                $name = parse_url($model->url(), PHP_URL_HOST) . '-' . date('YmdHis', strtotime($i['time'])) . '.png';
                WebScreenshot::model(array('url' => $i['url']))->save( $baseDir . $dir . $name );

                $snap = new self;
                $snap->domain_id = $model->id;
                $snap->url = $i['url'];
                $snap->date = $i['time'];
                $snap->image = $dir . $name;
                $snap->save();

                $result[] = $snap;
            }
        }

        return $result;
    }

    public static function getSnapshot($host, $date = null) {
        $url = 'http://archive.org/wayback/available?';
        
        $query = array('url' => $host);

        if ($date) {
            if (!is_numeric($date)) {
                $date = strtotime($date);
            }

            $query['timestamp'] = date("YmdHis", $date);
        }

        $json = @file_get_contents($url . http_build_query($query));

        if ($json) {
            $data = json_decode($json, true);
            $data = $data['archived_snapshots']['closest'];

            if ($data and $data['timestamp']) {
                $data['time'] = substr($data['timestamp'], 0, 4) . '-' .
                    substr($data['timestamp'], 4, 2) . '-' .
                    substr($data['timestamp'], 6, 2) . ' ' .
                    substr($data['timestamp'], 8, 2) . ':' .
                    substr($data['timestamp'], 10, 2) . ':' .
                    substr($data['timestamp'], 12, 2)
                ;
            }

            return $data;
        }

        return false;
    }

}
