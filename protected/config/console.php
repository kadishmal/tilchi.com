<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Tilchi Console Application',
	'sourceLanguage' => 'en_us',
	'language' => 'ru',
    // autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.controllers.*',
		'application.modules.content.*',
		'application.modules.content.controllers.*',
		'application.modules.content.models.*',
        'application.modules.user.*',
        'application.modules.user.controllers.*',
        'application.modules.user.models.*',
        'ext.yii-mail.YiiMailMessage',
	),
	// application components
	'components'=>array(
        'mail' => array(
 			'class' => 'ext.yii-mail.YiiMail',
 			'transportType' => 'smtp',
            'transportOptions'=>array(
                'host'=>'smtp.gmail.com',
                'username'=>'forum@incorex.com',
                'password'=>'iFB#yq5Z*b',
                'encryption'=>'ssl',
                'port'=>465,
            ),
 			'logging' => false,
 		),
        'search' => array(
            'class' => 'application.components.DGSphinxSearch',
            'server' => '127.0.0.1',
            'port' => 9312,
            'maxQueryTime' => 3000,
            'enableProfiling'=>false,
            'enableResultTrace'=>false
        ),
        'amqp' => array(
            'class' => 'application.components.AMQP.CAMQP'
        ),
		'db'=>array(
			'username' => 'root',
			'password' => 'MySQLGfuLgtr4e',
			'connectionString' => 'mysql:host=localhost;dbname=dbtilchi',
			'emulatePrepare' => true,
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
			'enableProfiling' =>false,
            'enableParamLogging'=>false,
            'schemaCachingDuration'=>3600,
		),
        'cache' => array(
            'class' => 'system.caching.CApcCache',
        ),
	),
);