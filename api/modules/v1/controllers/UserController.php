<?php

namespace api\modules\v1\controllers;

use api\models\LoginForm;
use api\models\User;
use common\modelsgii\UserTip;
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
                'user-profile',
                'logout',
                'tip'
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
                return ['code' => 200, 'msg' => 'ok' ,'data' => $user->api_token];
            } else {
                $errors = ($model->errors);
                return ['code' => -1, 'msg' => current(current($errors)) ,'data' => null];
            }
        } else {
            $errors = ($model->errors);
            return ['code' => -1, 'msg' => current(current($errors)) ,'data' => null];
        }
    }

    /**
     * 获取用户信息
     */
    public function actionUserProfile ($token)
    {
        $user = User::findIdentityByAccessToken($token);
        if($user){
            $userInfo =  [
                'id' => $user->uid,
                'username' => $user->username,
                'email' => $user->email,
            ];
            return ['code' => 200, 'msg' => 'ok' ,'data' =>  $userInfo];
        }
        return ['code' => -2, 'msg' => '登录过期' ,'data' => null];

    }

    /**
     * 退出
     */
    public function actionLogout ($token)
    {
        $user = User::logoutByToken($token);
        if($user){
            return ['code' => 200, 'msg' => 'ok' ,'data' => true];
        }
        return ['code' => -2, 'msg' => '登录过期' ,'data' => null];
    }

    /**
     * 获取提醒记录
     */
    public function actionTip ($token,$num=5,$page=1)
    {
        $user = User::findIdentityByAccessToken($token);
        if($user){
            $tipModel = UserTip::find()->where(['uid' => $user -> uid]);
            $tipInfo  = $tipModel ->limit($num)->offset(($page-1)*$num)->orderBy('create_time desc')->asArray()->all();
            $total = $tipModel -> count();
            $list = [];
            if($tipInfo){
                foreach ($tipInfo as $k => $v){
                    $list[] = [
                        'route_id' =>  $v['route_id'],
                        'create_time' =>  intval($v['create_time']),
                    ];
                }
            }
            unset($tipInfo);
            return ['code' => 200, 'msg' => 'ok' ,'data' =>  ['list' => $list,'total' => intval($total)]];
        }
        return ['code' => -2, 'msg' => '登录过期' ,'data' => null];
    }

}
