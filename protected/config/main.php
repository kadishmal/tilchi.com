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
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'loginUrl' => array('/user/signin'),
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				// A custom rule to handle '/ru/ky/phrase' like URLs.
				array(
					'class' => 'application.components.LanguageUrlRule',
					'defaultParams'=>array('ajax'=>false),
				),
                // User URLs
				array('user/profile', 'pattern'=>'user'),
				array('user/signin/logout', 'pattern'=>'user/logout'),
                array('user/<controller>', 'pattern'=>'user/<controller:\w+>'),
                array('user/<controller>/<action>', 'pattern'=>'user/<controller:\w+>/<action:\w+>/<param>'),
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
			'username' => 'root',
			'password' => 'MySQLGfuLgtr4e',
			'connectionString' => 'mysql:host=localhost;dbname=dbtilchi',
			'emulatePrepare' => true,
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
			'enableProfiling' =>false,
            'enableParamLogging'=>false,
            'schemaCachingDuration'=>3600
		),
        'authManager'=>array(
            'class'=>'CDbAuthManager',
            'connectionID'=>'db',
            'defaultRoles'=>array('member', 'guest', 'anyone'),
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
			),
		),
        'cache' => array(
            'class' => 'system.caching.CApcCache',
        ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'online@tilchi.com',
	),
);