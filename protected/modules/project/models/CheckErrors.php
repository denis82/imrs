<?php 

class CheckErrors
{
	private $url = false; // урл сайта 
	private $domain = false;  // ид домена
	private $project;
	private $_logger;
	private $targetFiles = []; // названия файлов которые нужно прочитать
	//private $downloadPath = 'project/index/download/';
	private $reportErrors; // 
	private $listErrorPages = []; //список страниц с изменениями (ошибками) для емайла
	private $listBadStatusPages = [];  //список страниц не доступных (роботс, сайтмап) для емайла
	//$this->_logger->log("Well done google class.");
      
	const TYPE_ORIGIN = true; // тип записи - страница эталона, то с чем сравнивать текущее значение 
	const TYPE_DIFFERENT = false; // тип записи - страница эталона + страница на текущий момент, для архива 
	
	public function __construct() {

		$this->_logger = LogHelper::get_logger(Yii::app()->params['pathForPageErrorLogging']);
	}
  
/************************************************************
*      запускает механизм проверки      
***********************************************************/
  
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
			$this->project = $project;
			//$this->checkForExistenceOfRecords($project->domain_id);
			$resulltAvailable = $this->availableSite();
			$this->availableLoger($resulltAvailable);   // если тут true сайт не доступен и запись сделана
			if ( !$resulltAvailable ) {
				continue;
			}
			$this->mergeArray();
			$this->startRequests();
			$this->url = false;
			$this->domain = false; 
		}

	}
  
/************************************************************
*      запускает запросы к сайтам    
***********************************************************/
  
	private function startRequests(){
	    
		$listErrorPages = [];
		foreach ( $this->targetFiles as $file) { 
			if ( 200 != ($status = SeoUtils::testUrl($this->url  . $file )) ) {
				$this->listBadStatusPages[$this->url  . $file] = $status;
				continue;
			}
			$textPage = $this->requestMethod($file);   // запрос страницы
			$textPageHash = $this->makeHashPage($textPage); // хеш страницы
			
			$this->createNewPageNote($file ,$textPageHash);
			$equalResult = $this->checkEqual($file ,$textPageHash); // 
			if ( !$equalResult ) {
				  $this->errorRecord($file ,$textPageHash);
				  $this->listErrorPages[$file] = $this->url . $file;
			}
			$this->reportErrors = null;
		}
		var_dump($this->targetFiles);
		if ( !empty($this->listErrorPages) ) {
			$gitHandler = new GitHandler($this->domain);
			if( !$gitHandler->start() ) {
				$this->_logger->log($gitHandler->error);
			}
		}
		
		if ( !empty($this->listErrorPages) || !empty($this->listBadStatusPages)) {
			$this->emailSender();
		}
		$this->listErrorPages = [];
		$this->listBadStatusPages = [];

	}
    
/***********************************************************
*  мержит массив редактируемых урлов и  статичных страниц 
***********************************************************/
  
	private function mergeArray(){
	    
		$errorsProject = ReportErrorsLinks::model()->findByAttributes(['domain_id' => $this->domain]);
		$arrayErrorsProject = json_decode($errorsProject->path);
		$targetPages = [];
		if ( is_array($arrayErrorsProject) and 0 < count($arrayErrorsProject)) {
			foreach ( $arrayErrorsProject as  $errorProject) { 
				$normalPattern = "/".addcslashes($this->url, '/')."/";
				preg_match($normalPattern, $errorProject, $match);
				if ( !$match ) {
				    continue;
				}
				$cutOffUrl = preg_replace($normalPattern, "", $errorProject);
				$targetPages[] = $cutOffUrl;
			}
		}
		
		if ( !$errorsProject->robots_status and 200 != ($status = SeoUtils::testUrl($this->url . $errorsProject->robots ))){
			$this->listBadStatusPages[$this->url . $errorsProject->robots] = $status;
			
		} 
		
		if ( 200 == SeoUtils::testUrl($this->url . $errorsProject->robots ) ) {
			if ( $errorsProject->robots ) {
				$this->targetFiles = [ 'robots' => $errorsProject->robots];
			}
		}
		
		if ( !$errorsProject->sitemap_status and 200 != ($status = SeoUtils::testUrl($this->url . $errorsProject->sitemap )) ) {
			$this->listBadStatusPages[$this->url . $errorsProject->sitemap] = $status;
		} //else {
		//	$this->targetFiles = ['sitemap' => $errorsProject->sitemap ];
		//}

		
		$this->targetFiles = array_merge($this->targetFiles, $targetPages);
	}
    
/***********************************************************
*      делает запросы к страницам 
***********************************************************/
    
    
	private function requestMethod($url) {
	
		if( $curl = curl_init() ) {
			curl_setopt($curl,CURLOPT_URL, $this->url  . $url);
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
			  $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			  curl_close($curl);
		}
		//+++++++++++++++++++++++++++++++++++++++++
		// НАПИСАТЬ ОБРАБОТКЧИК - ОШИБОКИ В ЛОГИ 
		//+++++++++++++++++++++++++++++++++++++++++
		if ( 200 == $httpcode ) {
			$available = true; 
		}
		return $available;
	}
    
	public function availableLoger($resultAvailable) {
	
		$changeAvailableStatus = false;
		$modelProject = Project::model()->findByAttributes(['domain_id' => $this->domain]);
		if ( $modelProject->available != $resultAvailable ) {
			$modelProject->available = $resultAvailable;    
		}
		if ( $modelProject->save() ) {
			$changeAvailableStatus = true;
		}
		return $changeAvailableStatus;
	}
    
