<?php

class IpWhois extends CActiveRecord {

    public function tableName() {
        return '{{ip_whois}}';
    }

    public function rules() {
        return array(
            array('ip, text', 'required'),
            array('id, ip, text', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
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
		$criteria->compare('ip', $this->ip);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function download( $ip ) {
        $data = @shell_exec('whois ' . $ip);

        if (strlen($data)) {
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

                $result[] = $j;
            }

            $new = new self;
            $new->ip = $ip;
            $new->text = implode("\n", $result);
            $new->save();

            return $new;
        }
    }

}
