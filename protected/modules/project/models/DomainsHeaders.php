<?php

class DomainsHeaders extends CActiveRecord {

    public function tableName() {
        return '{{domains_headers}}';
    }

    public function rules() {
        return array(
            array('domain_id', 'required'),
            array('domain_id, if_modified_since', 'numerical', 'integerOnly' => true),
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

            $context = stream_context_create(
                array(
                    'http' => array(
                        'follow_location' => false
                    )
                )
            );

            $data = @file_get_contents($model->url(), false, $context);

            $h = new self;
            $h->domain_id = $model->id;
            $h->text = implode("\r\n", $http_response_header);
            $h->if_modified_since = intval( $this->checkIfModifiedSince( $model->url() ) );
            $h->save();

            return self::model()->findByPk( $h->id );
        }

        return false;
    }

    private function checkIfModifiedSince( $url ) {
        $opts = array('http' =>
            array(
                'method'  => 'GET',
                'header'  => "If-Modified-Since: " . date('r') . "\r\n",
                'timeout' => 60
            )
        );
                                
        $context  = stream_context_create($opts);
        $result = @file_get_contents($url, false, $context);

        foreach ($http_response_header as $h) {
            $m = array();

            if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $h, $m )) {
                $status = intval($m[1]);
                break;
            }
        }

        return ($status == 304);
    }

    public function getCookie() {
    	$result = array();

    	foreach (explode("\n", $this->text) as $line) {
    		$line = trim($line);
    		list($j, $i) = explode(':', $line, 2);

    		if (trim($j) == 'Set-Cookie') {
    			$result[] = $i;
    		}
    	}

    	return $result;
    }

}
