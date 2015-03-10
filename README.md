Yii 2 Redis manager application (Beta)
================================

 * Demo http://yii2redis-insolita1.c9.io/  (admin adminredis)
 * Based on redisman-module https://github.com/Insolita/yii2-redisman
Interface for work with different redis-connections, swtih between databases, search keys by patterns, move\edit\update\delete
You can use yii2-redis connection, or custom module component that work via php-redis extension


INSTALLATION
------------
(SIMILAR AS DEFAULT YII2-BASIC APPLICATION)
### Install via Composer
If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).
You can then install this application template using the following command:
~~~
php composer.phar global require "fxp/composer-asset-plugin:1.0.0"
php composer.phar create-project --prefer-dist --stability=dev insolita/yii2-redisman-app redisman
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
                    'class' => 'insolita\redisman\components\PhpredisConnection',
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
