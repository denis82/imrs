<?php

class DomainsWhois extends CActiveRecord {

    public function tableName() {
        return '{{domains_whois}}';
    }

    public function rules() {
        return array(
            array('domain_id, name', 'required'),
            array('domain_id, full_id', 'numerical', 'integerOnly' => true),
            array('id, domain_id, full_id, name, value', 'safe', 'on' => 'search'),
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
        $criteria->compare('full_id', $this->full_id);
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

            if ($tmp[0] == 'www') {
                $tmp = array_slice($tmp, 1);
            }

            $result = array();

            while (count($tmp) > 1 and count($result) < 3) {
                $result = array();
                $host = implode('.', $tmp);

                $data = @shell_exec('whois ' . $host);

                if (strlen($data)) {

                    $rows = explode("\n", $data);
                    foreach ($rows as $j) {
                        $j = trim($j);

                        $s = preg_replace('/http(s?):/i', '', $j);

                        $n = 0;
                        $q = explode(' ', $s);
                        foreach ($q as $i) {
                            if (strlen(trim($i))) {
                                $n++;
                            }
                        }

                        if ($j[0] == '%' or 
                            $j[0] == '#' or 
                            strpos($s, ':') === false or
                            $n > 9 or
                            strlen($j) == 0
                        ) continue;

                        $i = explode(":", $j, 2);

                        $i[0] = trim($i[0]);
                        $i[1] = trim($i[1]);

                        $result[] = $i;
                    }

                }

                $tmp = array_slice($tmp, 1);
            }

            if ($result and count($result)) {
                $params = array(
                    'host' => $host,
                    'created' => 0,
                    'expire' => 0,
                    'ns' => array()
                );

                $full = new DomainsWhoisFull;
                $full->domain_id = $model->id;
                $full->text = $data;
                $full->save();

                foreach ($result as $i) {
                    $item = new self;
                    $item->domain_id = $model->id;
                    $item->full_id = $full->id;
                    $item->name = $i[0];
                    $item->value = $i[1];
                    $item->save();

                    if (in_array($item->name, array('Creation Date', 'created', 'Registration Time', 'Changed'))) {
                        $params['created'] = max($params['created'], strtotime($item->value));
                    }
                    if (in_array($item->name, array('Expiration Date', 'Registrar Registration Expiration Date', 'paid-till', 'validity', 'Expiration Time'))) {
                        $params['expire'] = max($params['expire'], strtotime($item->value));
                    }
                    if (in_array($item->name, array('Name Server', 'nserver'))) {
                        $ns = strtolower($item->value);
                        if (!in_array( $ns, $params['ns']) ) {
                            $params['ns'][] = $ns;
                        }
                    }
                }

                foreach ($params as $j => $i) {
                    if ($i) {
                        $p = new DomainsWhoisParam;
                        $p->domain_id = $model->id;
                        $p->full_id = $full->id;
                        $p->name = $j;
                        $p->value = is_array($i) ? implode(', ', $i) : 
                                    (is_numeric($i) ? date('Y-m-d H:i:s', $i) : $i);
                        $p->save();
                    }
                }

            }
        }

        return DomainsWhoisFull::model()->findByPk($full->id);
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
