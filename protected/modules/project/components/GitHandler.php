<?php

class GitHandler {
	
	public $dirPath; // путь до создаваемой директрии проекта
	private $url = false; // урл сайта
	public $gitRepositoryPath = '.git'; // путь до гита
	public $massageGit = "On branch master nothing to commit, working directory clean ";
	public $prefix = 'project_id_';
	public $arrayPages = [];
	public $error;
	private $modelReportErrorsLinks ;
	private $ext = 'txt';
	private $_logger;
	private $pageText = '';

	//$this->_logger->log("Well done google class.");
	
//*********************************************	
//*     $dirName - int ид проекта (domain_id)
//*********************************************
	
	
	public function __construct($dirName) {

		if ( $dirName ) {	
			//$basePath = Yii::app()->basePath;
			$pathToDiffPages = Yii::app()->params['pathToDiffPages'];
			$this->dirPath = $pathToDiffPages . $this->prefix . (int)$dirName;
			
			$this->modelReportErrorsLinks = ReportErrorsLinks::model()->findByAttributes(['domain_id' => $dirName]);
			$this->_modelProject = Project::model()->findByAttributes(['domain_id' => $dirName]); 
			//$this->diffFile = Yii::app()->basePath . '/' . Yii::app()->params['pathToDiffList'] . 'git_diff_' . date('Y-m-d');
		}

		
		
		$this->_logger = LogHelper::get_logger(Yii::app()->params['pathForPageErrorLogging']);
		
	}
	
	public function start() {
	
		$reuslt = false;
		//$res = Yii::app()->basePath;
		
		$this->error = 'Запуск runCreate => ' . $this->dirPath;
		$isCreate = $this->runCreate();
		//var_dump($isCreate); 
		if ( !$isCreate ) {
		
			$this->error = 'Запуск runUpdate => ' . $this->dirPath;
			$isUpdate = $this->runUpdate();
			//var_dump($isUpdate ); 
		}
		$reuslt = true;
		$this->error = 'Старт завершен';
		//
		return $reuslt;
	}
	
	public function runCreate() {
	
		//$this->addDirectory();
		
		if ( !$this->addDirectory() ) {
			  return false;
		}
		$this->gitInit();
		$this->getArrayTextPages();
		
		foreach ( $this->arrayPages as $key => $page) {
			//var_dump($key);
			$this->pageText = $page;
			$this->addFile( $key ); 
			$this->pageText = '';
		}
		$this->gitCommit();
		return true;
		
	}
	
	public function runUpdate() {
	
		$this->getArrayTextPages();
		foreach ( $this->arrayPages as $key => $page) {  // записваю в файлы
			$this->pageText = $page;
			$this->addFile( $key ); 
			$this->pageText = '';
		}
 		$diffTest = $this->gitDiff();   // получаю строку git diff
 		
 		$diffRecord = new DiffRecord($this->_modelProject->domain_id);  
		$diffRecord->diffText = $diffTest;
 		$diffRecord->recordToFile(); // записваю в файлик
 		$res = $diffRecord->addRecordToBase(); // имя файлика пишу в базу
 		//$this->getArrayTextPages(); // масси с актуальными текстами страниц
 		
// 
 		$this->gitCommit(); // коммичу
		return true;
	}
	


//*********************************************	
//*     создает пустую директорию
//*	return - {boolean} true если все прошло удачно
//*********************************************
	
	public function addDirectory() {
		
		$result = false;
		if ( !file_exists ( $this->dirPath ) ) {
				
			$output = CFileHelper::createDirectory( $this->dirPath, 0777);
			//@chmod($this->dirPath , 0777);
			if ( $output ) {
				$result = true;
			}
		}
		return $result;
	}
	
//*********************************************
//*     рекурсивно удаляет директорию
//*********************************************
	
	public function removeDirectory() {
		if ( file_exists ( $this->dirPath ) ) {
			CFileHelper::removeDirectory();
		}	
	}
	
//*********************************************
//*     рекурсивно удаляет директорию
//*********************************************


	public function addFile( $fileName ) {
		if ( !file_exists ( $this->dirPath .  $fileName ) ) {
		
			$output = shell_exec('touch ' . $this->dirPath .'/' . $fileName);
			
			
			file_put_contents( $this->dirPath .'/' . $fileName , $this->pageText );
			
			
			@chmod( $this->dirPath .  $fileName , 0777 );
			if ( null == $output ) {
				return false;
			}
		}
	}
	public function removeFile($fileName) {
		if ( file_exists ( $this->dirPath .  $fileName ) ) {
			$output = shell_exec('rm ' . $this->dirPath .  $fileName);
			if ( null == $output) {
				return false;
			}
		}
	}
	
//*********************************************
//*     инициирует git репозиторий
//*     return - {boolean} true если все прошло удачно
//*********************************************	
	public function gitInit() {
		
		$result = false;
		$this->addFile( $this->gitList );
		if ( !file_exists ( $this->gitRepositoryPath ) ) {
		      shell_exec('git -C ' . $this->dirPath . ' init');
		      shell_exec('chmod -R 777 ' . $this->dirPath . '/' . $this->gitRepositoryPath);
		      shell_exec(' git -C ' . $this->dirPath . ' config --global user.name "root" ');
		      shell_exec(' git -C ' . $this->dirPath . ' config --global user.email mail@mail.com ');

		      $output = shell_exec('git -C ' . $this->dirPath . ' status');	
		      if ( $massageGit == $output ) {
				$result = true;
		      }
		}
		return $result;
	}
	
	public function gitCommit() {
		
		$result = false;
		      shell_exec('git -C ' . $this->dirPath . ' add .');
		      shell_exec('git -C ' . $this->dirPath . ' commit -m "[' . date(DATESTRING_FULL) . ']"');
		      $output = shell_exec('git -C ' . $this->dirPath . ' status');	
		      if ( $massageGit == $output ) {
				$result = true;
		      }
		
		return $result;
	}
	
	public function gitDiff() {
		//var_dump($this->dirPath);		
		$result = shell_exec('git -C ' . $this->dirPath . ' diff'); 
		
		return $result;
	}
	
//*********************************************
//*     формирует массив 
//*	ключ - url
//*     значение - текст страницы
//*     
//*********************************************	
	private function getArrayTextPages() {
		
	    
		$pathRobots = $this->modelReportErrorsLinks->robots;
		$this->arrayPages[pathinfo($pathRobots, PATHINFO_FILENAME) . '.' . $this->ext] = CurlHandler::getTextPage( $this->_modelProject->host . $pathRobots );
		$arrayPath = json_decode($this->modelReportErrorsLinks->path);
		if( is_array($arrayPath) ) {
			foreach( $arrayPath  as $path ) {
				$this->arrayPages[pathinfo($path, PATHINFO_FILENAME) . '.' .$this->ext] = CurlHandler::getTextPage($path);
				//var_dump(pathinfo($pathRobots, PATHINFO_FILENAME) . '.' . $this->ext);
			}
		}
		//var_dump($this->_modelProject->host);
		//var_dump($pathRobots);
		//var_dump(pathinfo($pathRobots, PATHINFO_FILENAME) . '.' . $this->ext);
		return $arrayPath;
	}
	
	 
	private function getNames4Files( $path ) {
	
		//preg_match("/[^\/]{0,}(\/|[a-zA-Z0-9])$/", $path, $res);
		$result = pathinfo($path, PATHINFO_FILENAME);
		return $result;
	}
	

	
}
