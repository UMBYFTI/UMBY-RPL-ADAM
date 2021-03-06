<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Ommu Platform',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		// Model
		'application.models.*',
		'application.models.core.*',
		'application.modules.users.models.*',
		//'application.modules.personal.models.*',

		// Components
		'application.components.*',
		'application.components.admin.*',
		'application.components.public.*',
		'application.components.system.*',
	),

	// application components
	'components'=>array(
		'clientScript' => array(
			'class' => 'OClientScript',
			'coreScriptPosition' => CClientScript::POS_END,
		),

		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		
		// uncomment the following to enable URLs in path-format		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false,
			//'rules'=>array(
				//'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				//'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				//'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				
				//'<module:\w+>/<controller:\w+>/<id:\d+>'=>'<module>/<controller>/view',
				//'<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<module>/<controller>/<action>',				
				//'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
			//),
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
					'levels'=>'error, warning',
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

);