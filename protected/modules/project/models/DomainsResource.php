<?php

class DomainsResource extends CActiveRecord {
    const T_IMAGE = 1;
    const T_CSS = 2;
    const T_SCRIPT = 3;
    const T_FORM = 4;

    public function tableName() {
        return '{{domains_resource}}';
    }

    public function rules() {
        return array(
            array('domain_id, type', 'required'),
            array('domain_id, type', 'numerical', 'integerOnly' => true),
            array('id, domain_id, type, hash, html, url', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'domain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
            'pages_resource' => array(self::HAS_MANY, 'PagesResource', 'domain_resource_id'),
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

    public function hash() {
    	return md5( $this->html );
    }

    public static function getHash( $html ) {
    	return md5( $html );
    }

    public function createIfNotExists( $data ) {

    	$data['hash'] = self::getHash( $data['url'] ? $data['url'] : $data['html'] );

    	$item = self::model()->findByAttributes(array(
    		'domain_id' => $data['domain_id'],
    		'hash' => $data['hash'],
    	));

    	if ($item) {
    		return $item->id;
    	}

        $sql = "INSERT IGNORE INTO " . self::model()->tableSchema->name . " 
        	(domain_id, type, hash, html, url)
        	VALUES 
        		(:domain_id, :type, :hash, :html, :url)
        ";

        $transaction = Yii::app()->db->beginTransaction();

        Yii::app()->db
        	->createCommand($sql)
        	->execute($data);

        $transaction->commit();

        if ( Yii::app()->db->lastInsertID ) {
        	return Yii::app()->db->lastInsertID;
        }

    	$item = self::model()->findByAttributes(array(
    		'domain_id' => $data['domain_id'],
    		'hash' => $data['hash'],
    	));

    	if ($item) {
    		return $item->id;
    	}

    	return 0;

    }

    public function pagesResource() {
        /*$criteria = new CDbCriteria;
        $criteria->alias = 't';
        $criteria->select = 't.*';
        $criteria->join = 'left join tbl_pages p on (p.id = t.page_id)';
        $criteria->condition = 't.domain_resource_id = :id and t.type like :type';
        $criteria->group = 'p.sitemap_id';
        $criteria->order = 't.page_id desc';
        $criteria->params = array(
        	'id' => $this->id,
        	'type' => $this->type
        );

    	return PagesResource::model()->findAll( $criteria );*/

    	return PagesResource::model()->findAllByAttributes(array('domain_resource_id' => $this->id, 'type' => $this->type));
    }

}
