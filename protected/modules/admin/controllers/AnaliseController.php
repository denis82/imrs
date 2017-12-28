<?php

class AnaliseController extends CAdminController
{

    public $name = "Анализ сайта";
    public $description = "";

    public function init()
    {
        $this->title = $this->name;
        Yii::import("application.modules.seo.components.*");
        Yii::import("application.modules.seo.models.*");
        return parent::init();
    }

    public function actionindex()
    {
        $domain = Domain::check("seo-experts.com");
        SiteInfo::check($domain->id);
    }

}

?>
