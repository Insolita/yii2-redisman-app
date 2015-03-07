<?php
use yii\helpers\Html;
use Zelenin\yii\SemanticUI\widgets\GridView;

/**
 * @var \yii\web\View $this
 * @var \insolita\redisman\controllers\DefaultController $context
 * @var \insolita\redisman\Redisman $module
 * @var \yii\data\ArrayDataProvider $dataProvider
 */
$module=$this->context->module;
$this->title=$module->getCurrentName();
?>

<div class="ui orange segment">

    <?php
    echo GridView::widget([
            'dataProvider'=>$dataProvider,
            'columns'=>[
                [ 'class'=>'\Zelenin\yii\SemanticUI\widgets\CheckboxColumn'],
                'key'
                ,'type','size','ttl',
                [
                    'class'=>'\yii\grid\ActionColumn',
                    'template'=>'{quick} {view}  {delete}',
                    'buttons' => [
                        'quick' => function ($url, $model) {
                            return  Html::a(
                                '<i class="icon circular large eye green"></i>',
                                \yii\helpers\Url::to(['/redisman/item/quick', 'key' => urlencode($model['key'])]),
                                ['data-pjax' => 0, 'class'=>'modalink', 'title'=>Yii::t('app','Quick View')]
                            );
                        },
                        'view' => function ($url, $model) {
                            return  Html::a(
                                '<i class="icon circular inverted eye green"></i>',
                                \yii\helpers\Url::to(['/redisman/item/view', 'key' => urlencode($model['key'])]),
                                ['data-pjax' => 0, 'title'=>Yii::t('app','View')]
                            );
                        },
                        'delete' => function ($url, $model) {
                            return  Html::a(
                                '<i class="icon circular small  trash red"></i>',
                                    \yii\helpers\Url::to(['/redisman/item/delete']),
                                    [
                                        'data-pjax' => 0,
                                        'data-params'=>['RedisItem[key]' => urlencode($model['key'])],
                                        'data-confirm' => 'Подтвердите действие', 'data-method' => 'post'
                                        , 'title'=>Yii::t('app','Delete')
                                    ]
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
        'header' => \insolita\redisman\Redisman::t('redisman','Key Information'),
        'actions'=>\Zelenin\yii\SemanticUI\Elements::button(\insolita\redisman\Redisman::t('redisman','Close'), ['class' => 'black'])
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


/** **/
?>
