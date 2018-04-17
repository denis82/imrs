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
///////////////////////////////////////////////////////////////////
// Для автоматизации заполнения адресов для их проверки на изменения
// данный экшен получает дамп двух таблиц с cabinet.seo-experts.com название таблиц в кабинете seo_sites, soe_site_words
// в аудите они переименовываются в tbl_projects_cabinet, tbl_projects_cabinet_words
//////////////////////////////////////////////////////////////////
    public function actionGetCurrentTables() {
	
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

	$pattern = '/seo_sites/';
	$replacement = 'tbl_projects_cabinet';
	$res = preg_replace($pattern, $replacement, $out);
	$pattern = '/seo_sites_words/';
	$replacement = 'tbl_projects_cabinet_words';
	$res = preg_replace($pattern, $replacement, $res);
	if ( file_get_contents( Yii::app()->basePath . '/files/dump/hashsql.txt' ) != md5($res) ) {
		file_put_contents( Yii::app()->basePath . '/files/dump/tablesdump.sql' , $res );
		file_put_contents( Yii::app()->basePath . '/files/dump/hashsql.txt' , md5($res));
		shell_exec ( 'mysql audit -u root -pkho28o9ndqdfwdsia723yz < ' . Yii::app()->basePath . '/files/dump/tablesdump.sql');
		
	} 
    }
    
    
    
    public function actionCreateArrayForTableLinks() {
	
		  //$projectModel = ProjectsCabinetWords::model()->findAll() ;
	  $projectModel = ProjectsCabinet::model()->with('projects_cabinet_words')->findAll();
//         foreach (Sitemap::model()->findAllByAttributes(array('domain_id' => '60053'), array('order' => 'url asc')) as $el) {
//                 $xml_data['struct']['sitemap'][] = $el->url;
//         }
// 
	$arrayCommon = [];
        foreach ($projectModel as $el) {
                
                
                $arrayTemp = [];
                foreach($el->projects_cabinet_words as $res) {
			//var_dump($res->url);
			if( !in_array($res->url, $arrayTemp) and '' != $res->url and '/' != $res->url) { 
				
				$arrayTemp[] = $res->url;
			}
                }
                $arrayCommon[$el->id] = $arrayTemp;
                
        }
	echo "<pre>"; 
        var_dump($arrayCommon);
                echo "</pre>";
    }

    
}