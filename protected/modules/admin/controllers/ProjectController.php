<?php

class ProjectController extends CListController
{

    public $name = 'Проекты';
    public $description = '';
    public $type = 'Project';
    public $order = 'id asc';

    public function getColumns($columns = array())
    {
        return array(
            'name',
            'host',
            array(
                'class' => 'CAdminButtonColumn',
                'template' => '{price} {report} {update} {remove}',
                'buttons' => array(
				/*
					'stats' => array(
						'label' => 'Мини-аудит',
						'icon' => 'star',
						'color' => 'green',
						'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/stats", array("id"=>$data->id))',
                    ),
				*/
                    'report' => array(
                        'label' => 'Отчет',
                        'icon' => 'calendar',
                        'color' => 'blue',
                        'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/report", array("id"=>$data->id))',
                    ),
					'price' => array(
                        'label' => 'Цены',
                        'icon' => 'briefcase',
                        'color' => 'yellow',
                        'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/price", array("id"=>$data->id))',
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
                'htmlOptions' => array('style' => 'width:350px;'),
            )
        );
    }

    public function getForm($element)
    {
        return array(
            'rows' => array(
                'Параметры проекта',
                array(
                    'name' => array('type' => 'text'),
                ),
                array(
                    'host' => array('type' => 'text'),
                ),
                array(
                    'keywords' => array('type' => 'tags'),
                ),
                //array(
                //    'alltags' => array('type' => 'textarea'),
                //),
                array(
                    'regions' => array('type' => 'multiselect', 'items' => $this->getRegions()),
                ),
            ),
        );
    }

    private function getRegions()
    {
        $content = file_get_contents(dirname(__FILE__) . '/../files/regions.txt');
        $rows = explode("\r\n", $content);
        $result = array();
        foreach ($rows as $row) {
            $r = explode("\t", $row);
            $result[$r[0]] = $r[1];
        }
        return $result;
    }
	
    public function actionIndex(){
		//die('INDEX CONTROLLER');
	
        $this->title = $this->name;
        $this->description = $this->description;
        $this->breadcrumbs[] = $this->title;
        $criteria = new CDbCriteria();
        if (isset($this->order))
            $criteria->order = $this->order;
        if (Yii::app()->request->getParam($this->type . '_sort', "") != "") {
            $criteria->order = str_replace(".", " ", Yii::app()->request->getParam($this->type . '_sort'));
        }

        $criteria->condition .= (($criteria->condition) ? " and " : "") . "user_id=:user_id";
        $criteria->params["user_id"] = Yii::app()->user->id;

        foreach ($this->baseFilters as $key => $value) {
            $criteria->condition .= (($criteria->condition) ? " and " : "") . "$key=:$key";
            $criteria->params["$key"] = $value;
        }
        $filtered = false;
        foreach ($this->filters as $key => $filter) {
            if (Yii::app()->request->getParam($key)) {
                $filtered = true;
                $criteria->condition .= (($criteria->condition) ? " and " : "") . "$key=:$key";
                $criteria->params["$key"] = Yii::app()->request->getParam($key);
            }
        }
        if (!$filtered) {
            foreach ($this->filters as $key => $filter) {
                if ($filter["default"]) {
                    $criteria->condition .= (($criteria->condition) ? " and " : "") . "$key=:$key";
                    $criteria->params["$key"] = $filter["value"];
                }
            }
        }
        $this->order = $criteria->order;
        $dataProvider = new CActiveDataProvider($this->type, array('pagination' => array('pageSize' => 10), 'criteria' => $criteria));
        $this->render('list', array('list' => $dataProvider));
    }

	public function actionUpdateprice($id){
		Yii::import('application.modules.seo.models.*');
        $project = Project::model()->findByPk($id);
		
		$success = false;
		$error = false;
		
		if(isset($_POST['region'],$_POST['price'])){
		
			$post = $_POST;
			$priceList = $post['price'];
			$regionId = intval($post['region']);
			
			if(count($priceList)){
				foreach($priceList as $keywordId => $keywordPrice){
					$keywordPriceObj = KeywordsPrice::model()->findByAttributes(array('keyword_id'=>$keywordId,'region_id'=>$regionId));
					if($keywordPriceObj == NULL){
						$keywordPriceObj = new KeywordsPrice;
						$keywordPriceObj->keyword_id = $keywordId;
						$keywordPriceObj->region_id = $regionId;
					}
					$keywordPriceObj->price = floatval($keywordPrice);
					$keywordPriceObj->save();
				}
				$success = true;
			}else $error = 'price of keywords not found';
		}else $error = 'post dont found.'.gettype($_POST['region']).','.gettype($_POST['price']);
		
		
		echo CJSON::encode(array(
			'success' => $success,
			'error' => $error
		));
		
		Yii::app()->end();
	}
	
	public function actionPrice($id){
		Yii::import('application.modules.seo.models.*');
        $project = Project::model()->findByPk($id);
        $this->title = 'Цены по ключевым словам ' . $project->name;
        $this->breadcrumbs['Проекты'] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $this->breadcrumbs[] = $this->title;
		
		$regions = array();
        foreach ($project->regions as $regionId) {
            $regions[$regionId] = Region::getByPk($regionId);
        }
        $region = Yii::app()->request->getParam('region', current($project->regions));
		
		$keywordsList = array();
		$keywordsInsertList = array();
		$keywordsIdList = array();
		
		foreach(explode(',', $project->keywords) as $keyword){
			$keywordObject = Keywords::addOnce($keyword);
			$keywords_list[] = $keywordObject;
			
			$keywordsIdList[] = $keywordObject->id;
			
			$keywordsInsertList[] = array(
				'project_id' => $project->id,
				'keyword_id' => $keywordObject->id
			);
		}
		
		if($keywordsInsertList){
			$builder = Yii::app()->db->schema->commandBuilder;
			$command = $builder->createMultipleInsertIgnoreCommand(ProjectsKeywords::model()->tableName(), $keywordsInsertList);
			$command->execute();
		}
		
		$projectKeywords = ProjectsKeywords::model()->with('keyword')->findAllByAttributes(array('project_id'=>$project->id));
		
		$keywordsPriceData = KeywordsPrice::model()->findAllByAttributes(array('region_id'=>$region, 'keyword_id'=>$keywordsIdList));
		
		$keywordsPrice = array();
		
		foreach($keywordsPriceData as $key => $obj){
			$keywordsPrice[$obj->keyword_id] = $obj;
			$keywordsPriceData[$key] = NULL; // clear part of memmory collection's
		}
		
		$this->render('price', array(
            'list' => false,
			'regions' => $regions,
			'region' => $region,
			'keywords' => $projectKeywords,
			'price' => $keywordsPrice,
			'project' => $project
        ));
	}
	
	public function actionWordstat($id = 0){
		
		if($id == 0) Yii::app()->end();
	
		Yii::import('application.modules.seo.models.*');
		
        $project = Project::model()->with('keywordsid.keyword')->findByPk($id);
		
		if($project == NULL) return false;
		
		$regions = array();
        foreach ($project->regions as $regionId) {
            $regions[$regionId] = Region::getByPk($regionId);
        }
		
		$regionIdList = array_keys($regions);
		
		//var_dump($project->keywordsid);
		
		$keywordsList = array();
		$keywordsListId = array();
		
		foreach($project->keywordsid as $keywordIdObj){ 
			$keywordsList[] = $keywordIdObj->keyword->keyword;
			$keywordsListId[] = $keywordIdObj->keyword->id;
		}
		
		$keywordsListChunked = array_chunk($keywordsList, 10);
		
		$projectWordstatInsert = array();
		
		if($client = $this->wordstatClient()){
			foreach($keywordsListChunked as $__keywordsList){
				//foreach($__keywordsList as $__keyword)
				foreach($regionIdList as $__regionId){
				
					$reportId = $client->CreateNewWordstatReport(array(
						'Phrases' => $__keywordsList,
						'GeoID' => array( $__regionId )
					));
					
					if(is_int($reportId) && $reportId > 0){
						
						if($wordstat = WordstatReport::add($reportId, $__regionId)){
							$projectWordstatInsert[] = array( 'project_id' => $project->id, 'wordstat_report_id' => $wordstat->id );
						}
						
					}
					
					sleep(2); // sleeping for light parsing
				}
			}
			
			if(count($projectWordstatInsert)){
				$builder = Yii::app()->db->schema->commandBuilder;
				$command = $builder->createMultipleInsertIgnoreCommand(ProjectsWordstat::model()->tableName(), $projectWordstatInsert);
				$command->execute();
			}
			
		}
		
		Yii::app()->end();
		
		
	}
	
	public function wordstatClient(){
		$certPath = Yii::getPathOfAlias('application.modules.seo.files.certs');
		$wsdlurl = 'https://api.direct.yandex.ru/wsdl/v4/';
		
		$certificate = $certPath . DIRECTORY_SEPARATOR . 'solid-cert.crt';
		
		ini_set('soap.wsdl_cache_enabled', '0');
		
		$client = new SoapClient($wsdlurl,
			array(
				'trace'=> 1,
				'exceptions' => 0,
				'encoding' => 'UTF-8',
				'local_cert' => $certificate,
				'passphrase' => ''
			)
		);
		
		$result = $client->PingAPI();
		
		if (is_soap_fault($result)){
			return false;
		}else{
			return $client;
		}
	}
	
	public function actionList(){
		$certPath = Yii::getPathOfAlias('application.modules.seo.files.certs');
		$wsdlurl = 'https://api.direct.yandex.ru/wsdl/v4/';
		
		$certificate = $certPath . DIRECTORY_SEPARATOR . 'solid-cert.crt';
		
		ini_set("soap.wsdl_cache_enabled", "0");
		
		$client = new SoapClient($wsdlurl,
			array(
				'trace'=> 1,
				'exceptions' => 0,
				'encoding' => 'UTF-8',
				'local_cert' => $certificate,
				'passphrase' => ''
			)
		);
		
		//GetWordstatReportList
		
		$result = $client->PingAPI();
		
		if (is_soap_fault($result)){
			trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring}, detail: {$result->detail})", E_USER_ERROR);
		}else{
			$result = $client->GetWordstatReportList(array());
			print_r($result);
		}
		
		Yii::app()->end();
	}
	
