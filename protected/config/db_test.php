<?php


return array(
    'class' => 'CDbConnection',
    'connectionString' => 'mysql:host=db;dbname=mydb',
    'emulatePrepare' => true,
    'username' => 'root',
    'tablePrefix' => '',
    'password' => 'toor',
    'charset' => 'utf8',
    'enableProfiling' => (defined('YII_DEBUG') && YII_DEBUG),
    'enableParamLogging' => (defined('YII_DEBUG') && YII_DEBUG),
);

