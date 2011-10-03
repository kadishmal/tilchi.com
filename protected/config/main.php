<?php
// These constants are defined based for Pagodabox.
// To set your db connection credentials, replace $_SERVER['db_host'] and the
// rest with appropriate data.
define("DB_HOST", $_SERVER['db_host']);
define("DB_SOCK", $_SERVER['db_sock']);
define("DB_NAME", $_SERVER['db_name']);
define("DB_USER", $_SERVER['db_user']);
define("DB_PASS", $_SERVER['db_pass']);
define("FORUM_PASS", $_SERVER['forum_pass']);
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
	),

	// application components
	'components'=>array(
        'mail' => array(
 			'class' => 'ext.yii-mail.YiiMail',
 			'transportType' => 'smtp',
            'transportOptions'=>array(
                'host'=>'smtp.gmail.com',
                'username'=>'forum@incorex.com',
                'password'=>FORUM_PASS,
                'encryption'=>'ssl',
                'port'=>465,
            ),
 			'logging' => false,
 		),
        'search' => array(
            'class' => 'application.components.DGSphinxSearch',
            'server' => 'peopletranslate.com',
            'port' => 9312,
            'maxQueryTime' => 3000,
            'enableProfiling'=>false,
            'enableResultTrace'=>false
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
				// A custom rule to handle '/ky/phrase' like URLs.
				array(
					'class' => 'application.components.LanguageUrlRule',
					'connectionID' => 'db',
				),
				array('user/profile', 'pattern'=>'user'),
				array('user/signin/logout', 'pattern'=>'user/logout'),
                array('user/<controller>', 'pattern'=>'user/<controller:\w+>'),
                // Blog URLs
                array('content/blog/<action>', 'pattern'=>'blog/<action:(comments|tags)>/<param>'),
                array('content/blog/<action>', 'pattern'=>'blog/<action:(comments|tags)>', 'defaultParams'=>array('param'=>'all')),
                // Forum URLs
                array('content/forum/<action>', 'pattern'=>'forum/<action:\w+>/<type:(question|idea|issue)>'),
                array('content/<controller>/<action>', 'pattern'=>'<controller:(blog|forum)>/<action:\w+>/<id:\d+>'),
                array('content/<controller>/view', 'pattern'=>'<controller:(blog|forum)>/<slug>'),
                array('content/<controller>/<action>', 'pattern'=>'<controller:(blog|forum|vote)>/<action:\w+>'),
                array('content/forum/index', 'pattern'=>'forum'),
                array('content/<controller>/index', 'pattern'=>'<controller:(blog|forum)>'),
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
			'username' => DB_USER,
			'password' => DB_PASS,
			'connectionString' => 'mysql:unix_socket=' . DB_SOCK . ';dbname=' . DB_NAME,
			'emulatePrepare' => true,
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
			'enableProfiling' =>false,
            'enableParamLogging'=>false
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