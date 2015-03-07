<div class="ui centered padded stackable grid">
<?= \Zelenin\yii\SemanticUI\collections\Menu::widget(
    [
        'topAttached' => true,
        'fluid' => true,
        'inverted' => true,
        'options'=>['class'=>'centered'],
        'items' => [
            [
                'url' => ['/'],
                'label' => Yii::t('app','Main')
            ],
            [
                'url' => ['/site/about'],
                'label' => Yii::t('app','About')
            ],
            [
                'url' => ['/log/index'],
                'label' => Yii::t('app','Actions Log')
            ],
            [
                'label' => Yii::t('app','Language'),
                'items'=>[
                    [
                        'url' => ['/redisman/default/index','lang'=>'ru'],
                        'label' => Yii::t('app','Russian')
                    ],
                    [
                        'url' => ['/redisman/default/index','lang'=>'en'],
                        'label' => Yii::t('app','English')
                    ],
                ]
            ],
            [
                'url' => ['/site/logout'],
                'options'=>['data-method'=>'post'],
                'label' => Yii::t('app','Logout'),
                'visible'=>!Yii::$app->user->isGuest
            ],

        ],

    ]
);
?>
</div>