<?php
use insolita\redisman\Redisman;

/**
 * @var \yii\web\View $this
 * @var \insolita\redisman\controllers\DefaultController $context
 * @var \insolita\redisman\Redisman $module
 * @var \insolita\redisman\models\RedisItem $model
 */
?>
<div>
    <div class="ui top attached tabular menu">
        <div class="active item" data-tab="tabedit"><?= Redisman::t('redisman', 'Edit') ?></div>
        <div class="item" data-tab="tabappend"><?= Redisman::t('redisman', 'Append') ?></div>
    </div>
    <p><?=Redisman::t('redisman','Empty fields will not saved')?></p>

    <div class="ui bottom attached active tab segment" data-tab="tabedit">

        <?php $form = \Zelenin\yii\SemanticUI\widgets\ActiveForm::begin(
            [
                'action' => ['/redisman/item/update', 'key' => urlencode($model->key)]
            ]
        )?>

        <div class="one">
            <?php
            \yii\widgets\Pjax::begin(['timeout' => 5000, 'id' => 'hashpjax', 'enablePushState' => false]);
            if ($model->hasErrors()) {
                echo \yii\helpers\Html::errorSummary($model, ['encode' => true]);
            }
            ?>
            <?php

            echo \Zelenin\yii\SemanticUI\widgets\GridView::widget(
                [
                    'dataProvider' => $model->formatvalue,
                    'columns' => [
                        'field',
                        [
                            'attribute' => 'value',
                            'format' => 'raw',
                            'value' => function ($data,$key,$index) use ($model) {
                                return
                                    '<input type="hidden" name="RedisItem[formatvalue]['.$index.'][field]" value="'
                                    .\yii\helpers\Html::encode( $data['field']) . '">
                                    <input type="text" name="RedisItem[formatvalue]['.$index.'][value]" value="'
                                    . \yii\helpers\Html::encode( $data['value']) . '">';
                            }
                        ],
                        [
                            'class' => '\yii\grid\ActionColumn',
                            'template' => '{remove}',
                            'buttons' => [
                                'remove' => function ($url, $data) use ($model) {
                                    return \yii\helpers\Html::a(
                                        '<i class="icon remove"></i>', [
                                            '/redisman/item/remfield', 'RedisItem[key]' => urlencode($model->key),
                                            'RedisItem[field]' => $data['field']
                                        ],
                                        ['title' => Redisman::t('redisman', 'Remove field'), 'data-pjax' => 1]
                                    );
                                }
                            ]
                        ]
                    ],

                ]
            )
            ?>
            <?php
            \yii\widgets\Pjax::end();
            ?>
        </div>
        <br/><br/>

        <div class="one">
            <button class="ui blue icon button submit"><i class="save icon"></i><?= Yii::t('app', 'Update') ?>
            </button>
        </div>
        <?php \Zelenin\yii\SemanticUI\widgets\ActiveForm::end() ?>
    </div>

    <div class="ui bottom attached  tab segment" data-tab="tabappend">

        <?php $form = \Zelenin\yii\SemanticUI\widgets\ActiveForm::begin(
            [
                'action' => ['/redisman/item/append', 'key' => urlencode($model->key)]
            ]
        )?>

        <input type="hidden" name="page" value="<?= (int)Yii::$app->request->get('page', 1) ?>">

        <div class="one">
            <table class="ui table bordered">
                <thead>
                <th><?= Redisman::t('redisman', 'field') ?></th>
                <th><?= Redisman::t('redisman', 'value') ?></th>
                </thead>
                <?php for ($i = 0; $i <= 10; $i++): ?>
                    <tr>
                        <td><input type="text" name="RedisItem[formatvalue][<?=$i?>][field]" value=""></td>
                        <td><input type="text" name="RedisItem[formatvalue][<?=$i?>][value]" value=""></td>
                    </tr>
                <?php endfor ?>
            </table>
        </div>
        <br/><br/>

        <div class="one">
            <button class="ui blue icon button submit"><i class="save icon"></i><?= Redisman::t('redisman', 'Append') ?>
            </button>
        </div>
        <?php \Zelenin\yii\SemanticUI\widgets\ActiveForm::end() ?>

    </div>
