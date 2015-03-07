<?php
/**
 * Application configuration for unit tests
 */
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../config/web.php'),
    require(__DIR__ . '/../../../config/conf-local.php'),
    require(__DIR__ . '/config.php'),
    [

    ]
);