    public function actionReport($id, $format = 0)
    {
		
		$daysData = array(
			'week' => 7,
			'month' => 30,
			//'year' => 365
		);
		
		$formatData = array(
			'xlsx','pdf'
		);
		
		if(!isset($formatData[$format])) $format = 0;
		
        Yii::import('application.modules.seo.models.*');
        $project = Project::model()->findByPk($id);
        $this->title = 'Отчет по ' . $project->name;
        $this->breadcrumbs['Проекты'] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $this->breadcrumbs[] = $this->title;
        $regions = array();
        foreach ($project->regions as $regionId) {
            $regions[$regionId] = Region::getByPk($regionId);
        }
        $region = Yii::app()->request->getParam('region', current($project->regions));
		
		$keywords = $project->getKeywordsData();
		
        $date = Yii::app()->dateFormatter->formatDateTime(Yii::app()->request->getParam('date', date("Y-m-d")), 'medium', false);
        $rows = array();
        $dates = array();
        for ($i = 0; $i < 10; $i++) {
            $dates[date('Y-m-d', strtotime($date) - ($i) * 3600 * 24)] = $i;
        }
        foreach ($keywords as $keyword_id => $keyword) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'domain_id=:domain and region_id=:region and keyword_id=:keyword_id';
            $criteria->params = array('domain' => $project->domain_id, 'region' => $region, 'keyword_id' => $keyword_id);
            $criteria->order = "checkdate asc";
            $criteria->addInCondition('checkdate', array_keys($dates));
            $query = YPositions::model()->findAll($criteria);
            $row = array_fill(0, 10, 0);
            $qinfo = null;
            foreach ($query as $q) {
                if (array_key_exists($q->checkdate, $dates)) {
                    $qinfo = $q;
                    $row[$dates[$q->checkdate]] = $q->position;
                }
            }
            $rows[$keyword_id] = $row;
            $info[$keyword_id] = $qinfo;
        }

