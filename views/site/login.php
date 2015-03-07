<?php
use yii\helpers\Html;
use Zelenin\yii\SemanticUI\Elements;
use Zelenin\yii\SemanticUI\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form \Zelenin\yii\SemanticUI\widgets\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = Yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ui attached icon message">
    <i class="sign in icon"></i>

    <div class="content">
        <div class="header">
            <?= Html::encode($this->title) ?>
        </div>
        <p><?=Yii::t('app', 'Fill out the form below to sign-in and manage Redis Database')?></p>
    </div>
</div>
<?php $form = \Zelenin\yii\SemanticUI\widgets\ActiveForm::begin(
    [
        'id' => 'login-form', 'options' => ['class' => 'ui form attached fluid segment'],
        'enableAjaxValidation'=>false,
        'enableClientValidation'=>false,
        'action'=>\yii\helpers\Url::to([''])
    ]
); ?>
<?= $form->errorSummary($model) ?>
<div class="one">
    <?= $form->field($model, 'username',['options'=>['class'=>($model->hasErrors('username')?'field error':'field')]])->textInput() ?>
</div>
<div class="one">

    <?= $form->field($model, 'password',['options'=>['class'=>($model->hasErrors('username')?'field error':'field')]])->passwordInput() ?>
</div>
<div class="inline field">
    <?= $form->field($model, 'rememberMe')->checkbox() ?>
</div>
<?= Elements::button(
    '<i class="sign in icon"></i>' . Yii::t('app', 'Login'), ['class' => 'green', 'type' => 'submit','tag'=>'button']
) ?>
<?php ActiveForm::end(); ?>
<div class="ui bottom attached info icon message">
    <i class="info icon"></i>

    <div class="content">
        <div class="header">
            <?= Yii::t('app', 'Don`t know password?') ?>
        </div>
        <?= Yii::t(
            'app', 'To modify the username/password, please check out the code'
        ) ?><code> app\models\User::$users</code><?=Html::a('<i class="icon forward large"></i>','https://github.com/Insolita/yii2-redisman-app/blob/master/models/User.php',['target'=>'_blank'])?>
    </div>
</div>

