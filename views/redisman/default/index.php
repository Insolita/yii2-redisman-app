<?php
use \insolita\redisman\Redisman;
use Zelenin\yii\SemanticUI\modules\Accordion;
use \yii\helpers\Html;
/**
 * @var \yii\web\View $this
 * @var \insolita\redisman\controllers\DefaultController $context
 * @var \insolita\redisman\Redisman $module
 * @var array $info
 */
 $module=$this->context->module;

$this->title=$module->getCurrentName();
$infoformat=[];
foreach($info as $section=>$data){
    $content='';
    foreach($data as $key=>$val){
        if($key=='rdb_last_save_time'){
            $val=date('d.m.Y H:i:s',$val);
        }
        $content.=Html::tag('tr', Html::tag('td', Redisman::t('redisman',$key)). Html::tag('td', $val));
    }

    $infoformat[]=[
        'title'=>Redisman::t('redisman',$section),
        'content'=>Html::tag('table',$content,['class'=>"ui definition table"])
    ];
}
?>

<div class="ui blue segment">
    <?php
echo Accordion::widget([
        'styled' => true,
        'fluid' => true,
        'contentOptions' => [
            'encode' => false
        ],
        'items' => $infoformat
    ]);?>
</div>