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
            'on beforeFlushDB'=>function($event){
                if($event->data['db']==3){
                     $event->isValid=false;
                     return false;
                }else{
                    $event->isValid=true;
                    return true;
                }
            },
            'on beforeAction'=>function($event){
                \yii\base\Event::on('\insolita\redisman\models\RedisItem',
                       \insolita\redisman\models\RedisItem::EVENT_AFTER_CHANGE,
                       function($event){
                           \app\models\ActionLog::log($event);
                       }
                    );
            }
        ],

    ],
    'components' => [
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 3,
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@insolita/redisman/views' => '@app/views/redisman',
                ],
            ]
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
            'class' => 'pheme\i18n\I18nUrlManager',
            'languages' => ['en', 'ru'],
            'displaySourceLanguage'=>true,
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'suffix' => '.html',
            'rules' => [
                '<lang:(ru|en)>'=>'site/index',
                '<lang:(ru|en)>/index'=>'redisman/default/index',
                '<lang:(ru|en)>/log'=>'log/index',
                '<lang:(ru|en)>/user'=>'log/user',
                '<lang:(ru|en)>/login'=>'site/login',
                '<lang:(ru|en)>/logout'=>'site/logout',
                '<lang:(ru|en)>/about'=>'site/about',
                '<lang:(ru|en)>/<action:[\w-]+>' => 'redisman/default/<action>',
                '<lang:(ru|en)>/<controller:[\w-]+>/<action:[\w-]+>' => 'redisman/<controller>/<action>'
            ]
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'loginUrl' => ['site/login'],
            'returnUrl' => ['/'],
            'on afterLogin'=>function($event){
                \app\models\User::afterLogin($event);
            }
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
