<?php

namespace backend\models\Pc;

use Yii;

/*
 * ---------------------------------------
 *
 * ---------------------------------------
 */
class Room extends \common\modelsgii\Room
{

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
//            'timestamp' => [
//                'class' => 'yii\behaviors\TimestampBehavior',
//                'createdAtAttribute' => 'create_time',
//                'updatedAtAttribute' => 'update_time',
//                'value' => time(),
//            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time','update_time'], 'integer'],
            [['roomname','roomadmin','roomtime','roompass','agent','version'], 'string', 'max' => 255],
//            ['roomadmin', 'exist']
        ];
    }
    
    
}
