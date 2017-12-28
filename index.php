<?php
ini_set('default_charset', 'UTF-8');
mb_internal_encoding("UTF-8");


//error_reporting(E_ALL);
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

// change the following paths if necessary
$yii = __DIR__ . '/protected/vendor/yiisoft/yii/framework/yii.php';
$config = __DIR__ . '/protected/config/main.php';

define('YII_DEBUG', true);
//defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

// include_once(__DIR__ . '/../framework/yii.php');

require_once $yii;
require_once __DIR__ . '/protected/vendor/autoload.php';

$app = Yii::createWebApplication($config);

$app->run();
