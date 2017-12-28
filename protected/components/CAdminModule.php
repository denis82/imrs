<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CBaseAdminModule
 *
 * @author Kirshin Alexandr
 */
class CAdminModule extends CWebModule {

    protected function checkActive($route, $action = false) {
        $path = explode('/', $route);
		//var_dump(Yii::app()->controller);
		//var_dump(Yii::app()->controller->module);
		
		$MODULE_ID = isset(Yii::app()->controller->module)? Yii::app()->controller->module->id : NULL;
		
        if ($action)
            return 
				$MODULE_ID == $path[0] && 
				Yii::app()->controller->id == $path[1] && 
				Yii::app()->controller->action->id == $path[2];
        else
            return $MODULE_ID == $path[0] && Yii::app()->controller->id == $path[1];
    }

    public function getDashboard() {
        
    }

}

