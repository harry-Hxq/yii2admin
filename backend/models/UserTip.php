<?php

namespace backend\models;

use Yii;

/*
 * ---------------------------------------
 * 用户提现模型
 * ---------------------------------------
 */
class UserTip extends \common\modelsgii\UserTip
{
    public $username;
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

    
    
}
