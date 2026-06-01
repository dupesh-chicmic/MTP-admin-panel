<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),
	// application components
	'components'=>array(
		'db'=>array(
			'class'=>'CDbConnection',
			'connectionString'=>'mysql:host=mtp.cjdctzykicco.eu-west-1.rds.amazonaws.com;dbname=mtp_app_db;',
			'charset' => 'utf8',
			'username' => 'mtp_admin',
			'password' => 'Tk3H7wPJRLxyIp3W',
		),
	),
);
