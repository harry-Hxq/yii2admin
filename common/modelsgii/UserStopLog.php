<?php

namespace common\modelsgii;

use Yii;

/**
 * This is the model class for table "{{%user_stop_log}}".
 *
 * @property string $id ID
 * @property int $uid uid
 * @property string $latitude 地理位置纬度
 * @property string $longitude 地理位置经度
 * @property string $precision 地理位置精度
 * @property string $remark 备注
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 * @property int $status 状态
 */
class UserStopLog extends \common\core\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_stop_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'create_time', 'update_time', 'status'], 'integer'],
            [['latitude', 'longitude', 'precision'], 'number'],
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
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'precision' => 'Precision',
            'remark' => 'Remark',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'status' => 'Status',
        ];
    }
}
