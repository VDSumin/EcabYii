<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'EduCab',
    'theme' => 'bootstrap',
    'language' => 'ru',
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.models.form.*',
		'application.models.db.*',
		'application.models.priem.*',
		'application.models.notification.*',
        'application.components.*',
        'application.extensions.phpexcel.Classes.*',
        'ext.YiiMailer.YiiMailer',
	),

	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'EcabStudentOmGTU786!',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
        'admin',
        'individualplan',
        'studyProcess',
        'remote',
        'chiefs',
        'inquiries',
        'cases',
        'educationWork',
        'zak',
        'notification'

	),

	// application components
	'components'=>array(
        'widgetFactory' => array(
            'widgets' => array(
                'CLinkPager' => array(
                    'header' => '',
                    'nextPageLabel'=>'Следующая <i class="fa fa-long-arrow-right"></i>',
                    'prevPageLabel'=>'<i class="fa fa-long-arrow-left"></i> Предыдущая',
                    'lastPageLabel'=>'Последняя',
                    'firstPageLabel'=>'Первая',
                    'selectedPageCssClass' => 'active',
                    'hiddenPageCssClass' => 'disabled',
                    'htmlOptions' => array(
                        'class' => 'pagination',
                     ),
                ),
            ),
        ),
        'session' => array (
            'class' => 'system.web.CDbHttpSession',
            'connectionID' => 'db',
            'sessionName' => 'STUDSESSID',
            'sessionTableName' => '{{yii_session}}',
        ),
		'authManager' => array(
            'class' => 'CDbAuthManager',
            'defaultRoles' => array(
                'guest' /* WebUser::ROLE_GUEST */,
                'user' /* WebUser::ROLE_USER */),
            'assignmentTable' => 'tbl_authAssignment',
            'itemChildTable' => 'tbl_authItemChild',
            'itemTable' => 'tbl_authItem',
        ),
        'user' => array(
            'class' => 'WebUser',
            'allowAutoLogin'=>true,
            'authTimeout' => (defined('YII_DEBUG') && YII_DEBUG) ? null : 15 * 60,
            'loginUrl' => /*(defined('YII_DEBUG') && YII_DEBUG) ? array('site/login') :*/ 'http://omgtu.ru/ecab/'
        ),

		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/

		// database settings are configured in database.php
		'db' => file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'db-local.php') ?
            require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'db-local.php' :
            require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'db.php',
        'db2' => require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'priem.php',
		'db_test' => require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'db_test.php',

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CProfileLogRoute',
                    'levels' => 'profile',
                    'enabled' => (defined('YII_DEBUG') && YII_DEBUG),
                ),
                array(
                    'class' => 'CWebLogRoute',
                    'filter'=>'CLogFilter',
                    'levels' => 'error, warning, notice',
                    'enabled' => (defined('YII_DEBUG') && YII_DEBUG),
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, notice, info',
                ),
            ),
        ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
        'adminEmail'=>'webmaster@example.com',
        'YiiMailer' => array(
            'viewPath' => 'application.views.mail',
            'layoutPath' => 'application.views.layouts',
            'baseDirPath' => 'webroot.images.mail', //note: 'webroot' alias in console apps may not be the same as in web apps
            'savePath' => 'webroot.assets.mail',
            'testMode' => false,
            'layout' => 'mail',
            'CharSet' => 'UTF-8',
            'AltBody' => Yii::t('YiiMailer', 'You need an HTML capable viewer to read this message.'),
            'language' => array(
                'authenticate' => Yii::t('YiiMailer', 'SMTP Error: Could not authenticate.'),
                'connect_host' => Yii::t('YiiMailer', 'SMTP Error: Could not connect to SMTP host.'),
                'data_not_accepted' => Yii::t('YiiMailer', 'SMTP Error: Data not accepted.'),
                'empty_message' => Yii::t('YiiMailer', 'Message body empty'),
                'encoding' => Yii::t('YiiMailer', 'Unknown encoding: '),
                'execute' => Yii::t('YiiMailer', 'Could not execute: '),
                'file_access' => Yii::t('YiiMailer', 'Could not access file: '),
                'file_open' => Yii::t('YiiMailer', 'File Error: Could not open file: '),
                'from_failed' => Yii::t('YiiMailer', 'The following From address failed: '),
                'instantiate' => Yii::t('YiiMailer', 'Could not instantiate mail function.'),
                'invalid_address' => Yii::t('YiiMailer', 'Invalid address'),
                'mailer_not_supported' => Yii::t('YiiMailer', ' mailer is not supported.'),
                'provide_address' => Yii::t('YiiMailer', 'You must provide at least one recipient email address.'),
                'recipients_failed' => Yii::t('YiiMailer', 'SMTP Error: The following recipients failed: '),
                'signing' => Yii::t('YiiMailer', 'Signing Error: '),
                'smtp_connect_failed' => Yii::t('YiiMailer', 'SMTP Connect() failed.'),
                'smtp_error' => Yii::t('YiiMailer', 'SMTP server error: '),
                'variable_set' => Yii::t('YiiMailer', 'Cannot set or reset variable: ')
            ),
        ),
	),
);
