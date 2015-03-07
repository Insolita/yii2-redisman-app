<?php
use insolita\redisman\Redisman;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \insolita\redisman\controllers\DefaultController $context
 * @var \insolita\redisman\Redisman $module
 */
$module = $this->context->module;
$dbselect = $module->dbList()
?>

<div class="ui vertical menu pointing fluid">
    <div class="header item">
       <i class="icon options"></i> <?= Redisman::t('redisman','Connection Settings') ?>
    </div>
    <div class="text item">
        <?= $this->render('_connectform') ?>
    </div>
    <div class="header item">
        <i class="icon search"></i><?= Redisman::t('redisman','Search Key') ?>
    </div>
    <div class="text item">
        <?= $this->render('_searchform') ?>
    </div>
    <div class="header item">
        <i class="icon database"></i> <?= Redisman::t('redisman','Db Operations') ?>
    </div>
         <?= Html::a('<i class="icon save"></i> '.Redisman::t('redisman','Save database'), ['/redisman/default/savedb'], ['class'=>'active green item']) ?>
          <?= Html::a(
              '<i class="icon trash outline"></i> '.Redisman::t('redisman','Flush database'), ['/redisman/default/flushdb'],
            ['data-method' => 'post', 'data-confirm' => Redisman::t('redisman','You really want to do it?'),'class'=>'active red item']
        ) ?>
 </div>