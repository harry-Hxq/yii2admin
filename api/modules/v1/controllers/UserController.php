<?php

namespace api\modules\v1\controllers;

use api\models\LoginForm;
use api\models\User;
use api\models\WxLoginForm;
use backend\models\Route;
use backend\models\UserStopLog;
use backend\models\UserTip;
use common\helpers\FuncHelper;
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
                'wx-login',
                'oauth-callback',
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
     * 微信授权登录（获取code）
     */
    public function actionWxLogin(){
        $targetUrl = Yii::$app->request->get('targetUrl',Yii::$app->params['DEFAULT_TARGET_URL']);
        $model = new WxLoginForm();
        $model -> getCode($targetUrl);

    }

    /**
     * 微信授权登录（回调地址）
     */
    public function actionOauthCallback(){
        $targetUrl = Yii::$app->request->get('targetUrl',Yii::$app->params['DEFAULT_TARGET_URL']);
        $model = new WxLoginForm();
        $openid = $model -> getopenid();
        if($user = $model -> loginByOpenid($openid)){
            if ($user instanceof IdentityInterface) {
                header("location:".$targetUrl.'?api_token='.$user->api_token);
            }
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
                'plate_num' => $user->plate_num,
                'mobile' => $user->mobile,
                'free_times' => $user->free_times,
                'is_vip' => $user->is_vip,
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


    /**
     * 获取提醒记录
     */
    public function actionStopLog ()
    {
        $token = Yii::$app->request->post('api-token');
        $page = Yii::$app->request->post('page');
        $num = Yii::$app->request->post('num');
        $user = User::findIdentityByAccessToken($token);
        if($user){
            $UserStopModel = UserStopLog::find()->where(['uid' => $user -> uid]);
            $StopInfo  = $UserStopModel ->limit($num)->offset(($page-1)*$num)->orderBy('create_time desc')->asArray()->all();
            $total = $UserStopModel -> count();
            $list = [];
            if($StopInfo){
                foreach ($StopInfo as $k => $v){
                    $list[] = [
                        'remark' =>  $v['remark'],
                        'create_time' =>  intval($v['create_time']),
                    ];
                }
            }
            unset($StopInfo);
            return ['code' => 200, 'msg' => 'ok' ,'data' =>  ['list' => $list,'total' => intval($total)]];
        }
        return ['code' => -2, 'msg' => '登录过期' ,'data' => null];
    }


    /**
     * 用户停车(一开始有免费停车次数，当停车次数用完，需要充值成为会员才可以继续使用)
     */
    public function actionStopCar ()
    {
        $token = Yii::$app->request->post('api-token');
        $lat = Yii::$app->request->post('lat');
        $lng = Yii::$app->request->post('lng');
        $location = Yii::$app->request->post('location');
        $user = User::findIdentityByAccessToken($token);
        if($user){
            $now = time();

            //判断当前用户是否是vip
            if(!$user -> is_vip){
                // 判断是否存在免费次数
                if($user -> free_times <=0 ){
                    return ['code' => -1, 'msg' => '你的体验次数已经用完，请升级为vip用户即可免费继续体验' ,'data' => null];
                }

                //免费次数减一
                $user -> free_times --;
                $user ->save(false);
            }

            // 记录停车记录
            $userStopCarLog = new UserStopLog();
            $userStopCarLog -> uid = $user->uid;
            $userStopCarLog -> latitude = $lat;
            $userStopCarLog -> longitude = $lng;
            $userStopCarLog -> remark = $location;
            $userStopCarLog -> create_time = $now;
            $userStopCarLog -> save();

            //判断附近是否存
            $res = Route::find()->where(['>','start_time',$now])->andWhere(['>','end_time',$now])->asArray()->all();
            if($res){
                foreach ($res as $k => $v){
                    // 计算距离
                    $distance = FuncHelper::distanceBetween($lat,$lng,$v['latitude'],$v['longitude']);
                    if($distance < Yii::$app->params['DISTANCE']){
                        //提醒用户 - 消费服务
                        $userTip = new UserTip();
                        $userTip -> uid = $user->uid;
                        $userTip -> route_id = $v['id'];
                        $userTip -> remark = $v['remark'];
                        $userTip -> create_time = $now;
                        $userTip -> save();

                        return ['code' => 200, 'msg' => '距离近'];break;
                    }
                }
            }else{
                return ['code' => -1, 'msg' => '未查到' ,'data' => null];
            }

        }
        return ['code' => -2, 'msg' => '登录过期' ,'data' => null];
    }



    /**
     * 绑定用户信息(车牌和手机号)
     */
    public function actionBindUserInfo ()
    {
        $token = Yii::$app->request->post('api-token');
        $plate_num = Yii::$app->request->post('plate_num','');
        $mobile = Yii::$app->request->post('mobile');
        $user = User::findIdentityByAccessToken($token);
        if($user){

            $user -> plate_num = $plate_num;
            $user -> mobile = $mobile;
            $user -> update_time = time();
            $user -> save(false);

            return ['code' => 200, 'msg' => 'ok','data' => null];

        }
        return ['code' => -2, 'msg' => '登录过期' ,'data' => null];
    }






}
