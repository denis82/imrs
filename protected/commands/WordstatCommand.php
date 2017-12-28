<?php

define('REPORTS_LIMIT', 5); // Лимит репортов на сервере Яндекс.Директа
define('WORDS_LIMIT', 10); // Лимит фраз в одном репорте

class WordstatCommand extends CConsoleCommand
{

    public function actionStart(){
		
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
	
	public function actionRegion(){
		
		$connection = Yii::app()->db;
		
		$sql = 'SELECT * FROM {{projects}} p';
		$command = $connection->createCommand($sql);
		//$command->bindParam(':month', intval(date('m')), PDO::PARAM_INT);
		//$command->bindParam(':year', intval(date('Y')), PDO::PARAM_INT);
		//$command->bindParam(':limit', $countWordsLimit, PDO::PARAM_INT);
			
		$dataReader = $command->query();
		
		$rows = $dataReader->readAll();
		
		$count = 0;
		
		foreach($rows as $row){
			//var_dump($row['regions']);
			
			$regionsArray = explode(',',$row['regions']);
			
			foreach($regionsArray as $region_id){
				if( $connection->createCommand('INSERT IGNORE INTO {{projects_regions}} (project_id,region_id) VALUE ('.$row['id'].','.intval($region_id).')')->execute() ){
					$count++;
				}
			}
			
		}
		
		self::write($count, 'Updated rows');
		
		self::write(count($rows), 'Count rows');
		
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