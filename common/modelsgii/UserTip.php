<?php

namespace common\modelsgii;

use Yii;

/**
 * This is the model class for table "{{%user_tip}}".
 *
 * @property string $id ID
 * @property int $uid uid
 * @property int $route_id 路线id
 * @property string $remark 备注
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 * @property int $status 状态
 */
class UserTip extends \common\core\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_tip}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'route_id', 'create_time', 'update_time', 'status'], 'integer'],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'route_id' => 'Route ID',
            'remark' => 'Remark',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'status' => 'Status',
        ];
    }
}
