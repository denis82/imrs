<?php

class AuditController extends CListController
{

	public $name = 'Мини-аудиты';
	public $description = '';
	public $type = 'Audit';
	public $order = 'id asc';
	public $errors = array();

	public function getColumns($columns = array())
	{
		return array(
			'domain.ru_domain',
			'name',
			array(
				'class' => 'CAdminButtonColumn',
				'template' => '{report} {remove}',
				'buttons' => array(
					'report' => array(
						'label' => 'Мини-аудит',
						'icon' => 'star',
						'color' => 'blue',
						'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/report", array("id"=>$data->id))',
					),
					'update' => array(
                        'label' => 'Изменить',
                        'icon' => 'edit',
                        'color' => 'purple',
                        'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/update", array("id"=>$data->id))',
                    ),
					'remove' => array(
						'label' => 'Удалить',
						'icon' => 'trash',
						'color' => 'red',
						'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/remove", array("id"=>$data->id))',
						'class' => 'remove-element',
					),
				),
				'htmlOptions' => array('style' => 'width:270px;'),
			)
		);
	}

	public function getForm($element)
	{
		return array(
			'rows' => array(
				'Параметры проекта для аудита',
				array(
					'domain' => array('type' => 'text'),
				),
				array(
					'name' => array('type' => 'text'),
				),
				array(
                    'regions' => array('type' => 'multiselect', 'items' => $this->getRegions()),
                ),
				array(
					'keywords' => array(
						'type' => 'keywords', 
						'link' => Yii::app()->createUrl($this->module->id . '/' . $this->id . '/addkeyword'),
						'data' => $this->getDataKeywords()
					)
				)
			),
		);
	}
	
	private function getDataKeywords(){
		return array('test');
	}
	
    private function getRegions()
    {
        $content = file_get_contents(dirname(__FILE__) . "/../files/regions.txt");
        $rows = explode("\r\n", $content);
        $result = array();
        foreach ($rows as $row) {
            $r = explode("\t", $row);
            $result[$r[0]] = $r[1];
        }
        return $result;
    }
	
	public function actionAddkeyword($id=false){
		Yii::import('application.modules.seo.models.*');
		
		$json = array('success'=>false);
		
		$success = false;
		$error = false;
		$elements = array();
		
		if(isset($_POST['value'])){
		
			$keywords = Keywords::add($_POST['value']);
			
			if($keywords){
				
				foreach($keywords as $keyword) 
					$elements[$keyword->id] = $keyword->keyword;
				
				$success = true;
				
			}else $error = 'Правильных ключевых фраз в запросе не найдено';
			
		}else $error = 'Запрос отправлен без данных POST';
		
		echo CJSON::encode(array('success'=>$success,'error'=>$error,'elements'=>$elements));
		
		Yii::app()->end();
	}
	
	public function actionRemovekeyword($id){
		
		Yii::import('application.modules.seo.models.*');
		
		$success = false;
		$error = false;
		
		$auditKeywords = AuditKeywords::model()->findByPk($id);
		if($auditKeywords!==NULL) $auditKeywords->delete();
		
		$success = true;
		
		echo CJSON::encode(array('success'=>$success,'error'=>$error));
		
		Yii::app()->end();
		
	}

	public function actionTest(){

		Yii::import('application.modules.seo.models.*');

		//$solomono = Solomono::check(55599, '2015-02-25', true);
		//var_dump($solomono);

		//$whois = Whois::check(55611, '2015-02-25', true);
		//var_dump($whois);

		Yii::app()->end();
	}
	
	public function actionReport($id, $date=false)
	{
		Yii::import('application.modules.seo.models.*');
		
		if(!$date){
			$date = date('Y-m-d');
			$time = time();
			$dateEuropean = date('d.m.Y');
		}else{
			$time = strtotime($date);
			$date = date('Y-m-d',$time);
			$dateEuropean = date('d.m.Y',$time);
		}

		$force  = isset($_REQUEST['force']);
		
		$audit = Audit::model()->with('domain')->findByPk($id);
		
		$region = Yii::app()->request->getParam('region', current($audit->regions));
		
		$keywords = AuditKeywords::model()->with('keyword')->findAllByAttributes(array('audit_id'=>$audit->id));
		
		$positions = YPositions::model()->orderByPositions()->findAll(
			'domain_id=:domain_id and checkdate=:date and region_id=:region',
			array(
				':date' => date('Y-m-d'),
				':region' => $region,
				':domain_id' => $audit->domain_id
			)
		);
		
		$this->title = 'Мини-аудит к ' . $audit->domain->ru_domain;
        $this->breadcrumbs['Мини-аудиты'] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $this->breadcrumbs[] = $this->title;
		
        $audit = Audit::model()->findByPk($id);
		$siteInfo = SiteInfo::check($audit->domain_id, date('Y-m-d'), $force);
		$auditHistory = AuditHistory::model()->audit($audit->id)->findAll();
		$this->render('stats', array(
			'siteInfo' => $siteInfo,
			'audit' => $audit,
			'positions' => $positions,
			'history' => $auditHistory,
			'keywords' => $keywords
		));
	}
	
