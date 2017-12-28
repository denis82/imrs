<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
Yii::setPathOfAlias('root', dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../');
Yii::setPathOfAlias('common', dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../common/');

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'SEO-EXPERTS',
    'sourceLanguage' => 'ru',
    'language' => 'ru',
    'preload' => array('log'),
    'import' => array(
        'common.*',
        'common.components.*',
        'common.models.*',
        'common.helpers.*',
        'application.models.*',
        'application.components.*',
        'application.widgets.*',
        'application.modules.project.models.*',
        'application.modules.seo.components.*',
        'application.modules.wordx.models.*',
        'ext.yii-mail.YiiMailMessage',
    ),
    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123',
            'ipFilters' => array("*"),
        ),
        'seo' => array(),
        'main' => array(),
    ),
    'components' => array(
        'user' => array(
            'class' => 'application.components.CAdminWebUser',
            'loginUrl' => '/site/login',
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                'gii' => 'gii',
                'gii/<controller:\w+>' => 'gii/<controller>',
                'gii/<controller:\w+>/<action:\w+>' => 'gii/<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:.*>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
            ),
        ),
        'db' => include dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../common/config/db.php',
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace, error, warning',
                ),
            ),
        ),
        'request' => array(
            'enableCsrfValidation' => true,
            'enableCookieValidation' => true,
        ),
        'authManager' => array(
            'class' => 'application.components.CAdminPhpAuthManager',
            'defaultRoles' => array('guest'),
        ),
    ),
    'params' => array(
        'cacheDuration' => 3600,
        
        'yandexXML' => array(
            'proxy_address' => '127.0.0.1:3128',
            'proxy_auth' => 'paul:zawert',
            //'user' => 'xsite',
            //'key' => '03.37624:68dcfd904d9cac84ac2e25bf79b104af',
            'user' => 'xsite-a',
            'key' => '03.577340534:573d837a3bda8d4931edf83c417e4ce1',
        ),
        'yandexDirect' => array(
            'user' => 'xsite',
            'token' => 'AQAAAAAAAJL4AAAtCs7K1CrOPE6InROBmmX4FJc',
        ),
        'Mystem' => array(
            'path' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../../mystem/mystem',
            'tmp' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../../mystem/tmp/',
        ),
        'report' => array(
            'path' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../../report',
            'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../../report/default',
        ),
        'google' => array(
        	'pageSpeed' => array(
        		'key' => 'AIzaSyDxeKbWSsiOMK2WiSunNHFoDdhHk9MbyKU',
        	)
        ),
    ),
);