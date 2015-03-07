Yii 2 Redis manager application (Beta)
================================

Demo coming soon


INSTALLATION
------------



### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this application template using the following command:

~~~
php composer.phar global require "fxp/composer-asset-plugin:1.0.0"
php composer.phar create-project --prefer-dist --stability=dev yiisoft/yii2-app-basic basic
~~~

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

~~~
http://localhost/basic/web/
~~~


CONFIGURATION
-------------

### Database

Edit the file `config/web.php` And configure required redis-connections, for example:

```php
'modules' => [
        'redisman' => [
            'class'=>'\insolita\redisman\Redisman',
            'connections'=>[
                'local'=>[
                    'class' => 'yii\redis\Connection',
                    'hostname' => 'localhost',
                    'port' => 6379,
                    'database' => 0,
                    'unixSocket'=>'/tmp/redis.sock'
                ],
                'remote1'=>[
                    'class' => 'insolita\redisman\components\NativeConnection',
                    'hostname' => '1.2.3.4',
                    'port' => 6379,
                    'database' =>1,
                    'unixSocket'=>'/tmp/redis.sock'
                ],
                'remote1'=>[
                    'class' => 'yii\redis\Connection',
                    'hostname' => '123.45.67.88',
                    'port' => 6379,
                    'database' => 0
                 ],
            ],
            'defRedis'=>'local',
            'searchMethod'=>'SCAN',
            'greedySearch'=>false,
        ],
```
