<?php

namespace api\modules\v1\controllers;

use api\models\LoginForm;
use api\models\User;
use yii\base\Exception;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use Yii;
use yii\web\IdentityInterface;

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
class UserController extends ActiveController
{
    public $modelClass = 'api\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        /* 设置认证方式 */
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'optional' => [
                'login',
                'user-profile'
            ]
        ];
        return $behaviors;
    }

    /**
     * 登录
     */
    public function actionLogin(){
        $model = new LoginForm();
        $model->setAttributes(Yii::$app->request->post());
        if ($user = $model->login()) {
            if ($user instanceof IdentityInterface) {
                return $user->api_token;
            } else {
                return $user->errors;
            }
        } else {
            return $model->errors;
        }
    }

    /**
     * 获取用户信息
     */
    public function actionUserProfile ($token)
    {
        $user = User::findIdentityByAccessToken($token);
        return [
            'id' => $user->uid,
            'username' => $user->username,
            'email' => $user->email,
        ];
    }

}
