<?php

class DomainsWhois extends CActiveRecord {

    public function tableName() {
        return '{{domains_whois}}';
    }

    public function rules() {
        return array(
            array('domain_id, name', 'required'),
            array('domain_id', 'numerical', 'integerOnly' => true),
            array('id, domain_id, name, value', 'safe', 'on' => 'search'),
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
        $criteria->compare('name', $this->name);
        $criteria->compare('value', $this->value);

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
            $host = $model->domain;

            if (strpos($host, '//')) {
                $host = parse_url($model->domain, PHP_URL_HOST);
            }

            $tmp = explode('.', $host);

            $host = implode('.', array_slice($tmp, -2));

            $data = @shell_exec('whois ' . $host);

            if (strlen($data)) {
                $full = new DomainsWhoisFull;
                $full->domain_id = $model->id;
                $full->text = $data;
                $full->save();

                $rows = explode("\n", $data);
                foreach ($rows as $j) {
                    $j = trim($j);

                    $s = preg_replace('/http(s?):/i', '', $j);

                    if ($j[0] == '%' or 
                        $j[0] == '#' or 
                        strpos($s, ':') === false or
                        count(explode(' ', $s)) > 9 or
                        strlen($j) == 0
                    ) continue;

                    $i = explode(":", $j, 2);

                    $i[0] = trim($i[0]);
                    $i[1] = trim($i[1]);

                    $result[] = $i;
                }

                foreach ($result as $i) {
                    $item = new self;
                    $item->domain_id = $model->id;
                    $item->name = $i[0];
                    $item->value = $i[1];
                    $item->save();

                    $out[] = $item;
                }
            }

            /*list($tmp, $zone) = explode('.', $host, 2);

            if ($zone == 'ru' or $zone == 'XN--P1AI') {
                return $this->downloadRipn( $host );
            }*/
        }

        return $out;
    }

    private function downloadRipn( $host ) {

        $query = array('Whois' => $host);

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($query),
                'timeout' => 60
            )
        );
                                
        $context  = stream_context_create($opts);
        $result = @file_get_contents('http://www.ripn.net/nic/whois/whois.cgi', false, $context);

        $match = array();

        preg_match_all('/<pre>(.*?)<\/pre>/is', $result, $match);

        $data = strip_tags($match[1][0]);
        $result = array();

        if (strlen($data)) {
            $full = new DomainsWhoisFull;
            $full->domain_id = $this->domain_id;
            $full->text = $data;
            $full->save();

            $rows = explode("\n", $data);
            foreach ($rows as $j) {
                $j = trim($j);

                if ($j[0] == '%' or strlen($j) == 0) continue;

                $i = explode(":", $j, 2);

                $i[0] = trim($i[0]);
                $i[1] = trim($i[1]);

                if (strpos($i[0], ' ') === false and strlen($i[0])) {
                    $result[] = $i;
                }
            }

            foreach ($result as $i) {
                $item = new self;
                $item->domain_id = $this->domain_id;
                $item->name = $i[0];
                $item->value = $i[1];
                $item->save();

                $out[] = $item;
            }
        }

        return $out;
    }

}
