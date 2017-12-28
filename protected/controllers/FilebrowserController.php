<?php

class FilebrowserController extends CAdminController {

    public function registerScripts() {
        $this->clientScript->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
        $this->clientScript->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js');
        $this->clientScript->registerCssFile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css');

        $this->clientScript->registerCssFile($this->assetsUrl . '/plugins/elfinder/css/elfinder.min.css');
        $this->clientScript->registerCssFile($this->assetsUrl . '/plugins/elfinder/css/theme.css');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/plugins/elfinder/js/elfinder.min.js');
        $this->clientScript->registerScriptFile($this->assetsUrl . '/plugins/elfinder/js/i18n/elfinder.ru.js');

        $this->clientScript->registerScript("elfinder", "
                                        var funcNum = window.location.search.replace(/^.*CKEditorFuncNum=(\d+).*$/, \"$1\");
                                        var elf = $('#elfinder').elfinder({
					url : '" . $this->assetsUrl . "/plugins/elfinder/php/connector.php',
					lang: 'ru',      
                                       height: 700,
                                        getFileCallback: function(url) {
                                            window.opener.CKEDITOR.tools.callFunction(funcNum, url);
                                            window.close();
                                        }
				}).elfinder('instance');");
    }

    public function actionIndex() {
        $this->layout = "//layouts/filebrowser";
        $this->render('index');
    }

}
?>
