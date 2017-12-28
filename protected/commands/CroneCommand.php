<?php

define('REPORTS_LIMIT', 5); // Лимит репортов на сервере Яндекс.Директа
define('WORDS_LIMIT', 10); // Лимит фраз в одном репорте

class CroneCommand extends CConsoleCommand
{

    public function actionStart(){
	
        Yii::import('application.modules.seo.components.*');
        Yii::import('application.modules.seo.models.*');
		
		$pojectsAll = Project::model()->findAll();
		
		if(count($pojectsAll)){
		
			foreach ($pojectsAll as $__i => $project){
			
				foreach (explode(',', $project->keywords) as $keyword){
				
					foreach ($project->regions as $lr){
					
						//echo "$keyword $lr " . $project->name . '  - ';
						$project->analisisYandexPosition($keyword, $lr);
						$project->analisisRivals($keyword, $lr);
						//echo " OK\n";
						
					}
					
				}

				SiteInfo::check($project->domain_id);
				
				$project = NULL;
				$pojectsAll[$__i] = NULL;
			}
		}
		
		$pojectsAll = NULL;
    }
	
	public function actionUpdatestats(){
		
		set_time_limit(0);
		
		Yii::import('application.modules.seo.components.*');
        Yii::import('application.modules.seo.models.*');
		
		if($client = $this->wordstatClient()){
			$keywordsPriceAll = KeywordsPrice::model()->noUpdated()->findAll();
		};
		
		/*
		if($client = $this->wordstatClient()){
			
			$pojectsAll = Project::model()->notUpdated()->with('keywordsid.keyword')->findAll();
			
			foreach($pojectsAll as $project){
			
				if($project == NULL) continue;
				
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
							
							sleep(1); // sleeping for light parsing
						}
					}
					
					if(count($projectWordstatInsert)){
						$builder = Yii::app()->db->schema->commandBuilder;
						$command = $builder->createMultipleInsertIgnoreCommand(ProjectsWordstat::model()->tableName(), $projectWordstatInsert);
						$command->execute();
					}
				
				$projectWordstatInsert = NULL;
			
			}
		
		}
		*/
	}
	
	/*
	public function actionUpdate(){
	
		set_time_limit(0);
		
		Yii::import('application.modules.seo.components.*');
        Yii::import('application.modules.seo.models.*');
		
		$projects = Project::model()->findAll();
		
		foreach($projects as $project){
		
			foreach(explode(',',$project->keywords) as $kw){
			
				$keywordObj = Keywords::addOnce($kw);
				
				if($keywordObj != NULL){
				
					if(!ProjectsKeywords::model()->findByAttributes(array('project_id' => $project->id, 'keyword_id' => $keywordObj->id))){
				
						$projectKeyword = new ProjectsKeywords;
						$projectKeyword->project_id = $project->id;
						$projectKeyword->keyword_id = $keywordObj->id;
						$projectKeyword->save();
						
						$projectKeyword = NULL;
					
					}
				}
				
				$keywordObj = NULL;
			}
			
		}
		
		
	}
	*/
	
	// REPORTS_LIMIT = 5
	// WORDS_LIMIT = 10
	
	public function actionWlist(){
	
		self::startWordstat();
		
		Yii::import('application.modules.seo.components.*');
        Yii::import('application.modules.seo.models.*');
		
		$client = self::wordstatClient();
		
		if(!$client) self::closeWordstat('client not worked');
		
		$connection = Yii::app()->db;
		
		$reportList = $client->GetWordstatReportList(array());
		
		$countReportList = count($reportList);
		
		if($countReportList < REPORTS_LIMIT){
			$countClearReport = REPORTS_LIMIT - $countReportList;
			$countWordsLimit = $countClearReport * WORDS_LIMIT;
			
			$sql = '
			SELECT 
				pkw.*,
				ws.*
			FROM {{projects_keywords}} pkw
			LEFT JOIN {{wordstat_stack}} ws 
				ON ws.keyword_id = pkw.keyword_id 
				AND ws.month = :month
				AND ws.year = :year
			WHERE ws.month != 0 AND ws.month IS NULL
			LIMIT :limit';
			
			
			
			$command = $connection->createCommand($sql);
			$command->bindParam(':month', intval(date('m')), PDO::PARAM_INT);
			$command->bindParam(':year', intval(date('Y')), PDO::PARAM_INT);
			$command->bindParam(':limit', $countWordsLimit, PDO::PARAM_INT);
			
			self::write($command->getText(), 'Query');
			
			$dataReader = $command->query();
			
			$rows = $dataReader->readAll();
			
			self::write(count($rows), 'Count rows');
			
			
			
		}
		
		
		
		//self::closeWordstat();
		
		
       
		
		self::closeWordstat(); //EXIT
	}
	
