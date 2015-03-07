<?php
use insolita\redisman\Redisman;
use yii\helpers\Html;
use Zelenin\yii\SemanticUI\widgets\DetailView;

/**
 * @var \yii\web\View                                    $this
 * @var \insolita\redisman\controllers\DefaultController $context
 * @var \insolita\redisman\Redisman                      $module
 * @var \insolita\redisman\models\RedisItem              $model
 */
$module = $this->context->module;
$this->title = $module->getCurrentName();
?>

<div class="ui raised segment">
    <a class="ui ribbon teal label"><?= Redisman::keyTyper($model->type) ?></a>
    <span><i class="icon privacy"></i><?= Html::encode($model->key) ?></span>
    <?php echo DetailView::widget(
        [
            'model' => $model,
            'attributes' => [
                'size', 'ttl',
                'refcount', 'idletime', 'db',
                'encoding',
                [
                    'label' => Redisman::t('redisman', 'Value'), 'format' => 'raw'
                    , 'value' => (is_array($model->value) ? Html::encode(\yii\helpers\VarDumper::dumpAsString($model->value, 10, false))
                    : Html::encode($model->formatvalue))
                ]
            ]
        ]
    )?>
</div>