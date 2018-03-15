<?php

namespace backend\controllers;

use backend\models\User;
use backend\models\UserStopLog;
use backend\models\UserTip;
use common\aliyunDysms\api_demo\SmsDemo;
use common\helpers\Sms;
use common\helpers\Wechat;
use EasyWeChat\Message\Text;
use Yii;
use backend\models\search\UserStopLogSearch;
use yii\web\NotFoundHttpException;

/**
 * 用户停车控制器
 * @author longfei <phphome@qq.com>
 */
class UserStopLogController extends BaseController
{

    /**
     * ---------------------------------------
     * 列表页
     * ---------------------------------------
     */
    public function actionIndex()
    {
        /* 添加当前位置到cookie供后续跳转调用 */
        $this->setForward();

        $searchModel = new UserStopLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * ---------------------------------------
     * 手动提醒用户
     * ---------------------------------------
     */
    public function actionTip($id)
    {
        $UserStopLog = $this->findModel($id);
        if($UserStopLog -> status == 1){ // 已经结束的记录不需要提醒
            throw new NotFoundHttpException(' 已经结束的记录不需要提醒.');
        }
        if($UserStopLog -> is_tip == 2){ // 已经提醒了，就不要继续提醒
            throw new NotFoundHttpException('已经提醒了，就不要继续提醒.');
        }

        $user = User::find()->where(['uid' => $UserStopLog->uid]) ->one();
        if(!$user){
            throw new NotFoundHttpException('用户不存在');
        }


        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // 改变记录为已提醒
            $UserStopLog -> is_tip = 2;
            $UserStopLog -> update_time = time();
            if(!$UserStopLog -> save()){
                $transaction -> rollBack();
            }

            // 新增提醒记录
            $UserTip = new UserTip();
            $UserTip -> uid = $UserStopLog -> uid;
            $UserTip -> route_id = $UserStopLog -> id;
            $UserTip -> remark = $UserStopLog -> remark;
            $UserTip -> create_time = time();
            $UserTip -> status = 1; //提醒成功，用户未挪车
            if(!$UserTip -> save()){
                $transaction -> rollBack();
            }

            $wechat = Wechat::wxInit();
            $staff =  $wechat -> staff;
            $content = "《新罗停车无忧》提醒您，您当前停车位置（".$UserStopLog -> remark."）有被贴罚单的风险，请您尽快挪车。";
            $message = new Text(['content' => $content]);
            $res = $staff->message($message)->to($user -> openid)->send();
            if(!$res){
                $transaction -> rollBack();
            }

            //发送短信通知
            if($user -> mobile){
                SmsDemo::sendSms($user->mobile,$UserStopLog -> remark);
            }

            $transaction -> commit();

            $this->success('提醒成功', '/admin/user-stop-log');
        }catch (\Exception $e){
            $transaction -> rollBack();
            Yii::error(sprintf("failed to tip user cos %s",$e ->getMessage()));
            throw new NotFoundHttpException('系统错误');
        }


    }

    public function actionStopMap(){

        $userStopLog = UserStopLog::find()->select(['latitude','longitude'])->asArray()->all();

        return $this->render('stop-map',['userStopLog' =>$userStopLog] );
    }


    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserStopLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if ($id == 0) {
            throw new NotFoundHttpException('The requested page does not exist1.');
        }
        if (($model = UserStopLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist2.');
        }
    }


}
