<?php

return array(
    'connectionString' => 'mysql:host=localhost;dbname=audit',
    'emulatePrepare' => true,
    'username' => 'audit',
    'password' => 'RTSVrFscf2Hyf3SYBr',
    'charset' => 'utf8',
    'enableProfiling' => true,
    'enableParamLogging' => true,
    'tablePrefix' => 'tbl_',
    'schemaCachingDuration' => 1,//3600,
	// включаем профайлер
	'enableProfiling'=>true,
	// показываем значения параметров
	'enableParamLogging' => true,
);

?>