        //Конкуренты
		/*
        $keywords = explode(',', $project->keywords);
        $keywordId = Yii::app()->request->getParam('keyword', 0);
        $keyword = $keywords[$keywordId];
		*/
		
		$keywordsIdList = array_keys($keywords);
        $keywordId = Yii::app()->request->getParam('keyword', $keywordsIdList[0]);
        $keyword = $keywords[$keywordId];
		
        $criteria = new CDbCriteria();
        $criteria->condition = 'keyword_id=:keyword_id and checkdate=:date and region_id=:region';
        $criteria->params = array('keyword_id' => $keywordId, 'date' => date('Y-m-d', strtotime($date)), 'region' => $region);
        $criteria->order = 'position';

        $rivals = new CActiveDataProvider('YPositions', array('criteria' => $criteria, 'pagination' => array(
            'pageSize' => 2000,)));
        $tab = Yii::app()->request->getParam('tab', 1);

        $siteInfo = SiteInfo::check($project->domain_id, date("Y-m-d", strtotime($date)));

        $this->render("report", array(
            'project' => $project,
            'date' => $date,
            'regions' => $regions,
            'region' => $region,
            'hostinfo' => $siteInfo,
            'statkeys' => $rows,
            'dates' => $dates,
            'tab' => $tab,
            'info' => $info,
            'rivals' => $rivals,
            'keyword' => $keywordId,
			'keywordsData' => $keywords,
			'daysData' => $daysData,
			'days' => $days,
			'format' => $format,
			'formatData' => $formatData
        ));
    }
	
	public function actionXls($id, $type = 'xls', $days = 7){
	
		$daysArray = array(7, 30, 365);
		
		if(!in_array($days, $daysArray)) $days = $daysArray[0];
		
		Yii::import('application.modules.seo.models.*');
		
		$filesPath = Yii::getPathOfAlias('application.modules.seo.files');
		
		$filesPathType = array( 
			'xlsx' => $filesPath . DIRECTORY_SEPARATOR . 'xlsx',
			'pdf' => $filesPath . DIRECTORY_SEPARATOR . 'pdf' 
		);
		
        $project = Project::model()->findByPk($id);
		
		$regions = array();
        foreach ($project->regions as $regionId) {
            $regions[$regionId] = Region::getByPk($regionId);
        }
        $region = Yii::app()->request->getParam('region', current($project->regions));
		
		$date = Yii::app()->dateFormatter->formatDateTime(Yii::app()->request->getParam('date', date('Y-m-d')), 'medium', false);
        $rows = array();
        $dates = array();
        $price = array();
        $names = array();
		$shows = array();
		$keywordsList = array();
		$keywordsObjectsByName = array();
		
        for ($i = 0; $i < $days; $i++){
            $dates[date('Y-m-d', strtotime($date) - ($i) * 3600 * 24)] = $i;
        }
        foreach (explode(',', $project->keywords) as $keyword) {
			$keywordObject = Keywords::addOnce($keyword);
			$keywordPriceObject = KeywordsPrice::model()->findByAttributes(array('keyword_id'=>$keywordObject->id,'region_id'=>$region));
			 // clear collection
		
            $criteria = new CDbCriteria();
            $criteria->condition = 'domain_id=:domain and region_id=:region and keyword=:keyword';
            $criteria->params = array('domain' => $project->domain_id, 'region' => $region, 'keyword' => $keyword);
            $criteria->order = "checkdate asc";
            $criteria->addInCondition('checkdate', array_keys($dates));
            $query = YPositions::model()->findAll($criteria);
            $row = array_fill(0, $days, 0);
            $qinfo = null;
            foreach ($query as $q) {
                if (array_key_exists($q->checkdate, $dates)) {
                    $qinfo = $q;
                    $row[$dates[$q->checkdate]] = $q->position;
                }
            }
			$names[$keywordObject->id] = $keyword;
			$shows[$keywordObject->id] = ($keywordPriceObject!==NULL)? $keywordPriceObject->shows : 0;
            $rows[$keywordObject->id] = $row;
            $info[$keywordObject->id] = $qinfo;
			$price[$keywordObject->id] = ($keywordPriceObject!==NULL)? $keywordPriceObject->price : 0;
			
			//KeywordsPrice::model()->findByAttributes(array('keyword_id'=>$keywordId,'region_id'=>$regionId));
			$keywordObject = NULL;
			$keywordPriceObject = NULL;
        }
		
		//var_dump($price);
		//var_dump($names);
		
		$keywordsIdList = array_keys($keywordsList);
		
		$excelPath = Yii::getPathOfAlias('ext.phpexcel.Classes');
		
		include_once($excelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		
		$excel = new PHPExcel();
		
		$excel->getProperties()
			->setCreator('Maarten Balliauw')
			->setLastModifiedBy('Maarten Balliauw')
			->setTitle('PDF Test Document')
			->setSubject('PDF Test Document')
			->setDescription('Test document for PDF, generated using PHP classes.')
			->setKeywords('pdf php')
			->setCategory('Test result file');
		
		$defaultStyles = &$excel->getDefaultStyle();
		
		$excelFont = $defaultStyles->getFont();
		$excelFont->setName('Calibri');
		$excelFont->setSize(10);
		
		$defaultStyles->getAlignment()->setIndent(1);
		
		// Add some data
		$activeSheet = &$excel->setActiveSheetIndex(0);
		
		ExcelTools::setColumnWidth($activeSheet,1,47);
		ExcelTools::setColumnWidth($activeSheet,2,18);
		ExcelTools::setColumnWidth($activeSheet,3,20);
		
		ExcelTools::setCellCombine($activeSheet,1,2,'Мониторинг позиций за период в поисковой системе Яндекс (Регион: Москва)',array('ALL_BORDER_BOLD','BOLD'));
		$activeSheet->mergeCells(ExcelTools::getCellName(1,2).':'.ExcelTools::getCellName(3,2));
		
		ExcelTools::setCellCombine($activeSheet,1,3,'Ключевое слово/Посадочная страница',array('ALIGN_LEFT','VALIGN_TOP','ALL_BORDER_BOLD','BOLD'));
		ExcelTools::setCellCombine($activeSheet,2,3,'Запросов в месяц/Точных запросов',array('ALIGN_CENTER','VALIGN_TOP','ALL_BORDER_BOLD','BOLD'));
		ExcelTools::setCellCombine($activeSheet,3,3,'Стоимость в месяц/Стоимость за период по позициям',array('ALIGN_CENTER','VALIGN_TOP','ALL_BORDER_BOLD','BOLD'));
		
		ExcelTools::setCellCombine($activeSheet,4,2,'позиции слова по дням',array('ALIGN_CENTER','VALIGN_CENTER','TOP_BLOCK'));
		$activeSheet->mergeCells(ExcelTools::getCellName(4,2).':'.ExcelTools::getCellName(4+count($dates)-1,2));
		
		//$excel->getActiveSheet()->getCell(ExcelTools::getCellName(4,4))->setValue(ExcelTools::valueAdditionalText('',,'GREEN'));
		
		//ExcelTools::setCellCombine($activeSheet,4,4,7,array('FILL_GREEN','ALIGN_CENTER'));
		//ExcelTools::valueAdditionalText(,,'GREEN')
		
		$_foreach_dates_i = 0;
		$_count_dates = count($dates);
		foreach($dates as $d => $key){
			$activeSheet->setCellValue(ExcelTools::getCellName(4+$_foreach_dates_i,3), Yii::app()->dateFormatter->formatDateTime($d, 'medium', false));
			
			$arrayStyles = array('ALL_BORDER_NORMAL', 'VALIGN_TOP', 'ALIGN_CENTER');
			
			if($_foreach_dates_i == $_count_dates-1) $arrayStyles[] = 'RIGHTBOLD';
			
			ExcelTools::setCellCombine($activeSheet,4+$_foreach_dates_i,3,Yii::app()->dateFormatter->formatDateTime($d, 'medium', false),$arrayStyles);
			
			ExcelTools::setColumnWidth($activeSheet,4+$_foreach_dates_i,15);
			
			$_foreach_dates_i++;
        }
		
		$lastCellVertical = 0;
		
		$priceAllMean = 0;
		$priceAllFact = 0;
		
		$_foreach_rows_i = 0;
		foreach($rows as $kid => $positions){
			
			$keyword = $names[$kid];
			
			$verticalPosition = 4 + $_foreach_rows_i;
			
			$lastCellVertical = $verticalPosition + 1;
			
			ExcelTools::setCellCombine($activeSheet,1,$verticalPosition,$keyword,array(
				'RIGHTTOP_BLOCK', 'VALIGN_TOP', 'ALIGN_LEFT'
			));
			
			if (isset($info[$kid]) && $info[$kid]->url != '0')
				$cellValue = $info[$kid]->url;
            else 
				$cellValue = '';
			
			ExcelTools::setCellCombine($activeSheet,1,$verticalPosition+1,$cellValue,array(
				'RIGHTBOTTOM_BLOCK','VALIGN_TOP','ALIGN_LEFT','ITALIC'
			));
			
			$positions = array_reverse(array_values($positions));
			$positionsCount = count($positions);
			
			$currencyMonth = intval($price[$kid]);
			$priceAllMean += $currencyMonth;
			$priceValue = round($currencyMonth/30);
			$currencySumm = 0;
			
			foreach($positions as $key => $pos){
				
				$pos = intval($pos);
				
				$prevPosition = isset($positions[$key-1])? (($positions[$key-1] == 0 || $positions[$key-1] > 100)? 100 : $positions[$key-1]) : NULL;
				
				$currency = $priceValue;
				
				$arrayStyles = array();
				if($key != 0 && $key <= $positionsCount){
					//$value = $pos;
				}else{
					//$value = '';
				}
				$value = $pos;
				if($pos <= 0) $value = '';
				
				if($pos > 0 && $pos <= 10) $arrayStyles['fill'] = 'FILL_GREEN';
				
				if($value == '') $arrayStyles['fill'] = 'FILL_GRAY';
				
				$arrayStyles[] = 'ALIGN_CENTER';
				$arrayStyles[] = 'ALL_BORDER_NORMAL';
				$arrayStyles[] = 'TOPBOLD';
				
				if($key == 0){
					$arrayStyles[] = 'LEFTBOLD';
				}
				if($key == $positionsCount-1) $arrayStyles[] = 'RIGHTBOLD';
				
				$realPos = (($pos == 0 || $pos > 100)? 100 : $pos);
				
				if($realPos != 0 && $realPos < 101 && $prevPosition !== NULL && $prevPosition != $realPos){
					$color = ($realPos <= 10)? 'BLACK' : false;
					if($realPos > $prevPosition) $value = ExcelTools::valueAdditionalText($value, '(-'.($realPos - $prevPosition).')', ($color)? $color : 'RED');
					else $value = ExcelTools::valueAdditionalText($value, '(+'.($prevPosition - $realPos).')', ($color)? $color : 'GREEN');
				}
				
				ExcelTools::setCellCombine($activeSheet,$key+4,$verticalPosition,$value,$arrayStyles);
				
				unset($arrayStyles['fill']);
				
				$arrayStyles[] = 'TOPNORMAL';
				$arrayStyles[] = 'BOTTOMBOLD';
				
				if($pos > 0 && $pos <= 10 && $value != ''){
					$arrayStyles[] = 'CURRENCY_RUB';
					$currency = floatval($currency);
					$currencySumm += $currency;
				}elseif($value == ''){
					$arrayStyles['fill'] = 'FILL_GRAY';
					$currency = '';
				}else $currency = '—';
				
				
				
				ExcelTools::setCellCombine($activeSheet,$key+4,$verticalPosition+1,$currency,$arrayStyles);
			}
			
			$priceAllFact += $currencySumm;
			
			ExcelTools::setCellCombine($activeSheet,3,$verticalPosition,$currencyMonth,array('RIGHTTOP_BLOCK','ALIGN_CENTER','CURRENCY_RUB'));
			ExcelTools::setCellCombine($activeSheet,3,$verticalPosition+1,$currencySumm,array('RIGHTBOTTOM_BLOCK','ALIGN_CENTER','CURRENCY_RUB','BOLD'));
			
			ExcelTools::setCellCombine($activeSheet,2,$verticalPosition,$shows[$kid],array('LEFTTOP_BLOCK','ALIGN_CENTER'));
			ExcelTools::setCellCombine($activeSheet,2,$verticalPosition+1,'',array('LEFTBOTTOM_BLOCK'));
			
			$_foreach_rows_i += 2;
		}
		
		$edningBlockY = $lastCellVertical + 2;
		
		ExcelTools::setCellCombine($activeSheet,2,$edningBlockY,'Итого по договору:',array('LEFTTOP_BLOCK','ALIGN_RIGHT','BOLD'));
		ExcelTools::setCellCombine($activeSheet,2,$edningBlockY+1,'Итого к оплате:',array('LEFTBOTTOM_BLOCK','ALIGN_RIGHT','BOLD'));
		ExcelTools::setCellCombine($activeSheet,3,$edningBlockY,$priceAllMean,array('RIGHTTOP_BLOCK','ALIGN_LEFT','BOLD','CURRENCY_RUB'));
		ExcelTools::setCellCombine($activeSheet,3,$edningBlockY+1,$priceAllFact,array('RIGHTBOTTOM_BLOCK','ALIGN_LEFT','BOLD','CURRENCY_RUB'));
		
		//header('Content-Type: application/pdf');
		//header('Content-Disposition: attachment;filename="01simple.pdf"');
		//header('Cache-Control: max-age=0');
 
		//$objWriter = PHPExcel_IOFactory::createWriter($excel, 'PDF');
		//$objWriter->save('php://output');
		
		
		if($type == 'html'){
			$objWriter = new PHPExcel_Writer_HTML($excel);
		
			echo $objWriter->generateHTMLHeader();
			echo $objWriter->generateStyles();
			echo $objWriter->generateSheetData();
			echo $objWriter->generateHTMLFooter();
		}elseif($type == 'pdf'){
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment;filename="report.pdf"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($excel, 'PDF');
			//$objWriter = new PHPExcel_Writer_PDF($excel);
			//$objWriter->save('php://output');
			$objWriter->save( $filesPathType['pdf'] . DIRECTORY_SEPARATOR . 'file.pdf');
		}else{
			header('Content-disposition: attachment; filename=report.xlsx');
			header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			
			$objWriter = new PHPExcel_Writer_Excel2007($excel);
			$objWriter->save('php://output');
			
			//$objWriter->save( $filesPathType['xlsx'] . DIRECTORY_SEPARATOR . 'file.xlsx');
		}
		
		//spl_autoload_register(array('YiiBase','autoload'));
		
		Yii::app()->end();
		
	}
	
	public function actionAnchors($id){
	
		Yii::import('application.modules.seo.models.*');
		
		$project = Project::model()->findByPk($id);
		$domain = Domain::model()->findByPk( $project->domain_id );
		
		$date = Yii::app()->request->getParam('date', date('Y-m-d'));
		
		$this->title = 'Статистика анкоров по ' . $domain->ru_domain;
		$this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
		$this->breadcrumbs[] = $this->title;
		
		$anchorCloudStatData = Ahrefs::getAnchorsByProject($project->id, $date);
		
		if (Yii::app()->request->isAjaxRequest){
            echo CJSON::encode(array('anchorCloudStatData' => $anchorCloudStatData,'error'=>$error));
            Yii::app()->end();
        } else {
			$this->render('anchors', array('anchorCloudStatData' => $anchorCloudStatData));
		}
		
	}
	

    public function actionNew(){

    	$this->redirect( Yii::app()->createUrl('project/index/new') );

	
        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $this->title = 'Добавить';
        $this->description = 'Создать новый элемент';
        $this->breadcrumbs[] = $this->title;
        $type = $this->type;
        $model = new $type();
        if (isset($_POST[$type])) {
            $model->attributes = $_POST[$type];
            $model->user_id = Yii::app()->user->id;
            if ($model->save()) {
            	ProjectsFavicon::download($model);

                if (!isset($_POST['apply'])){
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/view', array('id' => $model->id)));
                } else {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));
                }
            }
            else {
            	$e = $model->getErrors();
            }
        }

        $this->render('project.new', array(
        	"model" => $model, 
        	'formElements' => $this->getForm($model),
        	'errors' => $e,
        ));
    }

    public function actionUpdate($id)
    {
        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $type = $this->type;
        $this->project = $model = $type::model()->findByPk($id);
        $this->breadcrumbs[] = strip_tags($model->name);
        $this->title = strip_tags($model->name);
        $this->description = 'Изменить элемент';
        if (isset($_POST[$type])) {
            $model->attributes = $_POST[$type];
            if ($model->save()) {
                if (!isset($_POST["apply"])) {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/view', array('id' => $model->id)));
                } else {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));
                }
            }
        }
        $this->render('project.new', array("model" => $model, 'formElements' => $this->getForm($model)));
    }

    public function actionAnalize($id, $pos = -1, $step = 1)
    {
        Yii::import("application.modules.seo.models.*");
        $project = Project::model()->findByPk($id);
        $this->title = "Анализ " . $project->name;
        $this->breadcrumbs["Проекты"] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $this->breadcrumbs[] = $this->title;

        $regions = array();
        foreach ($project->regions as $regionId) {
            $regions[$regionId] = Region::getByPk($regionId);
        }
        $pairs = array();
        foreach (explode(',', $project->keywords) as $keyword) {
            foreach ($project->regions as $region) {
                $pairs[] = array("keyword" => $keyword, "region" => $region);
            }
        }

        if ($pos >= 0){
            $pair = $pairs[$pos];
            switch ($step) {
                case 1:
                    $project->analisisYandexPosition($pair["keyword"], $pair["region"]);

                    break;
                case 2:
                    $project->analisisRivals($pair["keyword"], $pair["region"]);
					
					AuditHistory::create($project->id, date('Y-m-d'));
					
                    break;
            }
        }
        $state = ($pos + 1 == count($pairs)) ? "success" : "progress";
        //Включаем второй шаг проверки
        if ($state == "success" && $step == 1) {
            $pos = -1;
            $step = 2;
            $state = "progress";
        }
        //Подготовка сообщения
        switch ($step) {
            case 1:
                $message = "Анализ позиций по запросу " . $pairs[$pos + 1]["keyword"] . " в регионе " . $regions[$pairs[$pos + 1]["region"]];
                break;
            case 2:
                $message = "Анализ доменов конкурентов по запросу " . $pairs[$pos + 1]["keyword"] . " в регионе " . $regions[$pairs[$pos + 1]["region"]];
                break;
        }
        $percent = ((float)($pos + 1) / (float)(count($pairs))) * 100;
        //Вызов представления
        if (Yii::app()->request->isAjaxRequest) {
            echo CJSON::encode(array("pos" => intval($pos), "message" => $message, "percent" => $percent, "state" => $state, "step" => $step));
            Yii::app()->end();
        } else {
            $this->render("analize", array("pos" => intval($pos), "message" => $message, "percent" => $percent, "state" => $state, "step" => $step));
        }
    }

    public function actionView($id)
    {
        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $type = $this->type;
        $this->project = $model = $type::model()->findByPk($id);
        $this->breadcrumbs[] = strip_tags($model->name);
        $this->title = strip_tags($model->name);

        $this->render('project.view', array("model" => $model, 'formElements' => $this->getForm($model)));
    }

    public function actionInternal($id)
    {
        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $type = $this->type;
        $this->project = $model = $type::model()->findByPk($id);
        $this->title = strip_tags($model->name);
        $this->description = 'Внутренняя оптимизация';
        $this->breadcrumbs[$this->title] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/view', array('id' => $model->id));
        $this->breadcrumbs[] = 'Внутренняя оптимизация';

/*$query = array('Whois' => 'nic.ru');

$opts = array('http' =>
	array(
		'method'  => 'POST',
		'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
		'content' => http_build_query($query),
		'timeout' => 60
	)
);
                        
$context  = stream_context_create($opts);
$url = 'https://'.$https_server;
$result = file_get_contents('http://www.ripn.net/nic/whois/whois.cgi', false, $context);

$match = array();

preg_match_all('/<pre>(.*?)<\/pre>/is', $result, $match);

$data = strip_tags($match[1][0]);
$result = array();

if (strlen($data)) {
	$rows = explode("\n", $data);
	foreach ($rows as $j) {
		$j = trim($j);

		if ($j[0] == '%' or strlen($j) == 0) continue;

		$i = explode(":", $j, 2);

		$i[0] = trim($i[0]);
		$i[1] = trim($i[1]);

		if (strpos($i[0], ' ') === false) {
			$result[] = $i;
		}
	}
}

print_r($result);die();*/

        $scr = $model->screenshot();

        if (!$scr) {
        	$scr = ProjectsScreenshot::download( $model );
        }

        $this->render('project.internal', array(
        	"model" => $model,
        	"screenshot" => $scr,
        	"whois" => $model->domain->whois,
        ));
    }

    public function actionLoad()
    {
    	if (Yii::app()->request->isAjaxRequest) {
    		$model = Project::model()->findByPk( intval(Yii::app()->request->getPost('id')) );
    		$action = 'load' . Yii::app()->request->getPost('info');

    		if ($model and $model->id and method_exists($this, $action)) {
    			return $this->$action( $model );
    		}
    	}

    	$this->renderPartial('error/notfound');

    	Yii::app()->end();
    }

    private function loadWhois( $project ) {
    	$whois = $project->domain->whois;

    	if (!$whois or !count($whois)) {
    		$whois = DomainsWhois::model()->download( $project->domain );
    	}

    	$this->renderPartial('domain/whois', array('data' => $whois));

    	Yii::app()->end();
    }

    private function loadWayback( $project ) {
    	$data = $project->domain->wayback;

    	if (!$data or !count($data)) {
    		$data = DomainsWayback::model()->download( $project->domain );
    	}

    	$this->renderPartial('domain/wayback', array('data' => $data));

    	Yii::app()->end();
    }

    private function loadHostingIp( $project ) {
    	$data = $project->domain->ip;

    	if (!$data or !count($data)) {
    		$data = DomainsIp::model()->download( $project->domain );
    	}

    	if (is_array($data)) {
    		$data = $data[0];
    	}

    	if ($data) {
	    	$whois = IpWhois::model()->findByAttributes(array('ip' => $data->ip), array('order' => 'id desc'));

	    	if (!$whois or !$whois->id) {
	    		$whois = IpWhois::download( $data->ip );
	    	}
    	}

    	$this->renderPartial('hosting/ip', array(
    		'data' => $data,
    		'whois' => $whois,
    	));

    	Yii::app()->end();
    }

    private function loadServerHeaders( $project ) {
    	$data = $project->domain->headers;

    	if (!$data or !count($data)) {
    		$data = DomainsHeaders::model()->download( $project->domain );
    	}

    	if (is_array($data)) {
    		$data = $data[0];
    	}

    	$this->renderPartial('server/headers', array('data' => $data));

    	Yii::app()->end();
    }

}
