<?php
use Zelenin\yii\SemanticUI\Elements;
use Zelenin\yii\SemanticUI\widgets\ActiveForm;

/**
 * @var \yii\web\View                                    $this
 * @var \insolita\redisman\controllers\DefaultController $context
 * @var \insolita\redisman\Redisman                $module
 */
$module = $this->context->module;

$model = new \insolita\redisman\models\ConnectionForm();
$model->connection = $module->getCurrentConn();
$model->db = $module->getCurrentDb();
?>
<?php $form = \Zelenin\yii\SemanticUI\widgets\ActiveForm::begin(
    [
        'id' => 'login-form', 'options' => ['class' => 'ui form attached fluid segment'],
        'enableClientValidation' => true,
        'method' => 'post',
        'action' => \yii\helpers\Url::to(['/redisman/default/switch'])
    ]
); ?>
<?= $form->errorSummary($model) ?>
    <div class="one">
        <?= $form->field($model, 'connection')->dropDownList(
            $module->connectionList(),
            [
                'id' => 'currentcon'
            ]
        );
        ?>
    </div>
    <div class="one">

        <?= $form->field($model, 'db')->dropDownList($module->dbList(), ['id' => 'currentdb']) ?>
    </div><br/>
<div class="one right ui aligned">
<?= Elements::button(
    '<i class="plug icon"></i>' . \insolita\redisman\Redisman::t('redisman','Connect'),
    ['class' => 'green circular right  aligned', 'type' => 'submit', 'tag' => 'button']
) ?>    </div>

<?php ActiveForm::end(); ?>
<?php
$js = new \yii\web\JsExpression(
    'var url="' . \yii\helpers\Url::to(['/redisman/default/dbload']) . '";
var cur=$("#connectionform-connection").val();
   $("#currentcon").dropdown
   ({
      onChange: function(value, text, $selectedItem)
      {
          console.log(value + "|"+ cur);
          if(value && value!=cur)
          {
             cur=value;
             $.post
             (
                url,{"connection":value},
                 function( data )
                                    {
                                        if(data){$("#currentdb div.menu" ).html(data);$("#currentdb" ).dropdown("set selected",0);}
                                    }
             );
          }
      }
    });

'
);

$this->registerJs($js);
?>