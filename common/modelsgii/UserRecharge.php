<?php

namespace common\modelsgii;

use Yii;

/**
 * This is the model class for table "{{%user_recharge}}".
 *
 * @property string $id ID
 * @property int $uid uid
 * @property string $openid openid
 * @property string $out_trade_no 商户订单号
 * @property int $total_fee 总金额
 * @property string $spbill_create_ip 终端IP
 * @property string $time_start 交易起始时间
 * @property string $time_expire 交易结束时间
 * @property string $notify_url 通知地址
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 * @property int $status 状态
 */
class UserRecharge extends \common\core\BaseActiveRecord
{

    const STATUS_PAID_SUCCESSFULLY = 1;
    const STATUS_PAID_FAILED = 0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_recharge}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'total_fee', 'create_time', 'update_time', 'status'], 'integer'],
            [['openid', 'out_trade_no', 'notify_url'], 'string', 'max' => 255],
            [['spbill_create_ip', 'time_start', 'time_expire'], 'string', 'max' => 20],
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
            'openid' => 'Openid',
            'out_trade_no' => 'Out Trade No',
            'total_fee' => 'Total Fee',
            'spbill_create_ip' => 'Spbill Create Ip',
            'time_start' => 'Time Start',
            'time_expire' => 'Time Expire',
            'notify_url' => 'Notify Url',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'status' => 'Status',
        ];
    }
}
