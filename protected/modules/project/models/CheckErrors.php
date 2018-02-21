<?
class CheckErrors
{
    private $url = false; // урл сайта 
    private $domain = false;  // ид домена
    private $zeroStatus = false;  // новый проект или уже существующий 
    private $targetFiles = ['robots.txt','sitemap.xml']; // названия файлов которые нужно прочитать
  
  //const ROBOTS = 'robots.txt';
  //const SITEMAP = 'sitemap.xml';
  
/*
      запускает механизм проверки
*/
  
    public function start($arrayWithModelProjects) {
	
	if ( empty($arrayWithModelProjects) ) {
	    return true;
	}
	
	foreach ( $arrayWithModelProjects as $project ) {
	    
	    if ( !$project->host ) {
		continue;
	    }
	    $this->url = $project->host;
	    $this->domain = $project->domain_id;
	    $this->checkForExistenceOfRecords($project->domain_id);
	    $resulltAvailable = $this->availableSite();
	    $this->availableLoger($resulltAvailable);   // если тут true сайт не доступен и запись сделана
	    if ( !$resulltAvailable ) {
		continue;
	    }
	    $this->startRequests();
	    $this->url = false;
	    $this->domain = false; 
	}

    }
  
/*
      запускает запросы к сайтам
*/
  
    private function startRequests(){
    
	foreach ( $this->targetFiles as $file) { 
	    
	    $this->requestMethod($file);
	    if ($zeroStatus) {
		
	    } else {
		$modelReportErrors = new ReportErrors();
		//$modelReportErrors->current_text = 
		//$modelReportErrors->current_text = 
	    }
	}
    }
    
    
    private function requestMethod($file) {
    
	if( $curl = curl_init() ) {
	    curl_setopt($curl,CURLOPT_URL, $this->url . '/' . $file);
	    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	    curl_setopt($curl,CURLOPT_NOBODY,false);
	    curl_setopt($curl,CURLOPT_HEADER,false);
	    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	    $out = curl_exec($curl);
	    curl_close($curl);
	} 
	
	return $out;
    }
    
    public function checkForExistenceOfRecords($domain_id) {
	
	$this->zeroStatus = false;
	$modelReportErrors = ReportErrors::model()->findByAttributes(['domain_id' = $domain_id]);
	if ( count($modelReportErrors) ) {
	    $this->zeroStatus = true;
	}    
    }

    public function availableSite() {
	
	$available = false;
	if( $curl = curl_init() ) {
	  curl_setopt($curl,CURLOPT_URL, $this->url);
	  curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	  curl_setopt($curl,CURLOPT_NOBODY,true);
	  curl_setopt($curl,CURLOPT_HEADER,true);
	  curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	  curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	  $resultHeaders = curl_exec($curl);
	  curl_close($curl);
	}
	//+++++++++++++++++++++++++++++++++++++++++
	// НАПИСАТЬ ОБРАБОТКЧИК - ОШИБОКИ В ЛОГИ 
	//+++++++++++++++++++++++++++++++++++++++++
	if ( is_string($resultHeaders) ) {
	    preg_match( "/200 OK/", $resultHeaders, $match );
	    if (!empty($match)) {
		$available = true;
	    } 
	}
	return $available;
    }
    
    public function availableLoger($resultAvailable) {
    
	$changeAvailableStatus = false;
	$modelProject = Project::model()->findByAttributes(['domain_id' = $this->domain]);
	if ( $modelProject->available != $resultAvailable ) {
	    $modelProject->available = $resultAvailable;    
	}
	if ( $modelProject->save() ) {
	    $changeAvailableStatus = true;
	}
	return $changeAvailableStatus;
    }
    
    
    public function reqAvailable() {
		$criteria=new CDbCriteria;
	$criteria->condition='domain_id='. $domain_id;
	$criteria->order = "date DESC";
 
  //ServiceStep::model()->find($criteria);
    
    $modelProject = ReportErrors::model()->findAll($criteria);
	
	//$arrayWithModelProjects = ReportErrors::find()->where(['domain_id' => $domain_id])->orderBy('id')->all();
	
	return $out;
    }
}

