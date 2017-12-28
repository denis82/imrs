<?php

class MainModule extends CAdminModule {

    public $name = "Настройки";
    public $version = "1.0";
    public $icon = "cogs";

    public function getMenu() {
        return array(
            array('label' => 'Почтовые шаблоны', 'url' => Yii::app()->urlManager->createUrl('main/email/index'), 'active' => $this->checkActive('main/email/index')),
            array('label' => 'Пользователи', 'url' => Yii::app()->urlManager->createUrl('main/user/index'), 'active' => $this->checkActive('main/user/index')),
        );
    }

    public function init() {
        Yii::import('application.modules.project.models.*');
        /*Yii::import('application.modules.seo.models.*');*/
		return parent::init();
    }

}

?>
