<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Tilchi.com',

	'sourceLanguage' => 'en_us',
	'language' => 'ru',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'ext.yii-mail.YiiMailMessage',
	),

	'modules'=>array(
        'content'=>array(

	    ),
        'user'=>array(

	    ),
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'MyGiiGfuLgtr4e',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
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
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'loginUrl' => array('/user/signin'),
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				array('user/profile', 'pattern'=>'user'),
				array('user/signin/logout', 'pattern'=>'user/logout'),
                array('user/<controller>', 'pattern'=>'user/<controller:\w+>'),
                // Blog URLs
                array('content/blog/<action>', 'pattern'=>'blog/<action:(comments|tags)>/<param>'),
                array('content/blog/<action>', 'pattern'=>'blog/<action:(comments|tags)>', 'defaultParams'=>array('param'=>'all')),
                array('content/blog/<action>', 'pattern'=>'blog/<action:\w+>/<id:\d+>'),
                array('content/blog/<action>', 'pattern'=>'blog/<action:\w+>'),
                array('content/blog/view', 'pattern'=>'blog/<title>'),
                array('content/blog/index', 'pattern'=>'blog'),
                // Forum URLs
                array('content/forum/<action>', 'pattern'=>'forum/<action:\w+>/<type:(question|idea|issue)>'),
                array('content/forum/<action>', 'pattern'=>'forum/<action:\w+>/<id:\d+>'),
                array('content/forum/<action>', 'pattern'=>'forum/<action:\w+>'),
                array('content/forum/view', 'pattern'=>'forum/<slug>'),
                array('content/forum/index', 'pattern'=>'forum'),
                // Vote URLs
                array('content/vote/<action>', 'pattern'=>'vote/<action:\w+>'),
                // Site URLs
                array('<controller>/', 'pattern'=>'<controller:\w+>'),
                array('<controller>/<action>', 'pattern'=>'<controller:\w+>/<action:\w+>'),

                '<controller:\w+>/<id:\d+>/<title>'=>'<controller>/view',
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
				// necessary for /content/comment/delete/22
				array('<module>/<controller>/<action>', 'pattern'=>'<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>'),
			),
		),
		'db'=>array(
//			'username' => 'opentran_tilchi',
//			'password' => 'G9xbCB3^hM',
			'connectionString' => 'mysql:host=localhost;dbname=opentran_dbtilchi',
			'username' => 'root',
			'password' => 'rootOtPsWdGfuLgtr4e',
			'emulatePrepare' => true,
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
			'enableProfiling' =>true,
            'enableParamLogging'=>true
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, trace, info',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'online@tilchi.com',
	),
);