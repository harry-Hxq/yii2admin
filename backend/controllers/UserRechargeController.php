<?php

namespace backend\controllers;

use backend\models\search\UserRechargeSearch;
use Yii;

/**
 * 订到控制器
 * @author longfei <phphome@qq.com>
 */
class UserRechargeController extends BaseController
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

        $searchModel = new UserRechargeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