	public function actionAnchors($id){
	
		Yii::import('application.modules.seo.models.*');
		
		$audit = Audit::model()->findByPk($id);
		$domain = Domain::model()->findByPk( $audit->domain_id );
		
		$date = Yii::app()->request->getParam('date', date('Y-m-d'));
		
		$this->title = 'Статистика анкоров по ' . $domain->ru_domain;
		$this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
		$this->breadcrumbs[] = $this->title;
		
		$anchorCloudStatData = Ahrefs::getAnchorsByAudit($audit->id, $date);
		
		if (Yii::app()->request->isAjaxRequest){
            echo CJSON::encode(array('anchorCloudStatData' => $anchorCloudStatData,'error'=>$error));
            Yii::app()->end();
        } else {
			$this->render('anchors', array('anchorCloudStatData' => $anchorCloudStatData));
		}
		
	}

	public function actionNew(){
		
		Yii::import('application.modules.seo.models.*');
		
		$this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
		$this->title = 'Добавить сайт';
		$this->description = 'Создать новый элемент';
		$this->breadcrumbs[] = $this->title;
		$type = $this->type;

		$model = new $type;
		
		if (isset($_POST[$type])) {
			
			// CHECK WITH HARD CHECKING AND UPDATING
			$domain = Domain::check(SeoUtils::normalizeDomain($_POST[$type]['domain'], false),true);
			
			if($auditExists = Audit::model()->findByAttributes(array('domain_id' => $domain->id))){
				
				$this->errors[] = 'Такой домен уже добавлен в мини-аудиты';
				//$model->errors['domain'][] = 'Такой домен уже добавлен в мини-аудиты.';
				//$this->render('application.views.site.error', array('message' => 'test error'));
				//$model->errors[] = '';
				//var_dump($model->errors);
				//Yii::app()->end();
				$this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/report',array('id'=>$auditExists->id)));
			}else{
			
				$model->attributes = $_POST[$type];
				
				$model->domain_id = $model->attributes['domain_id'] = $domain->id;
				
				if ($model->save()){
				
					$keywordsInsert = [];
					
					if(isset($_POST['keywords']) && count($_POST['keywords'])){
						
						foreach($_POST['keywords'] as $keyword_id) 
							$keywordsInsert[] = array('keyword_id'=>$keyword_id,'audit_id'=>$model->id);
						
					}else{
						/*
						$anchorCloudStatData = Ahrefs::getAnchorsByAudit($audit->id, $date);
						
						var_dump($anchorCloudStatData);
						
						die();
						
						if($anchorCloudStatData && count($anchorCloudStatData)){
							$keywordsCloud = [];
						
							foreach($anchorCloudStatData as $anchorStatData) $keywordsCloud[] = $anchorStatData['anchor'];
							
							//file_put_contents('test.txt', implode(',',$keywordsCloud));
							
							$keywords = Keywords::add($keywordsCloud);
							
							//file_put_contents('test.txt', json_encode($keywords));
							
							if($keywords!==NULL){
								
								foreach($keywords as $keyword) 
									$keywordsInsert[] = ['keyword_id'=>$keyword->id,'audit_id'=>$model->id];
								
								$success = true;
								
							}
						}*/
						
					}
					
					if(count($keywordsInsert)){
                        foreach($keywordsInsert as $keyword)
                        {
                            $ak = new AuditKeywords();
                            $ak->audit_id = $keyword['audit_id'];
                            $ak->keyword_id = $keyword['keyword_id'];
                            $ak->save();
                        }
                        /*
						$builder = Yii::app()->db->schema->commandBuilder;

						$command = $builder->createMultipleInsertCommand(AuditKeywords::model()->tableName(), $keywordsInsert);

						$query = $command->getText();

						$query = str_replace('INSERT ', 'INSERT IGNORE ', $query);

						$command->setText($query);

						//$command = $builder->createMultipleInsertIgnoreCommand(AuditKeywords::model()->tableName(), $keywordsInsert);
						$command->execute();*/
					}
				
					$this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/report',array('id'=>$model->id)));
				}
				
			}
        }
		
		$this->render( 'form', array( 'errors' => $this->errors,'model' => $model, 'formElements' => $this->getForm( $model ) ) );
	}
	
