<?php


class ReporterrorsCommand extends CConsoleCommand {

    public function actionRequest() {
    
	$arrayWithModelProjects = Project::model()->findAllByAttributes(['error_control' => true]);
	
	if ( empty($arrayWithModelProjects) ) {
	    return true;
	}
	$classCheckErrors = new CheckErrors();
//var_dump($arrayWithModelProjects );
	$classCheckErrors->start($arrayWithModelProjects);
// 	foreach ( $modelProject as $project ) {
// 	    
// 	    $classCheckErrors->url = $project->host;
// 	    $classCheckErrors->start();
// 	}

    }
///////////////////////////////////////////////////////////////////
// Для автоматизации заполнения адресов для их проверки на изменения
// данный экшен получает дамп двух таблиц с cabinet.seo-experts.com название таблиц в кабинете seo_sites, soe_site_words
// в аудите они переименовываются в tbl_projects_cabinet, tbl_projects_cabinet_words
//////////////////////////////////////////////////////////////////
    public function actionTest() {
	$ti = false;
	if ( $dump = $ti) {
	    var_dump('da');
	} else {
	    var_dump($dump );
	}
	
    }
    
    
    
    public function actionCreateArrayForTableLinks() {
	
	$get = new GetCurrentTables();
	//$get->getArrayDomains();
	$get->run();
	echo "<pre>"; var_dump('done'); echo "</pre>";
    }

    
}