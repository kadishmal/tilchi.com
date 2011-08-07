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
		'application.modules.content.*',
		'application.modules.content.models.*',
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
                'password'=>'J*1g!3C$z5',
                'encryption'=>'ssl',
                'port'=>465,
            ),
 			'logging' => false,
 		),
		'db'=>array(
			'username' => 'opentran_tilchi',
			'password' => 'G9xbCB3^hM',
			'connectionString' => 'mysql:host=localhost;dbname=opentran_dbtilchi',
//			'username' => 'root',
//			'password' => 'rootOtPsWdGfuLgtr4e',
			'emulatePrepare' => true,
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
			'enableProfiling' =>true,
            'enableParamLogging'=>true
		),
	),
);