<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'name'=>'RedisAdmin v.0.1',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language'=>'ru',
    'homeUrl'=>'http://redisadm.ru',
    'sourceLanguage'=>'en',
    'timezone'=>'Asia/Irkutsk',
    'defaultRoute'=>'redisman/default/index',
    'modules' => [
        'redisman' => [
            'class'=>'\insolita\redisman\Redisman',
            'connections'=>[
                'local'=>[
                    'class' => 'yii\redis\Connection',
                    'hostname' => 'localhost',
                    'port' => 6379,
                    'database' => 0,
                ],
                'localnat'=>[
                    'class' => 'insolita\redisman\components\NativeConnection',
                    'hostname' => 'localhost',
                    'port' => 6379,
                    'database' => 0,
                    //  'unixSocket'=>'/tmp/redis.sock'
                ],
            ],
            'defRedis'=>'local',
            'searchMethod'=>'SCAN',
            'greedySearch'=>false,
        ],

    ],
    'components' => [
        'redis' => [
            'class' => 'yii\redis\Connection'
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2hvNcLVat3j01t2EtMIRsTEcMyJPX3VG',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'authManager'=>[
            'class' => 'yii\rbac\PhpManager',
        ],
        'urlManager' => [
            'class' => '\yii\web\UrlManager',
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'suffix' => '.html',
            'rules' => [
                ['pattern' => '', 'route' =>'', 'suffix' => ''],
                ['pattern' => '/', 'route' =>'redisman/default/index', 'suffix' => ''],
                'login'=>'site/login',
                'logout'=>'site/logout',
                'about'=>'site/about',
                '<action:[\w-]+>' => 'redisman/default/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>' => 'redisman/<controller>/<action>'
            ]
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'loginUrl' => ['site/login'],
            'returnUrl' => ['/'],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'session'=>[
            'class'=>'yii\web\Session'
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
