<?php
use Zelenin\yii\SemanticUI\widgets\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
/**
 * @var $this yii\web\View
 * @var \app\models\UserLog $model
 * @var \yii\data\ActiveDataProvider $dp
 */

$this->title = Yii::t('app', 'User Log');
?>
    <div class="ui orange segment">

        <?php
        echo GridView::widget([
                'filterModel'=>$model,
                'dataProvider'=>$dp,
                'columns'=>[
                    [ 'attribute'=>'connection','filter'=>Yii::$app->getModule('redisman')->connectionList()],
                    [ 'attribute'=>'db'],
                    [ 'attribute'=>'time','value'=>function($model){return date('d.m.Y H:i',$model->time);},'filter'=>false],
                    [ 'attribute'=>'user.ip'],
                    [ 'attribute'=>'command','filter'=>false],
                    [
                        'class'=>'\yii\grid\ActionColumn',
                        'template'=>'{user}',
                        'buttons' => [
                            'user' => function ($url, $model) {
                                return  Html::a(
                                    '<i class="icon circular large user green"></i>',
                                    \yii\helpers\Url::to(['/log/user', 'id' => $model->user_id]),
                                    ['data-pjax' => 0, 'class'=>'modalink', 'title'=>Yii::t('app','User Info')]
                                );
                            },
                        ]
                    ]

                ]
            ])?>
    </div>
<?php
$modal =\Zelenin\yii\SemanticUI\modules\Modal::begin([
        'id'=>'quickmodal',
        'size' => \Zelenin\yii\SemanticUI\modules\Modal::SIZE_LARGE,
        'header' => Yii::t('app','User Information'),
        'actions'=>\Zelenin\yii\SemanticUI\Elements::button(Yii::t('app','Close'), ['class' => 'black'])
    ]);
?>
    <div class="content"></div>
<?php
$modal::end();

$this->registerJs('
   $(document).on("click",".modalink",function(e){
       e.preventDefault();
       var url=$(this).attr("href");
$.get(url,function(data){
$("#quickmodal .content").html(data);
$("#quickmodal").modal({onHide:function(){ $("#quickmodal .content").html("");}}).modal("show");
});

   });
');

?>