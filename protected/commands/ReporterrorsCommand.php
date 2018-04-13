<?php
class ReporterrorsCommand extends CConsoleCommand {

    public function actionRequest() {
    
	$arrayWithModelProjects = Project::model()->findAllByAttributes(['error_control' => true]);
	
	if ( empty($arrayWithModelProjects) ) {
	    return true;
	}
	$classCheckErrors = new CheckErrors();

	$classCheckErrors->start($arrayWithModelProjects);
// 	foreach ( $modelProject as $project ) {
// 	    
// 	    $classCheckErrors->url = $project->host;
// 	    $classCheckErrors->start();
// 	}

    }
    
    public function actionTest() {
	
	//$res = Yii::app()->basePath;
	//$dst = 60048;
//$result = shell_exec('git -C /var/www/skipper.su/diffPages/project_id_60136 diff');
$fileName = 'git_diff_60136_2018-04-05.txt';
$result = Yii::app()->urlManager->baseUrl  . 'project/index/download/'  . $fileName;
	//$res = Yii::app()->urlManager->BaseUrl;
	//$gitHandler = new GitHandler($dst);
	//$gitHandler->start();
	var_dump($result);
	
    }
    
}