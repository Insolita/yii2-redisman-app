<?php

use yii\helpers\Html;
use yii\web\HttpException;
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception (yii\web\Exception|HttpException) */

$this->title = $name;
$icons=[
    '403'=>'lock',
    '404'=>'find',
    '0'=>'bug',
    'default'=>'warning sign',
];
$code=($exception instanceof HttpException ?$exception->statusCode:$exception->getCode());
$icon=(isset($icons[$code]))?$icons[$code]:$icons['default'];
?>

<div class="ui bottom attached error icon message">
    <i class="<?=$icon?> icon"></i>

    <div class="content">
        <div class="header">
            <?= Html::encode($this->title) ?>
        </div>
        <?= nl2br(Html::encode($message)) ?>
    </div>
</div>
