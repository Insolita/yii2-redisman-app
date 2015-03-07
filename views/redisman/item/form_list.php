<?php
use insolita\redisman\Redisman;

/**
 * @var \yii\web\View                                    $this
 * @var \insolita\redisman\controllers\DefaultController $context
 * @var \insolita\redisman\Redisman                      $module
 * @var \insolita\redisman\models\RedisItem              $model
 */
?>

<div class="ui top attached tabular menu">
    <div class="active item" data-tab="tabedit"><?=Redisman::t('redisman','Edit')?></div>
    <div class="item" data-tab="tabappend"><?=Redisman::t('redisman','Append')?></div>
</div>
<p><?=Redisman::t('redisman','Enter each new value with new line')?></p>

<div class="ui bottom attached active tab segment"  data-tab="tabedit">

        <?php $form = \Zelenin\yii\SemanticUI\widgets\ActiveForm::begin(
            [
                'action' => ['/redisman/item/update','key'=>urlencode($model->key)]
            ]
        )?>


    <div class="one">
        <?php
        echo $form->field($model, 'formatvalue')->widget(
            \lav45\aceEditor\AceEditorWidget::className(), [
                'mode' => 'text',
                'fontSize' => 15,
                'height' => 200,
                'options'=>[ 'id'=>'editfield']
            ]
        );
        ?>
    </div><br/>
    <div class="one">
        <button class="ui blue icon button submit"><i class="save icon"></i><?= Yii::t('app', 'Replace') ?>
        </button>
    </div>
    <?php \Zelenin\yii\SemanticUI\widgets\ActiveForm::end() ?>
     </div>

<div class="ui bottom attached  tab segment"  data-tab="tabappend">
        <?php $form = \Zelenin\yii\SemanticUI\widgets\ActiveForm::begin(
            [
                'action' => ['/redisman/item/append','key'=>urlencode($model->key)]
            ]
        )?>

    <div class="one">
        <?php
        $model->formatvalue='';
        echo $form->field($model, 'formatvalue')->widget(
            \lav45\aceEditor\AceEditorWidget::className(), [
                'mode' => 'text',
                'fontSize' => 15,
                'height' => 200,
                'options'=>[ 'id'=>'appendfield']

            ]
        );
        ?>
    </div>    <br/>

    <div class="one">
        <button class="ui blue icon button submit"><i class="save icon"></i><?=  Redisman::t('redisman', 'Append') ?>
        </button>
    </div>
    <?php \Zelenin\yii\SemanticUI\widgets\ActiveForm::end() ?>

</div>