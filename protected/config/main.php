<?php

define('YII_ALIAS_ROOT', __DIR__ . '/../../');
define('YII_ALIAS_COMMON', YII_ALIAS_ROOT . 'common/');
define('YII_ALIAS_PROTECTED', YII_ALIAS_ROOT . 'protected/');
define('YII_ALIAS_VENDOR', YII_ALIAS_PROTECTED . 'vendor/');

Yii::setPathOfAlias('root', YII_ALIAS_ROOT);
Yii::setPathOfAlias('common', YII_ALIAS_COMMON);
Yii::setPathOfAlias('vendor', YII_ALIAS_VENDOR);

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
        'application.modules.seo.components.*',
        'application.modules.project.components.*',
        'ext.yii-mail.YiiMailMessage',
    ),
    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123def',
            'ipFilters' => array("*"),
			'enabled' => false
        ),
        'seo' => array(),
        'main' => array(),
        'project' => array(),
        'wordx' => array(),
        'admin' => array(),
    ),
    'components' => array(
		'cache'=>array(
			'class' => 'CMemCache',
			'servers' => array(
				array(
					'host' => 'localhost',
					'port' => 11211,
					'weight' => 60,
					'persistent' => 'seo_'
				),
			),
		),
        'user' => array(
            'class' => 'CAdminWebUser',
            'loginUrl' => '/site/login',
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            
            'rules' => array(
                //'gii' => 'gii',
                //'gii/<controller:\w+>' => 'gii/<controller>',
                //'gii/<controller:\w+>/<action:\w+>' => 'gii/<controller>/<action>',
                'audit/<id:\d+>' => '/report',
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:.*>' => '<module>/<controller>/<action>',
		'<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        'db' => include dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../common/config/db.php',
        
                
        'dbadvert' => array(
            'connectionString' => 'mysql:host=136.243.24.131;dbname=cabinet',
            'username'         => 'cabinet',
            'password'         => 'GVLwDBJaFAdf3LSz',
            'class'            => 'CDbConnection',          // DO NOT FORGET THIS!
            'emulatePrepare' => true,
            'charset' => 'utf8',
            'tablePrefix' => 'seo_',
        ),
        
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',

            'routes' => array(
                array(
                    
					//'class' => 'vendor.malyshev.yii-debug-toolbar.YiiDebugToolbarRoute',
					//'ipFilters' => array('78.85.16.5', '78.85.19.108'),
					//'ipFilters' => array('94.181.0.0'),

					'class' => 'ext.phpconsole.PhpConsoleLogRoute',
					/* Default options:
					'isEnabled' => true,
					'handleErrors' => true,
					'handleExceptions' => true,
					'sourcesBasePath' => $_SERVER['DOCUMENT_ROOT'],
					'phpConsolePathAlias' => 'application.vendors.PhpConsole.src.PhpConsole',
					'registerHelper' => true,
					'serverEncoding' => null,
					'headersLimit' => null,
					'password' => null,
					'enableSslOnlyMode' => false,
					'ipMasks' => array(),
					'dumperLevelLimit' => 5,
					'dumperItemsCountLimit' => 100,
					'dumperItemSizeLimit' => 5000,
					'dumperDumpSizeLimit' => 500000,
					'dumperDetectCallbacks' => true,
					'detectDumpTraceAndSource' => true,
					'isEvalEnabled' => false,
					*/
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'trace, info',
                    'categories'=>'application.*',
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
        'mail' => array(
            'class' => 'ext.yii-mail.YiiMail',
            'transportType'=>'smtp',
            'transportOptions'=>array(
                    'host'=>'ssl://smtp.yandex.ru',
                    'username'=>'seo@seo-experts.com',
                    'password'=>'987Q123!',
                    'port'=>'465',
            ),
            'viewPath' => 'application.views.mail',
            'logging' => true,
        ),
    ),
    'params' => array(
        'cacheDuration' => 3600,
        'yandexXML' => array(
            'proxy_address' => '127.0.0.1:3128',
            'proxy_auth' => 'paul:zawert',
            'user' => 'xsite',
            'key' => '03.37624:68dcfd904d9cac84ac2e25bf79b104af',
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
        'adminEmail'=>'dtelegin.spok@yandex.ru',
	'errorsPageEmails'=>['dtelegin.spok@yandex.ru'],
	'robots' => '/robots.txt',
	'sitemap' => '/sitemap.xml',
	'pathForPageErrorLogging' => '/var/www/skipper.su/cronlog/errorReport',
	'pathToDiffList' => 'files/diffPages/',
    ),
);
