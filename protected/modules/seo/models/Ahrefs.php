<?php

/**
 * @property integer $id
 * @property integer $domain_id
 * @property string $hash
 * @property string $date
 */
class Ahrefs extends CActiveRecord {

	//private $domain;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Block the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{ahrefs}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('domain_id, hash', 'required'),
			array('hash, date', 'length', 'max' => 255),
			array('domain_id, hash, date', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'domain' => array( self::HAS_ONE, 'Domain', array( 'id' => 'domain_id' ) ),
			'anchorsCloud' => array( self::HAS_MANY, 'AnchorsCloud', 'ahrefs_id' )
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'domain_id' => 'Идентификатор домена',
			'hash' => 'Хеш',
			'date' => 'Дата'
		);
	}
	
	public static function getAnchorsByProject($id,$date=false){
		//Yii::import('application.modules.seo.models.*');
		$project = Project::model()->findByPk($id);
		$domain = Domain::model()->findByPk($project->domain_id);
		$ahrefs = Ahrefs::model()->find('domain_id=:domain_id',array(':domain_id'=>$project->domain_id));
		if($ahrefs == NULL){
			$ahrefs = new Ahrefs;
			$ahrefs->domain_id = $project->domain_id;
			$ahrefs->hash = AhrefsTools::init()->getHash($domain->ru_domain);
			$ahrefs->date = date('Y-m-d');
			$ahrefs->save();
		}
		return self::getAnchorsByAhrefsId($ahrefs->id,$date);
	}
	
	public static function getAnchorsByAudit($id,$date=false){
		//Yii::import('application.modules.seo.models.*');
		$audit = Audit::model()->findByPk($id);
		$domain = Domain::model()->findByPk($audit->domain_id);
		$ahrefs = Ahrefs::model()->find('domain_id=:domain_id',array(':domain_id'=>$audit->domain_id));
		if($ahrefs == NULL){
			$ahrefs = new Ahrefs;
			$ahrefs->domain_id = $audit->domain_id;
			$ahrefs->hash = AhrefsTools::init()->getHash($domain->ru_domain);
			$ahrefs->date = date('Y-m-d');
			$ahrefs->save();
		}
		return self::getAnchorsByAhrefsId($ahrefs->id,$date);
	}
	
	public static function getAnchorsByAhrefsId($id,$date=false){
	
		$date = ($date)? (is_int($date)?date('Y-m-d',$date):$date) : date('Y-m-d');
		
		if( preg_match( '/([0-9]{2})\.([0-9]{2})\.([0-9]{4})/', $date ) ){
			//$date = $date_match[3] . '-' . $date_match[2] . '-' . $date_match[1];
			
			$date_parts = explode('.',$date);
			$date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
		}
		
		//var_dump($date);
		
		$ahrefs = Ahrefs::model()->findByPk($id);
		
		if($ahrefs == NULL) return false;
		
		$domain = Domain::model()->findByPk( $ahrefs->domain_id );
		$project = Project::model()->find('domain_id=:domain_id',array(':domain_id'=>$domain->id));
		$update = AhrefsUpdates::model()->find('ahrefs_id=:ahrefs_id and date=:date', array(':ahrefs_id'=>$ahrefs->id, ':date'=>$date));
		
		//var_dump($ahrefs);
		
		if( !$update ){
		
			$ahrefsTools = AhrefsTools::init();
			
			$anchorCloud = $ahrefsTools->getAnchorsCloud( $domain->ru_domain );
			
			$anchorCloudData = array();
			
			if( isset( $anchorCloud->Data ) and count( $anchorCloud->Data ) ){
				
				foreach( $anchorCloud->Data as $anchorInfo ){
					
					$anchorCloudData[] = array(
						'ahrefs_id' => $ahrefs->id,
						'anchor' => $anchorInfo->Data
					);
					
					$anchorCloudStatData[$anchorInfo->Data] = array(
						'anchor_id' => 0,
						'ahrefs_id' => $ahrefs->id,
						'date' => date('Y-m-d'),
						'count' => $anchorInfo->DomainsData,
						'percent' => $anchorInfo->PercentageRefdomains
					);
				}
				
				$builder = Yii::app()->db->schema->commandBuilder;
				$command = $builder->createMultipleInsertIgnoreCommand(AnchorsCloud::model()->tableName(), $anchorCloudData);
				$command->execute();
				
				$anchorsCloudResult = AnchorsCloud::model()->findAll( 'ahrefs_id=:ahrefs_id', array( ':ahrefs_id' => $ahrefs->id ) );
				
				if( $anchorsCloudResult and count( $anchorsCloudResult ) ){
					
					foreach($anchorsCloudResult as $anchorsStatData){
						
						$anchorCloudStatData[$anchorsStatData->anchor]['anchor_id'] = $anchorsStatData->id;
						$anchorCloudStatData[$anchorsStatData->anchor]['anchor'] = $anchorsStatData->anchor;
						
					}
					
					$anchorCloudStatData = array_values( $anchorCloudStatData );
					
					$savedStatData = $anchorCloudStatData;
					
					foreach($anchorCloudStatData as $key => $value) unset($anchorCloudStatData[$key]['anchor']);
					
					$command = $builder->createMultipleInsertIgnoreCommand(AnchorsCloudStats::model()->tableName(), $anchorCloudStatData);
					$command->execute();
					
					$anchorCloudStatData = $savedStatData;
					
					$ahrefsUpdate = new AhrefsUpdates;
					$ahrefsUpdate->ahrefs_id = $ahrefs->id;
					$ahrefsUpdate->date = $date;
					$ahrefsUpdate->save();
					
					return $anchorCloudStatData;
				}
				
			}
			
		}else{
			$anchorCloudStatList = AnchorsCloudStats::model()->with(array( 
					'anchor' => array(
						'select' => 'anchor',
						'joinType' => 'LEFT JOIN'
					),
				))->findAll( 't.ahrefs_id=:ahrefs_id and t.date=:date', array( ':date' => $date, ':ahrefs_id' => $ahrefs->id ) );
				
			return self::anchorsToArray($anchorCloudStatList);
		}
		
		return false;
	}
	
	public static function anchorsToArray($anchors=false){
	
		if(!$anchors) return false;
		
		if($anchors && count($anchors)){
		
			$anchorCloudStatData = array();
				
			foreach($anchors as $anchorsStatData){
				$anchorCloudStatData[] = array(
					'id' => $anchorsStatData->id,
					'anchor' => $anchorsStatData->anchor->anchor,
					'ahrefs_id' => $anchorsStatData->ahrefs_id,
					'anchor_id' => $anchorsStatData->anchor_id,
					'date' => $anchorsStatData->date,
					'count' => $anchorsStatData->count,
					'percent' => $anchorsStatData->percent
				);
			}
			
			return $anchorCloudStatData;
		}
		
		return false;
		
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('domain_id', $this->domain_id, true);
		$criteria->compare('hash', $this->hash, true);
		$criteria->compare('date', $this->hash, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
/*
	public function beforeValidate() {
		return parent::beforeValidate();
	}

	public function beforeSave() {
		return parent::beforeSave();
	}

	public function afterFind() {
		return parent::afterFind();
	}
*/

}