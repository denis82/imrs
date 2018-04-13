  <?php

/**
 * Модель формы информации об организации
 */

class ErrorForm extends CFormModel {
    
	public $path = '';
	public $path_to_form = '';
	public $project;
	public $allCountUrlString = 0;
	public $damageCountUrlString = 0;
	public $damageUrl = [];
	public $showInfo = false;
	public $validUrl = [];
	public $robots;
	public $sitemap;
	public $robots_status;
	public $sitemap_status;
	public $error_control;

	public function rules() {
		return array(
			array('path , path_to_form , robots , sitemap , robots_status , sitemap_status' , 'safe'),
		);
	}

	/**
	* Declares attribute labels.
	*/
	public function attributeLabels() {
	    return array(
		'path_to_form' => 'Укажите адреса для отслеживания (каждый с новой строки)',
		'allCountUrlString' => 'Общее количество введеных строк',
	        'countUrlString' => 'Количество валидных сток',
		'damageCountUrlString' => 'Количество не валидных сток',
		'damageUrl' => 'Не валидные строки:',
		'robots' => 'Файл роботс',
		'sitemap' => 'Файл сайтмап',
		'robots_status' => 'Исключить роботс из емайл отчета',
		'sitemap_status' => 'Исключить сайтмап из емайл отчета',
	    );
	}

	public function save() {
		
		$modelReportErrorsLinks = ReportErrorsLinks::model()->findByAttributes(array('domain_id' => $this->project->domain_id));
		
		if ( !$modelReportErrorsLinks or !$modelReportErrorsLinks->domain_id ) {
			$modelReportErrorsLinks = new ReportErrorsLinks;
			$modelReportErrorsLinks->domain_id = $this->project->domain_id;
			$modelReportErrorsLinks->robots = Yii::app()->params['robots'];
			$modelReportErrorsLinks->sitemap = Yii::app()->params['sitemap'];
		}
		$modelReportErrorsLinks->attributes = $this->attributes;
		$modelReportErrorsLinks->path = $this->createArrayFromStringJson($this->path_to_form);
		$modelReportErrorsLinks->path_to_form = implode("\n", $this->validUrl);//$this->createArrayFromString($this->path_to_form);
		if ($modelReportErrorsLinks->save()) {
		    return true;
		}

		$this->addError('name', 'Непредвиденная ошибка. Попробуйте ещё раз.');
		return false;
	}
	
	/**
	* Делает из строки массив представленный в виде json
	*/
	
	private function createArrayFromStringJson($string) 
	{
		if (!$string || !is_string($string)) {
			return false;
		}
		$sentence = preg_replace("/\n/", "@!@", $string);
		$arrayPath = explode("@!@",$sentence);
		$arrayPathTenp = [];
		if (is_array($arrayPath)) {
			foreach ($arrayPath as $key => $path) {
				preg_match_all('/http/', $path , $matches, PREG_OFFSET_CAPTURE);
				$this->allCountUrlString++;				
				if ( 1 != count($matches[0]) ) {
					$this->damageCountUrlString++;
					$this->damageUrl[] = ['path' => $path,'status' => 0];
					$this->showInfo = true;
					continue;
				} 
				$arrayPathTemp[$key] = trim($path, " \n\r\0\x0B");
			}
			$this->filterBadStatus($arrayPathTemp);
		}
		return json_encode($this->validUrl);
	}
	
		/**
	* Делает из строки массив представленный в виде json
	*/
	
	private function createArrayFromString($string) 
	{

		return implode("\n", $this->validUrl);
	}
	    
	private function filterBadStatus($arrayPath) {
	    
		foreach($arrayPath as $path) {
			
			$status = $this->requestMethod($path);
			
			if ( 200 != $status) {
				$this->damageUrl[] = ['path' => $path,'status' => $status];
				$this->damageCountUrlString++;
				$this->showInfo = true;
				continue;
			}
			$this->validUrl[] = $path;
		}
	}

/***********************************************************
*      делает запросы к страницам 
***********************************************************/
    
	private function requestMethod($path) {
	    
		if( $curl = curl_init() ) {
			curl_setopt($curl,CURLOPT_URL, $path);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_NOBODY,false);
			curl_setopt($curl,CURLOPT_HEADER,false);
			curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
			$out = curl_exec($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
		} 
		return $httpcode;
	}
	
}
