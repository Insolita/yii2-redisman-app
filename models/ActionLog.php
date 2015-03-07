<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 07.03.15
 * Time: 20:54
 */

namespace app\models;


use yii\data\ActiveDataProvider;
use yii\redis\ActiveRecord;

/**
 * Class ActionLog
 *
 * @package app\models
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $operation
 * @property integer $db
 * @property string $connection
 * @property string $command
 * @property integer $time
 */
class ActionLog extends ActiveRecord{

    public $ip;
    /**
     * @return array
     */
    public function attributes(){
        return ['id','user_id','operation','db','connection','command','time'];
    }
    public function rules()
    {
        return [
            [['time','db'], 'integer'],
            [['ip','operation','command','connection'], 'string'],
        ];
    }
    /**
     * @return array
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @return array
     */
    public function attributeLabels(){
        return [
            'id'=>'ID',
            'user_id'=>\Yii::t('app','User'),
            'operation'=>\Yii::t('app','Operation'),
            'connection'=>\Yii::t('app','Connection'),
            'db'=>\Yii::t('app','DB'),
            'command'=>\Yii::t('app','Command'),
            'time'=>\Yii::t('app','Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser(){
        return self::hasOne(UserLog::className(),['id'=>'user_id']);
    }

    /**
     * @param \insolita\redisman\events\ModifyEvent $event
     */
    public static function log($event){
        $ip=\Yii::$app->request->getUserIP();
        $user=UserLog::findByIp($ip);
        if(!$user){
            $user=new UserLog();
            $user->ip=$ip;
            $user->logincount=1;
            $user->userAgent=\Yii::$app->request->getUserAgent();
            $user->lastvisit=time();
            $user->save(false);
        }
        $model=new ActionLog();
        $model->user_id=$user->id;
        $model->operation=$event->operation;
        $model->db=$event->db;
        $model->command=$event->command;
        $model->connection=$event->connection;
        $model->time=time();
        $model->save(false);
     }

    public function search($params){
        $query=self::find();
        $query->with(['user']);

        $dp=new ActiveDataProvider([
                'query' => $query,
                'sort'=>false
            ]);
        if (!($this->load($params) && $this->validate())) {
            return $dp;
        }

        $query->andFilterWhere(
            [
                'connection' => $this->connection,
                'db'=>$this->db,
                'operation'=>$this->operation,
                'user.ip'=>$this->ip,
                'time'=>$this->time
            ]
        );

        return $dp;
    }
} 