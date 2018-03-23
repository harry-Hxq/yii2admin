<?php

namespace backend\models;

use Yii;

/*
 * ---------------------------------------
 * 路线模型
 * ---------------------------------------
 */
class RouteMap extends \common\modelsgii\RouteMap
{
    const TYPE_MOTO = 1; //motor
    const TYPE_CAR = 2; //car
    const TYPE_TIME_MORNING = 1;
    const TYPE_TIME_AFTERNOON = 2;
    const TYPE_TIME_EVENING = 3;
    const TYPE_TIME_ALLDAY = 4;


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            /**
             * 写库和更新库时，时间自动完成
             * 注意rules验证必填时可使用AttributeBehavior行为，model的EVENT_BEFORE_VALIDATE事件
             */
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => time(),
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type','time_type','remark','latitude','longitude','route_date'], 'required'],
            [['remark'], 'string', 'max' => 255],
        ];
    }
    
    
}
