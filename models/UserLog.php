<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 07.03.15
 * Time: 20:50
 */

namespace app\models;


use yii\data\ActiveDataProvider;
use yii\redis\ActiveRecord;


/**
 * Class UserLog
 *
 * @package app\models
 *
 * @property integer $id
 * @property string $ip
 * @property string $userAgent
 * @property integer $lastvisit
 * @property integer $logincount
 */
class UserLog extends ActiveRecord{

     /**
     * @return array
     */
    public function attributes(){
        return ['id','ip','userAgent','lastvisit','logincount'];
    }

    public function rules()
    {
        return [
            [['id', 'lastvisit','logincount'], 'integer'],
            [['userAgent'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(){
        return [
            'id'=>'ID',
            'ip'=>\Yii::t('app','Ip-Adress'),
            'userAgent'=>\Yii::t('app','UserAgent'),
            'lastvisit'=>\Yii::t('app','Login time'),
            'logincount'=>\Yii::t('app','Login times'),
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
     * @return \yii\db\ActiveQueryInterface
     */
    public function getActions(){
        return self::hasMany(ActionLog::className(),['user_id'=>'id']);
    }

    /**
     * @param $ip
     *
     * @return UserLog
     */
    public static function findByIp($ip){
        return self::findOne(['ip'=>$ip]);
    }


} 