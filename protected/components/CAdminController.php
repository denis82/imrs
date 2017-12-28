<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CBaseAdminController
 *
 * @author Kirshin Alexandr
 */
class CAdminController extends CController {

    public $title;
    public $description;
    public $breadcrumbs = array();
    public $assetsUrl;
    public $clientScript;

    /**
     * Инициализация контроллера
     */
    public function init() {
        parent::init();
        if ($this->assetsUrl === null)
            $this->assetsUrl = Yii::app()->getAssetManager()->publish(Yii::app()->basePath . '/views/assets', false, -1, YII_DEBUG);
        $this->clientScript = Yii::app()->getClientScript();
    }

    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules() {
        return array(
            array('allow', // allow authenticated users to access all actions                
                'actions' => array('login', 'logout'),
                'users' => array('?'),
            ),
            array('allow', // allow authenticated users to access all actions                
                'users' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function getMenu() {
        $menu = array();
        foreach (Yii::app()->modules as $id => $module) {
            $moduleObject = Yii::app()->getModule($id);
            if (is_a($moduleObject, "CAdminModule")) {
                if ($moduleObject->id != "main")
                    $menu[] = array(
						'label' => $moduleObject->name, 
						'url' => 'javascript:;', 
						'active' => ( isset($this->module) && $this->module->id == $moduleObject->id ), 
						'visible' => true, 
						'items' => $moduleObject->menu, 
						'icon' => $moduleObject->icon
					);
            }
        }
        return $menu;
    }

    public function render($view, $data = null, $return = false) {
        $this->registerScripts();

        if ($this->getViewFile($view)) {
            parent::render($view, $data, $return);
        } else {
            parent::render("application.views.base.{$view}", $data, $return);
        }
    }

    public function registerScripts() {

        $this->clientScript->registerCoreScript('jquery');

        /*
        $this->clientScript->registerCssFile($this->assetsUrl . '/plugins/bootstrap/css/bootstrap.min.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/plugins/bootstrap/css/bootstrap-responsive.min.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/plugins/font-awesome/css/font-awesome.min.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/css/style-metro.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/css/style.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/css/style-responsive.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/css/themes/default.css');
        //JQuery UI
        $this->clientScript->registerCssFile($this->assetsUrl . '/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.css');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/scripts/jquery-migrate-1.2.1.min.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/plugins/bootstrap/js/bootstrap.min.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/plugins/jquery.blockui.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/ui-jqueryui.js");
        $this->clientScript->registerScript("ui", "UIJQueryUI.init();");
        */

        // Limitless 1.5 template

        //Global stylesheets
        $this->clientScript->registerCssFile('https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900');
        $this->clientScript->registerCssFile($this->assetsUrl . '/limitless_1.5/css/icons/icomoon/styles.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/limitless_1.5/css/bootstrap.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/limitless_1.5/css/core.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/limitless_1.5/css/components.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/limitless_1.5/css/colors.css');

        // Core JS files
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/plugins/loaders/pace.min.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/core/libraries/bootstrap.min.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/plugins/loaders/blockui.min.js');

        // Theme JS files
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/plugins/visualization/d3/d3.min.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/plugins/visualization/d3/d3_tooltip.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/plugins/forms/styling/switchery.min.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/plugins/forms/styling/uniform.min.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/plugins/forms/selects/bootstrap_multiselect.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/plugins/ui/moment/moment.min.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/plugins/pickers/daterangepicker.js');

        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/core/app.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/pages/dashboard.js');
        // end of Limitless 1.5 template

        //App
        $this->clientScript->registerScriptFile($this->assetsUrl . '/scripts/app.js');
        $this->clientScript->registerScript("app", "App.assets = '" . $this->assetsUrl . "'; App.init(); ");
    }

}

?>
