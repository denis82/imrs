<?php

class WordxModule extends CAdminModule {

    public $name = 'Словохрень';
    public $version = '1.0';
    public $icon = 'globe';

    public function getMenu(){
    	return array();
    }

    public function init() {
        Yii::import('application.modules.project.models.*');
        Yii::import('application.modules.wordx.models.*');
		return parent::init();
    }

}