/***********************************************************
*      делает хеши страниц
***********************************************************/
    
	private function makeHashPage($textPage) {
		
		
		$textPage = preg_replace('/\{.{0,}[\d,a-z]{10,40}.{0,}\}/', '', $textPage);
		$textPage = str_replace(' ', '', $textPage);
		$textPage = str_replace("\n", '', $textPage);
		$textPage = preg_replace('/[\x00-\x1F\x7F]/u', '', $textPage);
		$textPage = strip_tags($textPage);
		
		$textPage = md5( $textPage);
		
		return $textPage;
	}
    
/***********************************************************
*      проверяет сходство хешей страниц
***********************************************************/
    
    /*
    $url - путь к странице которую хотим сравнить(в базе)
    $textPageHash - хэш актуальной страницы для сравнения 
    return boolean - 1 равно/первая запись, 0 не равно 
    */
    
	private function checkEqual($url, $textPageHash) {
	    
		$result = false;
		$modelReportErrors = ReportErrors::model()->findByAttributes(['domain_id' => $this->domain, 'path' =>  $url, 'note_type' => self::TYPE_ORIGIN]);
		if ( !$this->reportErrors ) {
			$this->reportErrors = $modelReportErrors;
		}

		if( $modelReportErrors->origin_text == $textPageHash ) {
			$result = true;
		}
		return $result;
	}
    
/***********************************************************
*      проверяет на существование записи если ее нет то создает
***********************************************************/
    
    /*
    $url - путь к странице которую хотим сравнить(в базе)
    $textPageHash - хэш актуальной страницы для сравнения 
    return boolean - 1 записал, 0 не записал
    */
    
    private function createNewPageNote($url, $textPageHash) {
	
	$result = false;
	$modelReportErrors = ReportErrors::model()->findByAttributes(['domain_id' => $this->domain, 'path' =>  $url, 'note_type' => self::TYPE_ORIGIN]);
	
	if ( !$modelReportErrors ) {
		$modelReportErrors = new ReportErrors();
		$modelReportErrors->domain_id = $this->domain;
		$modelReportErrors->origin_text = $textPageHash;
		$modelReportErrors->path = $url;
		$modelReportErrors->date = date('Y-m-d');
		$modelReportErrors->note_type = self::TYPE_ORIGIN;
		if($modelReportErrors->save()) {
			$result = true; 
			
		}
	} else {
		$this->reportErrors = $modelReportErrors;
	} 
	
	return $result;
    }
    
/*****************************************************************
*      записывает хэш ошибки и эталон в базу и переписывает эталон
*
*****************************************************************/
    
    /*
    $url - путь к странице которую хотим сравнить(в базе)
    $textPageHash - хэш актуальной страницы для сравнения 
    return boolean - 1 записал, 0 не записал
    */
    
	private function errorRecord($url, $textPageHash) {
	    
		$result = false;
		$modelReportErrors = new ReportErrors();
		$modelReportErrors->domain_id = $this->domain;
		$modelReportErrors->origin_text = $this->reportErrors->origin_text;
		$modelReportErrors->current_text = $textPageHash;
		$modelReportErrors->path = $url;
		$modelReportErrors->date = date('Y-m-d');
		$modelReportErrors->note_type = self::TYPE_DIFFERENT;
		if( $modelReportErrors->save() ) {
			$this->reportErrors->origin_text = $textPageHash;
			if ( $this->reportErrors->save() ) {
				$result = true; 
			}
		}
			return $result;
	}
        
/*****************************************************************
*      Получает массив с изменеными страницами и отправляет сфомированный html на почту 
*
*****************************************************************/
    
    /*
    return boolean - true 
    */
    
    private function emailSender() {
	
	$errorsPageEmails = Yii::app()->params['errorsPageEmails'];
	if ( empty($errorsPageEmails) ) {
	    $errorsPageEmails = [Yii::app()->params['adminEmail']];
	}

	foreach ( $errorsPageEmails as $email ) {
		  
		    $message = new YiiMailMessage;
		    $message->view = 'emailText';
		    $message->setBody(['modelProject'         =>      $this->project,
				       'modelreportErrors'    =>      $modelreportErrors,
				       'arrayPages'           =>      $this->listErrorPages, 
				       'badStatusPages'       =>      $this->listBadStatusPages,
				       'diffTextDownload'     =>      'git_diff_' . $this->domain . '_'  . date('Y-m-d'),
				       ],
				       'text/html');
		    $message->subject = 'Аудит проверка на ошибки';
		    $message->addTo($email);
		    $message->from = Yii::app()->params['adminEmail'];
		    
		    Yii::app()->mail->send($message);
	}
	return true;
    }

}