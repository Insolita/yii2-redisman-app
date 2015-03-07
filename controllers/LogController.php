<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 07.03.15
 * Time: 20:49
 */

namespace app\controllers;


use app\models\ActionLog;
use app\models\UserLog;
use yii\web\Controller;
use yii\web\HttpException;

class LogController extends Controller{

    public function actionIndex(){
        $model=new ActionLog();
        $dp=$model->search(\Yii::$app->request->getQueryParams());
        return  $this->render('index',['model'=>$model,'dp'=>$dp]);
    }

    public function actionUser($id){
        if(\Yii::$app->request->isAjax){
            $model=UserLog::findOne(['id'=>$id]);
            if(!$model){
                throw new HttpException(404,\Yii::t('app','Page not found'));
            }
            return $this->renderAjax('_user',['model'=>$model]);
        }
        throw new HttpException(405,\Yii::t('app','Method not allowed'));
    }
} 