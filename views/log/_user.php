<div class="ui raised segment">
    <a class="ui ribbon teal label"><?= Yii::t('app','User Information')?></a>
    <span><i class="icon user"></i><?= $model->ip ?></span>
    <?php echo \Zelenin\yii\SemanticUI\widgets\DetailView::widget(
        [
            'model' => $model,
            'attributes' => [
                'ip', 'userAgent','logincount',
                [
                    'attribute' => 'lastvisit'
                    , 'value' => date('d.m.Y H:i',$model->lastvisit)
                ]
            ]
        ]
    )?>
</div>