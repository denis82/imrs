<?php

class SeoModule extends CAdminModule {

    public $name = 'SEO Анализ';
    public $version = '1.0';
    public $icon = 'globe';

    public function getMenu(){
		$menu = array(
			/*
			array(
				'label' => 'Анализ', 
				'url' => Yii::app()->urlManager->createUrl('seo/analise/index'), 
				'active' => $this->checkActive('seo/analise/index', true)
			),
            */
			/*array(
				'label' => 'Проекты', 
				'url' => Yii::app()->urlManager->createUrl('seo/project/index'), 
				'active' => $this->checkActive('seo/project/index', true),
				'icon' => 'list-alt'
			),
            array(
				'label' => 'Конкуренты', 
				'url' => Yii::app()->urlManager->createUrl('seo/rivals/index'), 
				'active' => $this->checkActive('seo/rivals/index', true),
				'icon' => 'globe'
			),
            array(
				'label' => 'Мини-аудиты', 
				'url' => Yii::app()->urlManager->createUrl('seo/audit/index'), 
				'active' => $this->checkActive('seo/audit/index', true),
				'icon' => 'file'
			)*/
		);
		
        return $menu;
    }

    public function init() {
        Yii::import('application.modules.seo.models.*');
        Yii::import('application.modules.project.models.*');
		return parent::init();
				
    }

}