	public function actionWordstat(){
	
		set_time_limit(0);
		
		self::startWordstat();
		
		Yii::import('application.modules.seo.components.*');
        Yii::import('application.modules.seo.models.*');
		
		$projectWordstatList = ProjectsWordstat::model()->with('wordstat')->findAll();
		
		if(!count($projectWordstatList)) self::closeWordstat(); //EXIT
		
		$client = self::wordstatClient();
		
		if(!$client) self::closeWordstat(); //EXIT
		
		$reportList = $client->GetWordstatReportList(array());
		
		if(!count($reportList)) 
			foreach($projectWordstatList as $__i => $projectWordstat){
				$projectWordstat->wordstat->delete();
				$projectWordstat->delete();
				
				$projectWordstatList[$__i] = NULL; // memory optimization
			}
		
		$statusReportListBinded = array(); 
		
		foreach($reportList as $__report) $statusReportListBinded[$__report->ReportID] = $__report->StatusReport;
		
		foreach($projectWordstatList as $__i => $projectWordstat){
			
			$__regionId = $projectWordstat->wordstat->region_id;
			
			if(isset($statusReportListBinded[$projectWordstat->wordstat->report_id])){
				
				$__status = $statusReportListBinded[$projectWordstat->wordstat->report_id];
				
				if($__status == 'Done'){
					
					$wordstatReport = $client->GetWordstatReport($projectWordstat->wordstat->report_id);
					
					if(count($wordstatReport)){
					
						foreach($wordstatReport as $__oi => $obj){
							
							foreach($obj->SearchedWith as $stat){
							
								if( $stat->Phrase == $obj->Phrase ){
								
									$keyword = Keywords::addOnce($obj->Phrase);
									
									if($keyword){
										$keywordPrice = KeywordsPrice::check($keyword->id, $__regionId);
										$keywordPrice->shows = intval($stat->Shows);
										
										$keywordPrice->save();
										
										$keywordPrice = NULL;
									}
									
									$keyword = NULL;
								}
								
							}
							
							$wordstatReport[$__oi] = NULL; // memory optimization
						}
					}
					
					$client->DeleteWordstatReport($projectWordstat->wordstat->report_id);
					
					$projectWordstat->wordstat->delete();
					$projectWordstat->delete();
					
					$wordstatReport = NULL; // memory optimization
					
				}
				
				$__status = NULL; // memory optimization
				
			}else{
				$projectWordstat->wordstat->delete();
				$projectWordstat->delete();
			}
			
			// memory optimization
			$__regionId = NULL;
			$projectWordstat = NULL; 
			$projectWordstatList[$__i] = NULL;
		}
		
		self::closeWordstat(); //EXIT
	}
	
	private static function getWordstatPidfile(){
		return Yii::getPathOfAlias('application.commands.pid') . DIRECTORY_SEPARATOR . 'wordstat.txt';
	}
	
	private static function startWordstat(){
		if( file_get_contents(self::getWordstatPidfile()) != '' ) exit("\n\n".'pid is exists'."\n\n");
		file_put_contents(self::getWordstatPidfile(), getmypid());
		echo "\n\n".'STARTED'."\n\n";
	}
	
	private static function closeWordstat($message = NULL){
		file_put_contents(self::getWordstatPidfile(), '');
		if($message !== NULL) exit("\n\n".$message."\n\n");
		exit("\n\n".'CLOSED'."\n\n");
	}
	
	private static function write($string, $description = NULL){
		if($description !== NULL) echo "\n" . $description . ' : ' . $string . "\n";
		else echo "\n" . $string . "\n";
	}
	
	private function wordstatClient(){
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

}


