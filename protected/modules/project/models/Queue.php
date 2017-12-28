<?php

class Queue extends CActiveRecord {

// 	public static $STAGE = array(
// 		 1 => 'domain.mainpage',
// 		 2 => 'project.favicon',
// 		 3 => 'project.screenshot',
// 		 4 => 'domain.whois',
// 		 5 => 'domain.wayback',
// 		 6 => 'domain.whoip',
// 		 7 => 'domain.httpheader',
// 		 8 => 'domain.robots',
// 		 9 => 'domain.sitemap',
// 		10 => 'domain.sitemapstructure',
// 
// 		11 => 'domain.cms',
// 		12 => 'domain.counters',
// 
// 		13 => 'domain.uniq',
// 		14 => 'domain.mention',
// 
// 		15 => 'domain.ssl',
// 		16 => 'domain.pagespeed',
// 		17 => 'domain.spell',
// 		18 => 'domain.responsetime',
// 		19 => 'domain.tic',
// 
// 		20 => 'domain.phrase',
// 
// 		21 => 'project.yastruct',
// 
// 		9000 => 'domain.crawler',
// 		9001 => 'domain.pagerank',
// 		9002 => 'domain.yandexmap', 
// 	);
	public static $STAGE = array(
		 1 => 'domain.mainpage',
		 2 => 'project.favicon',
		 3 => 'project.screenshot',
		 4 => 'domain.whois',
		 5 => 'domain.wayback',
		 6 => 'domain.whoip',
		 7 => 'domain.httpheader',
		 8 => 'domain.robots',
		 9 => 'domain.sitemap',
		 10 => 'project.yastruct',
		 //11 => 'domain.yandexmap',
		 11 => 'domain.sitemapstructure',

		 12 => 'domain.cms',
		 13 => 'domain.counters',

		14 => 'domain.uniq',
		15 => 'domain.mention',

		16 => 'domain.ssl',
		17 => 'domain.pagespeed',
		18 => 'domain.spell',
		19 => 'domain.responsetime',
		20 => 'domain.tic',

		21 => 'domain.phrase',

		

		9000 => 'domain.crawler',
		9001 => 'domain.pagerank',
		 
	);
// 	public static $STAGE_DESC = array(
// 		 1 => 'проверка домена',
// 		 2 => 'поиск favicon',
// 		 3 => 'создание скриншотов',
// 		 4 => 'проверка WhoIs',
// 		 5 => 'поиск истории',
// 		 6 => 'проверка хостинга',
// 		 7 => 'проверка HTTP-ответа',
// 		 8 => 'анализ robots.txt',
// 		 9 => 'анализ sitemap',
// 		10 => 'анализ страниц сайта',
// 
// 		11 => 'проверка на CMS',
// 		12 => 'поиск счетчиков',
// 
// 		13 => 'проверка текстов на уникальность',
// 		14 => 'поиск упоминания домена',
// 
// 		15 => 'проверка SSL',
// 		16 => 'проверка скорости загрузки страниц',
// 		17 => 'проверка орфографии страниц',
// 		18 => 'измерение времени ответа сервера',
// 		19 => 'определение тИЦ сайта',
// 
// 		20 => 'анализ слов и фраз',
// 
// 		21 => 'поиск страниц сайта в яндексе',
// 
// 		9000 => 'поиск всех страниц сайта',
// 		9001 => 'расчет веса страниц',
// 	);
	
		public static $STAGE_DESC = array(
		 1 => 'проверка домена',
		 2 => 'поиск favicon',
		 3 => 'создание скриншотов',
		 4 => 'проверка WhoIs',
		 5 => 'поиск истории',
		 6 => 'проверка хостинга',
		 7 => 'проверка HTTP-ответа',
		 8 => 'анализ robots.txt',
		 9 => 'анализ sitemap',
		 10 => 'поиск страниц сайта в яндексе',
		// 11 => 'анализ страниц сайта яндекс',
		11 => 'анализ страниц сайта',

		12 => 'проверка на CMS',
		13 => 'поиск счетчиков',

		14 => 'проверка текстов на уникальность',
		15 => 'поиск упоминания домена',

		16 => 'проверка SSL',
		17 => 'проверка скорости загрузки страниц',
		18 => 'проверка орфографии страниц',
		19 => 'измерение времени ответа сервера',
		20 => 'определение тИЦ сайта',

		21 => 'анализ слов и фраз',

		

		9000 => 'поиск всех страниц сайта',
		9001 => 'расчет веса страниц',
	);
	
