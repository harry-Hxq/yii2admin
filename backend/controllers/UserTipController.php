<?php

namespace backend\controllers;

use backend\models\search\UserTipSearch;
use Yii;
use backend\models\search\UserStopLogSearch;

/**
 * 用户提现记录控制器
 * @author longfei <phphome@qq.com>
 */
class UserTipController extends BaseController
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

        $searchModel = new UserTipSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
