<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;


/**
 * 这里注意是继承 yii\rest\ActiveController 因为源码中已经帮我们实现了index/update等方法
 * 以及其访问规则verbs()等，
 * 其他可参考：http://www.yiichina.com/doc/guide/2.0/rest-controllers
 *
 * 权限采用最简单的QueryParamAuth方式
 * 用户角色权限比较复杂，这里没有做
 *
 * @package api\modules\v1\controllers
 */
class PcController extends ActiveController
{
    public $modelClass = 'api\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        /* 设置认证方式 */
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'optional' => [
                'get-data',
                'get-all-data',
            ]
        ];
        return $behaviors;
    }

    /**
     * 获取数据
     */
    public function actionGetData(){
        $db = \Yii::$app->db2;
        $data[] = $db -> createCommand(sprintf("select `type`,`term`,`code`,`time`,`next_term`,`next_time` from fn_open WHERE `type` = %d ORDER by `term` DESC limit 1",1))->queryOne();
        $data[] = $db -> createCommand(sprintf("select `type`,`term`,`code`,`time`,`next_term`,`next_time` from fn_open WHERE `type` = %d ORDER by `term` DESC limit 1",2))->queryOne();
        $data[] = $db -> createCommand(sprintf("select `type`,`term`,`code`,`time`,`next_term`,`next_time` from fn_open WHERE `type` = %d ORDER by `term` DESC limit 1",3))->queryOne();
        $data[] = $db -> createCommand(sprintf("select `type`,`term`,`code`,`time`,`next_term`,`next_time` from fn_open WHERE `type` = %d ORDER by `term` DESC limit 1",4))->queryOne();
        $data[] = $db -> createCommand(sprintf("select `type`,`term`,`code`,`time`,`next_term`,`next_time` from fn_open WHERE `type` = %d ORDER by `term` DESC limit 1",5))->queryOne();
        return $data;
    }


    /**
     * 获取所有数据
     */
    public function actionGetAllData(){
        $db = \Yii::$app->db2;
        $dates = date("Y-m-d")." H:i:s";
        $data[1] = $db -> createCommand(sprintf("select `type`,`term`,`code`,`time`,`next_term`,`next_time` from fn_open WHERE `type` = %d AND `time` > '%s' ",1,$dates))->queryAll();
        $data[2] = $db -> createCommand(sprintf("select `type`,`term`,`code`,`time`,`next_term`,`next_time` from fn_open WHERE `type` = %d AND `time` > '%s' ",2,$dates))->queryAll();
        $data[3] = $db -> createCommand(sprintf("select `type`,`term`,`code`,`time`,`next_term`,`next_time` from fn_open WHERE `type` = %d AND `time` > '%s' ",3,$dates))->queryAll();
        $data[4] = $db -> createCommand(sprintf("select `type`,`term`,`code`,`time`,`next_term`,`next_time` from fn_open WHERE `type` = %d AND `time` > '%s' ",4,$dates))->queryAll();
        $data[5] = $db -> createCommand(sprintf("select `type`,`term`,`code`,`time`,`next_term`,`next_time` from fn_open WHERE `type` = %d AND `time` > '%s' ",5,$dates))->queryAll();
        return $data;
    }





}
