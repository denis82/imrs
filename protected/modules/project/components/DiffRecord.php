<?php

class DiffRecord {
	
	public $diffFilePath; // путь до файла где лежат различия (git diff)
	public $diffFileName;// название файла где лежат различия (git diff)
	public $domain_id;
	public $_logger; 
	public $diffText;
	private $ext = 'txt';
	
	public function __construct( $domain_id ) {
		
		$this->domain_id = $domain_id;
		$this->diffFilePath = Yii::app()->basePath . '/' . Yii::app()->params['pathToDiffList'] . 'git_diff_' .$domain_id . '_'  . date('Y-m-d');
		$this->diffFileName = 'git_diff_' .$domain_id . '_' . date('Y-m-d');

		$this->_logger = LogHelper::get_logger(Yii::app()->params['pathForPageErrorLogging']);
		
	}

//*********************************************
//*     записывает в базу  имя файла в котором лежит 
//*********************************************
	
	public function addRecordToBase() {
		    
		if ( $this->domain_id ) {
			$modelReportErrors = ReportErrors::model()->findByAttributes(['domain_id' => $this->domain_id, 'date' =>  date('Y-m-d') ]);
			if ( count( $modelReportErrors ) > 0) {
				$modelReportErrors->diffFile = $this->diffFileName;
				if( $modelReportErrors->save() ) {
					return true; 
				} else {  
					$err = json_encode($modelReportErrors->getErrors());
					$this->_logger->log($err);
				}
			}
		}
		return false;
	}
	
//*********************************************
//*     записывает текст в указанный файл
//*********************************************
	
	public function recordToFile() {
		file_put_contents( $this->diffFilePath . '.' . $this->ext, $this->diffText );
		
	}
}