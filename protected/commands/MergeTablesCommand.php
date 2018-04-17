<?php

class MergeTablesCommand extends CConsoleCommand {

    public function actionRun() {
	  
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