	public function actionKeywords($id){
		
		Yii::import('application.modules.seo.models.*');
		
		$success = false;
		$error = false;
	
		$audit = Audit::model()->findByPk($id);
	
		$auditKeywords = AuditKeywords::model()->with('keyword')->findAllByAttributes(array('audit_id'=>$audit->id));
		
		$keywords = array();
		
		$regions = array();
		
		if($auditKeywords!==NULL && count($auditKeywords)) 
			foreach($auditKeywords as $keywordObject)
				$keywords[] = array(
					'id' => $keywordObject->id,
					'audit_id' => $keywordObject->audit_id,
					'keyword_id' => $keywordObject->keyword_id,
					'keyword' => $keywordObject->keyword->keyword
				);
		
		if(count($keywords)){
			
			foreach ($audit->regions as $regionId) {
				$regions[$regionId] = Region::getByPk($regionId);
				if($regions[$regionId]==NULL) unset($regions[$regionId]);
			}
			
			if(!count($regions)) $error = 'Регионов привязанных к проекту не найдено.';
			else $success = true;
			
		}else $error = 'Ключевых фраз, относящихся к этому аудиту, не найдено.';
		
		echo CJSON::encode(array(
			'regions'=>$regions,
			'keywords'=>$keywords,
			'success'=>$success,
			'error'=>$error,
			'regions_count'=>count($regions),
			'keywords_count'=>count($keywords),
			'memmory_usage'=>memory_get_peak_usage(),
			'real_memmory_usage'=>memory_get_peak_usage(true)
		));
		
		Yii::app()->end();
	}
	
	public function actionPosition($id, $audut_keywords_id = false, $region_id = 0){
	
		Yii::import('application.modules.seo.models.*');
		
		$success = false;
		$error = false;
		
		if(!$audut_keywords_id) $error = 'Не задан';
		else{
			
			$region_id = intval($region_id);
			
			$currentRegion = Region::getByPk($region_id);
			
			if($currentRegion==NULL) $error = 'region not found!';
			else{
				
				$audut_keywords_id = intval($audut_keywords_id);
				
				$auditKeyword = AuditKeywords::model()->with('keyword')->findByPk($audut_keywords_id);
				
				if($auditKeyword==NULL) $error = 'Связь ключевой фразы с аудитом не найдена.';
				else{
				
					$audit = Audit::model()->findByPk($id);
					//$domain = Domain::model()->findByAttributes(array('id'=>$audit->domain_id));
					
					if($audit->analisisYandexPosition($auditKeyword->keyword->keyword, $region_id)){
						$success = true;
					}else{
						$error = 'Не удалось записать нулевое значение.';
					}
					
					
				}
				
				
			}
		}
		
		echo CJSON::encode(array(
			'error'=>$error,
			'success'=>$success,
			'memmory_usage'=>memory_get_peak_usage(),
			'real_memmory_usage'=>memory_get_peak_usage(true)
		));
		
        Yii::app()->end();
	}

