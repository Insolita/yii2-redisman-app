<?php
use insolita\redisman\Redisman;

/**
 * @var \yii\web\View                                    $this
 * @var \insolita\redisman\controllers\DefaultController $context
 * @var \insolita\redisman\Redisman                      $module
 * @var \insolita\redisman\models\RedisItem              $model
 * @var string                           $lastlog
 */
$module = $this->context->module;
$this->title = $module->getCurrentName();
?>

<div class="ui green pointed segment">
    <h1 class="ui header">
        <div class="sub header "><i class="icon plus circle"></i>
            <?= Redisman::t('redisman', 'Add key - {0}', $model->type)?>
        </div>
    </h1>
    <div class="ui two column grid">
        <div class="column">
            <div class="ui raised segment">
                <a class="ui ribbon teal label"><?= Redisman::t('redisman', 'Fill form')?></a>
                <span><?= Redisman::t('redisman', 'Fields with * required')?></span>
                <?php
                $form = \Zelenin\yii\SemanticUI\widgets\ActiveForm::begin(
                    [
                        'action' => ['/redisman/item/create', 'type' => $model->type]
                    ]
                );?>
                <div class="one">
                    <?php echo $form->field($model, 'key')->textInput([]); ?>
                    <br/>
                </div>
                <div class="one">
                    <?php
                    switch ($model->type) {
                    case Redisman::REDIS_STRING:
                        echo $form->field($model, 'formatvalue')->widget(
                            \lav45\aceEditor\AceEditorWidget::className(), [

                                'mode' => 'text',
                                'fontSize' => 15,
                                'height' => 200,

                            ]
                        );
                        break;
                    case Redisman::REDIS_LIST:
                    case Redisman::REDIS_SET:
                        echo '<p>' . Redisman::t('redisman', 'Enter each new value with new line') . '</p>';
                        echo $form->field($model, 'formatvalue')->widget(
                            \lav45\aceEditor\AceEditorWidget::className(), [

                                'mode' => 'text',
                                'fontSize' => 15,
                                'height' => 200,

                            ]
                        );
                        break;
                    case Redisman::REDIS_HASH:
                        echo ' <table class="ui table bordered"><thead>
                                <th>' . Redisman::t('redisman', 'field') . '</th>
                                <th>' . Redisman::t('redisman', 'value') . '</th>
                                </thead>';
                        for ($i = 0; $i <= 10; $i++) {
                            echo '<tr>
                                  <td><input type="text" name="RedisItem[formatvalue][' . $i . '][field]" value=""></td>
                                  <td><input type="text" name="RedisItem[formatvalue][' . $i . '][value]" value=""></td>
                                  </tr>';
                        }
                        echo '</table>';
                        break;
                    case Redisman::REDIS_ZSET:
                        echo '<table class="ui table bordered"><thead>
                              <th>' . Redisman::t('redisman', 'field') . '</th>
                              <th>' . Redisman::t('redisman', 'score') . '</th>
                              </thead>';
                        for ($i = 0; $i <= 10; $i++) {
                            echo '   <tr>
                                     <td><input type="text" name="RedisItem[formatvalue][' . $i . '][field]" value=""></td>
                                     <td><input type="text" name="RedisItem[formatvalue][' . $i . '][score]" value=""></td>
                                     </tr>';
                        }
                        echo '</table>';
                        break;
                    }
                    ?>
                </div>
                <div class="one">
                    <?php echo $form->field($model, 'ttl')->textInput(['class' => 'small'])->hint(Redisman::t('redisman','Unexpired by default')); ?>
                </div>
                <br/>

                <div class="one">
                    <button class="ui blue icon button submit"><i class="save icon"></i><?= Yii::t('app', 'Save') ?>
                    </button>
                </div>
                <?php \Zelenin\yii\SemanticUI\widgets\ActiveForm::end();?>
            </div>
        </div>
        <div class="column">
            <div class="ui segment">
                <a class="ui right ribbon blue label"><?= Redisman::t('redisman', 'Operations log') ?></a>
                <div style="min-height: 250px;max-height: 600px" class="ui bulleted divided list">
                    <?php foreach($lastlog as $log):?>
                        <?=\yii\helpers\Html::tag('div',$log,['class'=>'item'])?>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    </div>
</div>