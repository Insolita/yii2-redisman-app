<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'About this Project';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ui teal pointed segment">
    <h1 class="ui header">
        <i class="icon smile"></i><?= Html::encode($this->title) ?>
    </h1>

    <p>
        Yii2 redis database manager;<br/>
        Module source <?=Html::a('<i class="icon github"></i>'.'https://github.com/Insolita/yii2-redisman','https://github.com/Insolita/yii2-redisman')?><br/>
        Application source <?=Html::a('<i class="icon github"></i>'.'https://github.com/Insolita/yii2-redisman-app','https://github.com/Insolita/yii2-redisman-app');?><br/>
        More information coming soon

    </p>

 </div>
