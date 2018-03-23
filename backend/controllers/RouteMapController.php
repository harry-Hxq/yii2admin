<?php

namespace backend\controllers;

use backend\models\RouteMap;
use backend\models\search\RouteMapSearch;
use Yii;
use backend\models\Route;
use backend\models\search\RouteSearch;

/**
 * 路线控制器
 * @author longfei <phphome@qq.com>
 */
class RouteMapController extends BaseController
{
    /**
     * ---------------------------------------
     * 构造方法
     * ---------------------------------------
     */
    public function init()
    {
        parent::init();
    }

    /**
     * ---------------------------------------
     * 路线列表
     * ---------------------------------------
     */
    public function actionIndex()
    {
        /* 添加当前位置到cookie供后续操作调用 */
        $this->setForward();

        $searchModel = new RouteMapSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * ---------------------------------------
     * 添加
     * ---------------------------------------
     */
    public function actionAdd()
    {

        $model = new RouteMap();

        if (Yii::$app->request->isPost) {
            /* 表单验证 */
            $data = Yii::$app->request->post('RouteMap');
            $model->setAttributes($data);
            /* 保存用户数据到数据库 */
            if ($model->save()) {
                $this->success('操作成功', $this->getForward());
            } else {
                $this->error('操作错误');
            }
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    /**
     * ---------------------------------------
     * 编辑
     * ---------------------------------------
     */
    public function actionEdit($uid)
    {
        $model = RouteMap::findOne($uid);

        if (Yii::$app->request->isPost) {
            /* 表单验证 */
            $data = Yii::$app->request->post('RouteMap');

            $model->setAttributes($data);
            /* 保存用户数据到数据库 */
            if ($model->save()) {
                $this->success('操作成功', $this->getForward());
            } else {
                $this->error('操作错误');
            }
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    /**
     * ---------------------------------------
     * 删除
     * ---------------------------------------
     */
    public function actionDelete()
    {
        $ids = Yii::$app->request->param('id', 0);
        $ids = implode(',', array_unique((array)$ids));

        if (empty($ids)) {
            $this->error('请选择要操作的数据!');
        }

        $_where = 'id in(' . $ids . ')';
        if (RouteMap::deleteAll($_where)) {
            $this->success('删除成功', $this->getForward());
        } else {
            $this->error('删除失败！');
        }
    }


}
