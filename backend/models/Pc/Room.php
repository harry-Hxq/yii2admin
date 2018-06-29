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
            [['create_time','update_time','admin_id'], 'integer'],
            [['roomname','roomadmin','roomtime','roompass','agent','version','roompassshow'], 'string', 'max' => 255,'min' =>3],
//            ['username', 'unique', 'targetClass' => '\frontend\models\User', 'message' => '用户名已存在.'],
            ['roomadmin', 'unique','targetClass' => '\backend\models\Pc\Room','message' => '房间账户已近存在.',]
        ];
    }

    public static function getRoomUserTotal($roomid){
        $db = Yii::$app->db2;
        $res = $db -> createCommand(sprintf("select count(*) as allpeople from fn_user where `roomid` = %d and `jia` = '%s' and `money` > %d",$roomid,'false',0))->queryOne();
        return intval($res['allpeople']);
    }

    public static function getRoomUser($roomid){
        $db = Yii::$app->db2;
        $time = time()-300;
        $sql = sprintf("select count(*) as online from fn_user where `roomid` = %d and `statustime` > %d and `jia` = '%s' ",$roomid,$time,'false');
        $res = $db -> createCommand($sql)->queryOne();
        return intval($res['online']);
    }


    
}
