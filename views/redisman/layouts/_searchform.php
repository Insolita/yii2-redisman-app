<?php
use Zelenin\yii\SemanticUI\widgets\ActiveForm;
use Zelenin\yii\SemanticUI\Elements;
use \insolita\redisman\Redisman;
/**
 * @var \yii\web\View $this
 * @var \insolita\redisman\controllers\DefaultController $context
 * @var \insolita\redisman\Redisman $module
 */
$module=$this->context->module;

$model=new \insolita\redisman\models\RedisModel();
$model->restoreFilter();
?>
<?php $form = \Zelenin\yii\SemanticUI\widgets\ActiveForm::begin(
    [
        'id' => 'login-form', 'options' => ['class' => 'ui form attached fluid'],
        'enableClientValidation'=>true,
        'method'=>'post',
        'action'=>\yii\helpers\Url::to(['/redisman/default/search'])
    ]
); ?>
<?= $form->errorSummary($model) ?>
    <div class="one">
        <?= $form->field($model, 'pattern')->textInput()->hint(Redisman::t('redisman','support redis patterns (*,?,[var])'))?>
    </div>
    <div class="one">

        <?= $form->field($model, 'type')->checkboxList([
                Redisman::REDIS_STRING=>Redisman::t('redisman','string'),
                Redisman::REDIS_HASH=>Redisman::t('redisman','hash'),
                Redisman::REDIS_LIST=>Redisman::t('redisman','list'),
                Redisman::REDIS_SET=>Redisman::t('redisman','set'),
                Redisman::REDIS_ZSET=>Redisman::t('redisman','zset')

            ])?>
    </div>
    <div class="one">
        <?= $form->field($model, 'perpage')->dropDownList([20=>20,30=>30,50=>50,100=>100,200=>200,500=>500])?>
    </div>
    <div class="one">
        <?= $form->field($model, 'encache')->checkbox([])?>
    </div><br/>
<div class="one right aligned">
<?= Elements::button(
    '<i class="find icon"></i>' . Yii::t('app', 'Search'), ['class' => 'teal circular right ui aligned', 'type' => 'submit','tag'=>'button']
) ?><?= \yii\helpers\Html::a(
    '<i class="remove icon"></i>' . Yii::t('app', 'Clear'),['/redisman/default/reset-search'], ['class' => 'ui button blue circular left aligned']
) ?></div>
<?php ActiveForm::end(); ?>