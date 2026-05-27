<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

    include_once './protected/components/Serialization.php';
    $serialization = new Serialization();

    $deserializedArray = $serialization->deserialize( $serialization->getPathToFile() );

	$emailData = array(
		'adminEmail'=>'carguide@mtp.ie ',
		'mail_host'=>'mail.mtp.ie',
		'mail_username'=>'contact@mtp.ie',
		'mail_password'=>'',
		'mail_from'=>'MTP.IE',
		'dinkey'=>array(
			'M_LibrariesURL'=>'http://localhost/dinkey/libs',
			'M_ErrorPageURL'=>'http://localhost/dinkey/dinkeyweberr.php',
		),
		'googleAnalyticsCode'=>'UA-33837535-1',
	);

	if(!empty($deserializedArray)){
		$serializationParams = $deserializedArray;
	}
	$serializationParams = array_merge($serializationParams,$emailData);

	return array(
		'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
		'name'=>'MTP',
			'language'=>'en',

		// preloading 'log' component
		'preload'=>array('log'),

		// autoloading model and component classes
		'import'=>array(
			'application.models.*',
			'application.components.*',
				'application.components.dinkey.*',
				'application.components.dinkey.libs.*',
				'application.components.dinkey.libs.protected.*',
		),


		'modules'=>array(
			'gii'=>array(
				'class'=>'system.gii.GiiModule',
				'password'=>'xerdcftyuiol',
				// If removed, Gii defaults to localhost only. Edit carefully to taste.
				'ipFilters'=>false,//array('127.0.0.1','::1'),
			),
			'cms',
			//'qpay'=>array('modules'=>array('qpayrealex'=>array('responseRoute'=>'/realex/handleResponse'))),
					'qpay'=>array('modules'=>array('qpayrealex'=>array('responseRoute'=>'http://mtp.ie/app/index.php?r=/realex/handleResponse'))),
			'qupgrade'=>array('upgradeFilesPath'=>'/protected/data/upgrades'), //relative to webroot
		),

		// application components
		'components'=>array(
			'user'=>array(
						'class'=>'WebUser'
					),
			'session'=>array(
						'class' => 'CDbHttpSession',
						'sessionName'=>'MTP',
						'timeout'=>86400,
						'autoStart'=>false,
						'savePath'=>session_save_path().''
					),
			'mobileDetect' => array(
				'class' => 'ext.MobileDetect.MobileDetect'
			),
			'request'=>array(
				'enableCsrfValidation'=>false,
			),
			
			'db'=>array(
				'class'=>'CDbConnection',
				'connectionString'=>'mysql:host=mtp.cjdctzykicco.eu-west-1.rds.amazonaws.com;dbname=mtp_app_db;',
				'charset'=>'utf8',
				'username' => 'mtp_admin',
				'password' => 'Tk3H7wPJRLxyIp3W',
			),


			'errorHandler'=>array(
				// use 'site/error' action to display errors
				'errorAction'=>'site/error', //TODO: errorPage - zmienic na strone FRONTOWA (!)
			),
			'log'=>array(
				'class'=>'CLogRouter',
				'routes'=>array(
					array(
						'class'=>'CFileLogRoute',
						'levels'=>'error, warning',
					),
					array(
						'class'=>'CFileLogRoute',
						'levels'=>'info',
						'categories'=>'qpay',
						'logFile'=>'qpay_realex.log',
					),
				),
			),
			'cache'=>array(
				'class'=>'system.caching.CFileCache',
			),
			'settings'=>array(
				'class'             => 'ext.settings.CmsSettings',
				'cacheComponentId'  => 'cache',
				'cacheId'           => 'settings',
				'cacheTime'         => 84000,
				'tableName'     => 'settings',
				'dbComponentId'     => 'db',
				'createTable'       => false,
				'dbEngine'      => 'InnoDB',
			),
		),

		// application-level parameters that can be accessed
		// using Yii::app()->params['paramName']
		'params'=>array_merge(array(
			'RIVehicleData'=>array(
				'memberID'=>'MTP',
							'free'=>array(
							'url'=>'https://vqs.riskintelligence.ie/VehicleQueryReport.asmx?wsdl',
							'username'=>'mtpfree',
							'password'=>'XMG3HE7d',
						),
				'paid'=>array(
					'url'=>'https://pre-production-rii.moneymate.com/vqs/vehiclequery.asmx?wsdl',
					'username'=>'mtppaid',
					'password'=>'XMG3HE7d',
				)
			),
			
			'is_test_version'=>true,
			'is_test_version_deep_debug'=>false,
			'import_folder'=>'./qBixSoft',
			// code_column: on test version use true, on product version false
			'used_car_com_code_column_visibility'=>false, 
			$serializationParams
		))
	);
