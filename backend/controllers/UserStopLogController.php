<?php

namespace backend\controllers;

use Yii;
use backend\models\search\UserStopLogSearch;

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

}
