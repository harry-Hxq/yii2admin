<?php

namespace backend\controllers;

use backend\models\UserStopLog;
use backend\models\UserTip;
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

        // 改变记录为已提醒
        $UserStopLog -> is_tip = 2;
        $UserStopLog -> update_time = time();
        $UserStopLog -> save();

        // 新增提醒记录
        $UserTip = new UserTip();
        $UserTip -> uid = $UserStopLog -> uid;
        $UserTip -> route_id = $UserStopLog -> id;
        $UserTip -> remark = $UserStopLog -> remark;
        $UserTip -> create_time = time();
        $UserTip -> status = 1; //提醒成功，用户未挪车
        $UserTip -> save();

        # todo 发送微信信息，发送短信通知用户挪车，停车中的状态且应该挪车

        $this->success('提醒成功', '/admin/user-stop-log');

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
