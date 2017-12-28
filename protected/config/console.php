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
        'application.models.*',
        'application.components.*',
        'application.widgets.*',
        'application.modules.seo.components.*',
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
    ),
);