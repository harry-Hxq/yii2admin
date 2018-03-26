<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use console\models\Picture;

/*
 * 停车模块
 */
class StopController extends Controller
{
    /*
     * ---------------------------------------
     * 更新会员状态（将过期的用户标记未已过期）
     * ---------------------------------------
     */
    public function actionUpdateUser()
    {
        Yii::info("start to update user",__METHOD__);
        $db = Yii::$app->db;
        $now = strtotime(date("Y-m-d")) - 86400;
        $raw_sql = sprintf("update yii2_user set deadline = %d WHERE deadline = %d;",0, $now);
        $db->createCommand($raw_sql)->execute();
    }

}
