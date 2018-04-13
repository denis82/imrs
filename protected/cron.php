<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

// change the following paths if necessary
$yiic = __DIR__ . '/vendor/yiisoft/yii/framework/yiic.php';
$config = __DIR__ . '/config/cron.php';

require_once __DIR__ . '/vendor/autoload.php';

require_once($yiic);

