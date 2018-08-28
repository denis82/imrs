<?php


class GetCurrentTables {

	public $dump;
	public $renameDump;
	public $arrayInput = [];
	public $arrayJson = [];
	public $arrayHosts = [];
	public $arrayDomainId =[];
///////////////////////////////////////////////////////////////////
// Для автоматизации заполнения адресов для их проверки на изменения
// данный экшен получает дамп двух таблиц с cabinet.seo-experts.com название таблиц в кабинете seo_sites, soe_site_words
// в аудите они переименовываются в tbl_projects_cabinet, tbl_projects_cabinet_words
//////////////////////////////////////////////////////////////////

	public function run() {
	    
		if( $this->dump = $this->getTables() ) {
			  $this->getArrayDomains();
			  //$this->renameTables();                             // если таблицы получены из кабинета то переименовываем их
			  //if( !$this->saveTables()) { return false;}          // далее сохраняем дамп в файлик и в базу
			  $arrayCommon = $this->createArrayForTableLinks();  // получаем массивы с урлами
			  
			  $this->createFields($arrayCommon);		     // форматируем массивы с урлами для записи в целевую таблицу
			  //var_dump($this->arrayJson);
			  //return $this->addFields();				     // записываем в целевую таблицу: урлы с идешниками проектов
		}
		return false;
	}

///////////////////////////////////////////////////////////////////
// 
// Получает sql запрос из кабинета для обновления таблиц
//
//////////////////////////////////////////////////////////////////	
	
	public function getTables() {
	    
		$out = ''; 
		if( $curl = curl_init() ) {
				curl_setopt($curl,CURLOPT_URL, 'http://cabinet.seo-experts.com/login?tablestext=babkamakabka');
				curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
				curl_setopt($curl,CURLOPT_NOBODY,false);
				curl_setopt($curl,CURLOPT_HEADER,false);
				curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
				curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
				$out = curl_exec($curl);
				curl_close($curl);
			} 
		if ( null == $out || '' == $out ) {
			$out = false;
		}
		return $out; 
	}
    
///////////////////////////////////////////////////////////////////
// 
// Переименовывает таблицы 
// 
//////////////////////////////////////////////////////////////////    
    
	public function renameTables() {
	    
		$pattern = '/seo_sites/';
		$replacement = 'tbl_projects_cabinet';
		$resDump = preg_replace($pattern, $replacement, $this->dump);
		$pattern = '/seo_sites_words/';
		$replacement = 'tbl_projects_cabinet_words';
		$resDump = preg_replace($pattern, $replacement, $resDump);
		return $this->renameDump = $resDump;

	}
    
///////////////////////////////////////////////////////////////////
// 
// Выполняет sql запрос на обновление таблиц и далаем хэш с копией на память
// 
//////////////////////////////////////////////////////////////////

	public function saveTables() {
		
		if ( null == $this->renameDump || '' == $this->renameDump ) {
			return false;
		}
		
		if ( file_get_contents( Yii::app()->basePath . '/files/dump/hashsql.txt' ) != md5($this->renameDump) ) {
			file_put_contents( Yii::app()->basePath . '/files/dump/tablesdump.sql' , $this->renameDump );
			file_put_contents( Yii::app()->basePath . '/files/dump/hashsql.txt' , md5($this->renameDump));
			shell_exec ( 'mysql audit -u root -pkho28o9ndqdfwdsia723yz < ' . Yii::app()->basePath . '/files/dump/tablesdump.sql');
		} else {
			return false;
		}
		return true;  
	}

	
///////////////////////////////////////////////////////////////////
// 
//   создает массив с проектами в которые вложены массивы с уникальными ссылками 
//
//////////////////////////////////////////////////////////////////
    
	public function getArrayDomains() {
	    

		$projectModel = Project::model()->findAll();
		foreach ($projectModel as $el) {
			$arrayUrl = parse_url($el->host); 
			$this->arrayHosts[$el->domain_id] = $arrayUrl['host'];
		}
		return true;
	}
///////////////////////////////////////////////////////////////////
// 
//   создает массив с проектами в которые вложены массивы с уникальными ссылками 
//
//////////////////////////////////////////////////////////////////
    
	public function createArrayForTableLinks() {
	    

		$projectModel = ProjectsCabinet::model()->with('projects_cabinet_words')->findAll();
		$arrayCommon = [];
		foreach ($projectModel as $el) {
			
			$arrayTemp = [];
			foreach($el->projects_cabinet_words as $res) {
				if( !in_array($res->url, $arrayTemp) and '' != $res->url and '/' != $res->url) { 
					
					$arrayTemp[] = $res->url;
				}
			}
			if ( !empty($arrayTemp) ) {
				$arrayCommon[$el->id]['urls'] = $arrayTemp;
				$arrayCommon[$el->id]['host'] = $el->url;
			}
		}
		return $arrayCommon;
	}
    
	public function createFields($array) {
	
		$projectModel = ProjectsCabinet::model()->with('projects_cabinet_words')->findAll();
		$arrayInput = [];
		$arrayJson = [];
		foreach ($array as $key => $ar) {
			$arrayJson[$key]['urls'] = json_encode($ar['urls']);
			$arrayInput[$key]['urls'] = implode("\n", $ar['urls']);
			$arrayJson[$key]['host'] = $ar['host'];
			$arrayInput[$key]['host'] = $ar['host'];
		}
		$this->arrayInput = $arrayInput;
		$this->arrayJson = $arrayJson;
	}
    
        public function addFields() {
	
		$result = false;
		foreach ($this->arrayJson as $id => $string) {
			var_dump($this->getDomainId($id['host']));
			if ( null == ReportErrorsLinks::model()->findByAttributes(array('domain_id' => $id)) ) {
				/*$modelReportErrorsLinks = new ReportErrorsLinks();
				$modelReportErrorsLinks->domain_id = $this->getDomainId($id['host']);
				$modelReportErrorsLinks->path = $this->arrayJson[$id]['urls'];
				$modelReportErrorsLinks->path_to_form = $this->arrayInput[$id]['urls'];
				if ( $modelReportErrorsLinks->save() ) {
					$result = true;
				}*/
			}
		}
		return $result;
	}
	
	public function getDomainId($host) {
		
		foreach ($this->arrayHosts as $key => $ar) {
			if ( $host == $ar) {
				return $key;
			}
		}
		

	}
// echo "<pre>"; 
//         var_dump($arrayCommon);
//                 echo "</pre>";
    
}