	private $_model = null;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{queue}}';
    }

    public function rules() {
    	return array(
            array('id, object_type, object, stage, date, updated_date, status', 'safe'),
        );
    }

    public function relations() {
        return array(
        );
    }

    public function attributeLabels(){
        return array(
            'id' => 'ID',
        );
    }

    public function beforeSave() {
        if ($this->isNewRecord) {
        	$this->date = new CDbExpression('NOW()');
        }

        return parent::beforeSave();
    }

    private function pingDir() {
    	return Yii::getPathOfAlias('root') . '/../logs/ping';
    }

    private function getObject() {
    	if (!$this->_model) {
	    	$name = $this->object_type;
	    	$this->_model = $name::model()->findByPk( $this->object_id );
    	}

    	return $this->_model;
    }

    public function actionParams() {
    	$model = $this->getObject();

    	$action = self::$STAGE[ $this->stage ];
    	//var_dump('actionParams:');
        
    	return array(
    		'action' => str_replace('.', '', $action),
    		'model' => $model,
    	);
    }

    public function isAllowed() {
    	$model = $this->getObject();

    	if ($model and $model->id) {

    		if ($model instanceof Project) {
    			$filename = $this->pingDir() . '_Project_' . $model->id; 
    			//var_dump(time() - filemtime($filename));
    			if (file_exists($filename) and abs(time() - filemtime($filename)) < 3 * 60) {    				
                           // var_dump('isAllowed1:');
                           // var_dump( $filename);
                            return false;
    			}

    			$model = $model->domain;
    		}

    		if ($model instanceof Domain) {
    			$filename = $this->pingDir() . '_Domain_' . $model->id; 
                        
                        
                        
    			if (file_exists($filename) and abs(time() - filemtime($filename)) < 3 * 60) {
                               // var_dump('isAllowed2:');
                               // var_dump( $filename);
    				return false;
    			}
    		}

    	}
    	
    	return true;
    }

    public function ping() {
    	$model = $this->getObject();

    	if ($model and $model->id) {
			$filename = $this->pingDir() . '_' . get_class($model) . '_' . $model->id; 
			touch($filename);
		}
    }

    public function pingRemove() {
    	$model = $this->getObject();

    	if ($model and $model->id) {
			$filename = $this->pingDir() . '_' . get_class($model) . '_' . $model->id; 
			@unlink($filename);
		}
    }

    public function fail() {
    	$this->status = 0;
    	$this->updated_date = new CDbExpression( 'now()' );
    	$this->save();

    	$this->pingRemove();
    }

    public function success() {
    	$this->pingRemove();

    	$this->status = 1;
    	$this->save();
    }

    public function stageDesc() {
    	return self::$STAGE_DESC[ $this->stage ];
    }

    public function stageProgress() {
    	if ($this->stage == 10) {
    		$model = $this->getObject();

    		if ($model instanceof Project) {
    			$model = $model->domain;
    		}

    		if ($model instanceof Domain) {
	    		$total = Sitemap::model()->countByAttributes(array('domain_id' => $model->id));
	    		$progress = Sitemap::model()->countByAttributes(array('domain_id' => $model->id, 'status' => 0));

	    		if ($total) {
		    		$num = round( (1 - $progress / $total) * 100 );
		    		return $num . '% (' . ($total - $progress) . ' из ' . $total . ')';
	    		}
    		}

    	}

    	elseif ($this->stage == 9000) {
    		$model = $this->getObject();

    		if ($model instanceof Project) {
    			$model = $model->domain;
    		}

    		if ($model instanceof Domain) {
	    		$total = CrawlerPage::model()->countByAttributes(array('domain_id' => $model->id));
	    		$progress = CrawlerPage::model()->countByAttributes(array('domain_id' => $model->id, 'check' => 0));

	    		if ($total) {
		    		/*$num = round( (1 - $progress / $total) * 100 );
		    		return $num . '% (в очереди ' . $progress . ', найдено ' . $total . ')';*/
		    		return '(в очереди ' . $progress . ', найдено ' . $total . ')';
	    		}
    		}
    	}
    	elseif ($this->stage == 9001) {
    		$model = $this->getObject();

    		if ($model instanceof Project) {
    			$model = $model->domain;
    		}

    		if ($model instanceof Domain) {
	    		$total = CrawlerPage::model()->countByAttributes(array('domain_id' => $model->id));

				/*$progress = Yii::app()->db->createCommand()
				    ->select('count(*)')
				    ->from('tbl_crawler_page_rank r')
				    ->join('tbl_crawler_page as p using (id)')
				    ->where('p.domain_id = ' . $model->id and r.`in` > 0 and r.`out` > 0')
				    ->queryScalar();*/

				$progress = Yii::app()->db->createCommand('SELECT count(*) 
					FROM tbl_crawler_page_rank as r
						INNER JOIN  tbl_crawler_page as p using (id)
					WHERE p.domain_id = ' . $model->id . ' and (r.`in` > 0 or r.`out` > 0)'
				)->queryScalar();

	    		if ($total) {
		    		$num = round( ($progress / $total) * 100 );
		    		return $num . '% (' . $progress . ' из ' . $total . ')';
	    		}
    		}
    	}

    }

    public static function StartCheck( $model, $stage ) {
    	if (!is_numeric($stage)) {
	    	$n = array_search($stage, self::$STAGE);
		}

    	if (!$stage) {
    		return false;
    	}

    	$c = new self;
    	$c->object_type = get_class($model);
    	$c->object_id = $model->id;
    	$c->stage = $stage;
    	$c->save();

    	return $c;
    }

    public function findStageForProject( $model, $stage ) {
        $criteria = new CDbCriteria;
        $criteria->condition = '((object_type = :prj and object_id = :prj_id) or (object_type = :dmn and object_id = :dmn_id)) and stage = :stage and status=1';
        $criteria->params = array(
        	'prj' => 'Project',
        	'prj_id' => $model->id,
        	'dmn' => 'Domain',
        	'dmn_id' => $model->domain_id,
        	'stage' => $stage,
        );
        $criteria->order = '`date` desc';

        return self::model()->find( $criteria );
    }

}