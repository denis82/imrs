<?php
class Reporterrors extends CConsoleCommand {

    public function actionRequest() {
    
	$arrayWithModelProjects = Project::model()->findAllByAttributes(['error_control' => true]);
	
	if ( empty($modelProject) ) {
	    return true;
	}
	$classCheckErrors = new CheckErrors;
	$classCheckErrors->start($arrayWithModelProjects);
// 	foreach ( $modelProject as $project ) {
// 	    
// 	    $classCheckErrors->url = $project->host;
// 	    $classCheckErrors->start();
// 	}

    }
}