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
     *
     * ---------------------------------------
     */
    public function actionClear()
    {
        Picture::clearPic(Yii::$app->params['upload']['path']);
        return static::EXIT_CODE_NORMAL;
    }

}
