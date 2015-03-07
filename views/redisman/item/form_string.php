<p>
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

        ]
    );
    ?>
</div>    <br/>

<div class="one">
    <button class="ui blue icon button submit"><i class="save icon"></i><?= Yii::t('app', 'Replace') ?>
    </button>
</div>
<?php \Zelenin\yii\SemanticUI\widgets\ActiveForm::end() ?>
</p>