	public function actionAudit($id, $date = false){
		
		//error_reporting(E_ALL);
		
		if(!$date){
			$date = date('Y-m-d');
			$time = time();
			$dateEuropean = date('d.m.Y');
		}else{
			$time = strtotime($date);
			$date = date('Y-m-d',$time);
			$dateEuropean = date('d.m.Y',$time);
		}
		
		
		$replaceArray = array();
		
		Yii::import('application.modules.seo.models.*');
		
		$audit = Audit::model()->findByPk($id);
		
		$regionArray = array_slice($audit->regions, 0, 1);
		
		$domain = Domain::model()->findByPk( $audit->domain_id );
		$region = Yii::app()->request->getParam('region', count($regionArray)? $regionArray[0] : 213);
		$siteInfo = SiteInfo::check($audit->domain_id, $date);
		
		$files_path = __DIR__ . '/../files/';
		
		$fileName = $domain->ru_domain . '.' . $dateEuropean . '.docx';
		$sourceFilePath = $files_path . 'docx/source.docx';
		$destinationFilePath = $files_path . 'docx/audit/'.$audit->id.'.docx';
		$destinationXMLPath = $files_path . 'docx/audit/'.$audit->id.'_document.xml';
		$documentWordPathXML = 'word/document.xml';
		$newDocumentXML = $files_path . 'docx/document.xml';
		
		copy($sourceFilePath, $destinationFilePath);
		
		$zip = new ZipArchive();
		$intreval = date_diff(date_create($siteInfo->created), date_create($date));
		$hosterName = false;
		
		$replaceArray['domain'] = $domain->ru_domain;
		$replaceArray['date'] = $dateEuropean;
		$replaceArray['created'] = $this->f_word_string($siteInfo->created.' (Возраст: '.$intreval->format('%Y г., %M мес., %d дн.').')');
			
		
		$positions = YPositions::model()->findAll(
			'domain_id=:domain_id and checkdate=:date and region_id=:region',
			array(
				':date' => $date,
				':region' => $region,
				':domain_id' => $audit->domain_id
			)
		);
		
		$xmlTable = YPositions::generateXMLTable($audit->id);
		
		$anchorCloudStatData = Ahrefs::getAnchorsByAudit($audit->id, $date);
		
		$anchorCloudString = '';
		
		if( $anchorCloudStatData ){
			$_tempAchorsInfo = array();
			foreach($anchorCloudStatData as $anchorInfo) $_tempAchorsInfo[] = strip_tags($anchorInfo['anchor']) . ' ('.round($anchorInfo['percent']).'%)';
			$anchorCloudString = implode(' ',$_tempAchorsInfo);
			$_tempAchorsInfo = null;
			unset($_tempAchorsInfo);
		}
		
		if( $siteInfo->hoster != '' ){
			$hoster_array = array_reverse(explode('.',$siteInfo->hoster));
			$hosterName = $hoster_array[1] . '.' . $hoster_array[0];
			if(is_numeric($hoster_array[0])) $hosterName = false;
		}
		
		//var_dump($siteInfo->description);
		//var_dump($this->f_word_string($siteInfo->description,250));
		
		$recommends = '';
		
		if($siteInfo->last_modified==0){
			$recommends .= $this->f_recommend_str('Ответ сервера на запрос даты последней модификации документа;');
		}
		if(!$siteInfo->title){
			$recommends .= $this->f_recommend_str('Заголовки страниц (Title);');
		}
		if(!$siteInfo->description){
			$recommends .= $this->f_recommend_str('Мета-теги description;');
		}
		if(!$siteInfo->keywords){
			$recommends .= $this->f_recommend_str('Мета-теги keywords;');
		}
		if(!$siteInfo->yac){
			$recommends .= $this->f_recommend_str('Яндекс-каталог;');
		}
		if(!$siteInfo->yam){
			$recommends .= $this->f_recommend_str('Яндекс-метрика;');
		}
		if(!$siteInfo->yaw){
			$recommends .= $this->f_recommend_str('Яндекс-вебмастер;');
		}
		if(!$siteInfo->ga){
			$recommends .= $this->f_recommend_str('Google-analytics;');
		}
		if(!$siteInfo->gw){
			$recommends .= $this->f_recommend_str('Google-webmaster;');
		}
		if(!$siteInfo->sitemap){
			$recommends .= $this->f_recommend_str('Карта сайта;');
		}
		
		//$recommends .= $this->f_recommend_str('Исходящие ссылки домена;');
		$recommends .= $this->f_recommend_str('Текущие позиции сайта в поисковых системах.');
		
		$replaceArray = array_merge($replaceArray,array(
			'positions' => $xmlTable,
			'hosting' => (( $hosterName )? $this->f_word_yes($hosterName) : $this->f_word_no('не определено')),
			'cms' => ($siteInfo->cms=='Не определено'||$siteInfo->cms=='')? $this->f_word_no('не определено'):$this->f_word_yes(strip_tags($siteInfo->cms)),
			'robots' => ($siteInfo->robots)?$this->f_word_yes('есть'):$this->f_word_no('нет'),
			'sitemap' => ($siteInfo->sitemap)?$this->f_word_yes('есть'):$this->f_word_no('нет'),
			'error404' => ($siteInfo->error404)?$this->f_word_yes('есть'):$this->f_word_no('нет'),
			'last_modified' => ($siteInfo->last_modified==0)?$this->f_word_no('нет'):$this->f_word_string(date('d.m.Y', $siteInfo->last_modified)),
			'title' => $this->f_word_string($siteInfo->title),
			'description' => $this->f_word_string($siteInfo->description,250),
			'keywords' => $this->f_word_string($siteInfo->keywords,250),
			'h1h6' => ($siteInfo->h1h6)?$this->f_word_yes('есть'):$this->f_word_no('нет'),
			'alts' => ($siteInfo->alts)?$this->f_word_yes('есть'):$this->f_word_no('нет'),
			'tic' => $this->f_word_string($siteInfo->tic),
			'pr' => ($siteInfo->pr==NULL)? $this->f_word_no('не присвоен'): $this->f_word_string($siteInfo->pr),
			'yac' => ($siteInfo->yac)? $this->f_word_yes('зарегистрирован'):$this->f_word_no('не зарегистрирован'),
			'yam' => ($siteInfo->yam)? $this->f_word_yes('код метрики найден'):$this->f_word_no('код метрики не найден'),
			'yaw' => ($siteInfo->yaw)? $this->f_word_yes('код подтверждения найден'):$this->f_word_no('код подтверждения не найден'),
			'ga' => ($siteInfo->ga)? $this->f_word_yes('код аналитики найден'):$this->f_word_no('код аналитики не найден'),
			'gw' => ($siteInfo->gw)? $this->f_word_yes('код подтверждения найден'):$this->f_word_no('код подтверждения не найден'),
			'optimized' => ($siteInfo->optimized > 0)? $this->f_word_yes($siteInfo->optimized.'%'):$this->f_word_no('сайт не продвигали'),
			'index_count' => $this->f_word_string($siteInfo->index_count),
			'mr_sites' => $this->f_word_string($siteInfo->mr_sites),
			'ip_sites' => $this->f_word_string($siteInfo->ip_sites),
			'hin' => $this->f_word_string($siteInfo->hin),
			'din' => $this->f_word_string($siteInfo->din),
			'anchors' => $this->f_word_string($siteInfo->anchors),
			'hout' => $this->f_word_string($siteInfo->hout),
			'dout' => $this->f_word_string($siteInfo->dout),
			'anchors_out' => $this->f_word_string($siteInfo->anchors_out),
			'anchor_cloud' => $this->f_word_string($anchorCloudString),
			'recommends' => $recommends
		));
		
		if($zip->open($destinationFilePath) === TRUE){
			$zip->deleteName($documentWordPathXML);
			$newDocumentContent = file_get_contents($newDocumentXML);
			foreach($replaceArray as $key => $value) $newDocumentContent = str_replace('{'.$key.'}',$value,$newDocumentContent);
			$newDocumentContent = str_replace('><', ">\r\n<", $newDocumentContent);
			file_put_contents($destinationXMLPath,$newDocumentContent);
			$zip->addFromString($documentWordPathXML, $newDocumentContent);
			$zip->close();
			
			AuditHistory::create($audit->id, $date);
			///*
			header('Cache-Control: public');
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename='.$fileName);
			header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
			header('Content-Transfer-Encoding: binary');
			readfile($destinationFilePath);
			//*/
			Yii::app()->end();
			
		}
		
		
	}
	
