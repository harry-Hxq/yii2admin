<?php

namespace backend\models;

use Yii;

/*
 * ---------------------------------------
 * 路线模型
 * ---------------------------------------
 */
class Route extends \common\modelsgii\Route
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
            [['title','start_time','end_time'], 'required'],
            [['remark'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 32,'min' => 3]
        ];
    }
    
    
}
