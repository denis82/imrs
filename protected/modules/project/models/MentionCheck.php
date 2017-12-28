<?php

class MentionCheck extends CActiveRecord {

    public function tableName() {
        return '{{mention_check}}';
    }

    public function rules() {
        return array(
            array('domain_id', 'required'),
            array('domain_id, progress, page', 'numerical', 'integerOnly' => true),
            array('id, domain_id, progress, page', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
            'result' => array(self::HAS_MANY, 'Mention', 'check_id'),
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

    public function saveNextPage( ) {
        $n = count($this->result);

        $this->page++;
        $this->save();

        $YAXML = new YandexXML();

        $redis = new Redis();
        $redis->connect('127.0.0.1');

        $YAXML->setRedis($redis);

        $YAXML->addProxy(YandexProxy::create(
            '127.0.0.1:3128',
            'paul:zawert',
            Yii::app()->params['yandexXML']['user'],
            Yii::app()->params['yandexXML']['key']
        ));


        $YAXML->switchProxy();
        $REGION_ID = (int) 225;

        $xml = $YAXML->getXML($this->domain->host(), $REGION_ID, 100, $this->page-1);

        $host = $this->domain->host();

        if ($results = YandexXMLResult::parse($xml)) {
            $foundPhrase = (int)$results->foundDocsPhrase;
            $foundAll = (int)$results->foundDocsAll;
            $foundStrict = (int)$results->foundDocsStrict;

            $total = max( $foundPhrase, $foundAll, $foundStrict );

            foreach($results->list as $doc){

                if ($doc->domain == $host) {
                    continue;
                }

                $r = new Mention;
                $r->check_id = $this->id;
                $r->url = $doc->url;
                $r->title = $doc->title;
                $r->text = implode("\n", $doc->passages);
                $r->save();

                $n++;
            }
        }

        if (!$results or count($results->list) == 0) {
            $this->progress = 0;
            $this->save();
        }

        return array(
            'total' => $total,
            'percent' => $total ? floor($n / $total * 100) : 100,
        );

    }

}