	protected function f_recommend_str($str){
		return preg_replace('/[\t\n\r]+/', '', '
		<w:p w:rsidR="00081916" w:rsidRPr="00AD66A6" w:rsidRDefault="00081916" w:rsidP="00602D8F">
			<w:pPr>
				<w:pStyle w:val="af0"/>
				<w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr>
				<w:spacing w:line="240" w:lineRule="auto"/>
				<w:ind w:left="714" w:hanging="357"/>
			</w:pPr>
			<w:r w:rsidRPr="00AD66A6"><w:t>'.$str.'</w:t></w:r>
			</w:p>
		');
	}
	
	protected function f_word_compatible_string($string){
		//$string = str_replace('&nbsp;',' ',$string);
		//$string = htmlspecialchars_decode($string);
		$string = str_replace('&nbsp;',' ',$string);
		$string = str_replace('&','&amp;',$string);
		$string = str_replace('<','',$string);
		$string = str_replace('>','',$string);
		//$string = str_replace('|','&#124;',$string);
		return $string;
	}
	
	private function f_word_string($string, $limit = false){
		$string = $this->f_word_compatible_string($string);
		if( $limit ) return '<w:r><w:t w:space="preserve">'.mb_substr($string, 0, $limit, 'UTF-8').((mb_strlen($string, 'UTF-8')>$limit)?'...':'').'</w:t></w:r>';
		return '<w:r><w:t w:space="preserve">'.$string.'</w:t></w:r>';
	}
	
	private function f_word_yes($string){
		$string = $this->f_word_compatible_string($string);
		return '<w:r><w:rPr><w:color w:val="00B050"/></w:rPr><w:t w:space="preserve">'.$string.'</w:t></w:r>';
	}
	
	private function f_word_no($string){
		$string = $this->f_word_compatible_string($string);
		return '<w:r><w:rPr><w:color w:val="FF0000"/></w:rPr><w:t>'.$string.'</w:t></w:r>';
	}

}
