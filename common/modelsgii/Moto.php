<?php

namespace common\modelsgii;

use Yii;

/**
 * This is the model class for table "{{%ad}}".
 *
 * @property integer $id
 * @property string $image
 * @property integer $type
 * @property string $title
 * @property string $url
 * @property integer $sort
 * @property integer $status
 */
class Moto extends \common\core\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%moto}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','end_time','latitude','longitude','precision'], 'required'],
            [['start_time', 'end_time', 'create_time','update_time','status'], 'integer'],
            [['remark'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'latitude' => '地理位置纬度',
            'longitude' => '地理位置经度',
            'precision' => '地理位置精度',
            'remark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'status' => '状态',
        ];
    }
}
