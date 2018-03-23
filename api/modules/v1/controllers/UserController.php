<?php

namespace api\modules\v1\controllers;

use api\models\LoginForm;
use api\models\User;
use api\models\WxLoginForm;
use backend\models\Moto;
use backend\models\Route;
use backend\models\UserStopLog;
use backend\models\UserTip;
use common\aliyunDysms\api_demo\SmsDemo;
use common\helpers\Formatter;
use common\helpers\FuncHelper;
use common\helpers\Sms;
use common\helpers\Wechat;
use common\modelsgii\UserRecharge;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use EasyWeChat\Payment\Order;
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
                'tip',
                'stop-log',
                'bind-user-info',
                'stop-car',
                'create-menu',
                'notify',
                'pay',
                'pay-config',
                'pay-local-order',
                'end-stop-car',
                'wx-index',
                'send-vcode',
                'stop-tip-list',
                'get-all-stop',
                'moto-list',
                'edit-moto',
                'route-list',
                'route-dot',
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
        $wxUserInfo = $model -> getWxUserInfo();
        if($user = $model -> loginByOpenid($wxUserInfo)){
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

            if($user->stop_car_status == 2){
                $userStopLog = UserStopLog::find()->where(['uid' => $user->uid,'status' => 2]) ->one();
            }

            $userInfo =  [
                'id' => $user->uid,
                'plate_num' => $user->plate_num,
                'mobile' => $user->mobile,
                'is_vip' => $user->is_vip,
                'openid' => $user->openid,
                'reg_vip_time' => $user->reg_vip_time,
                'deadline' => max(intval((strtotime(date("Y-m-d",$user->reg_vip_time))+(86400 * 365) -time()) / 86400),1),
                'headimg' => $user->headimg,
                'username' => $user->username,
                'stop_car_status' => intval($user->stop_car_status),
                'lat' => isset($userStopLog->latitude) ? floatval($userStopLog->latitude) : 0,
                'lng' => isset($userStopLog->longitude) ? floatval($userStopLog->longitude) : 0,
                'address' => isset($userStopLog->remark) ? $userStopLog->remark : '',
                'is_tip' => isset($userStopLog->is_tip) ? intval($userStopLog->is_tip) : 1,
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
                        'remark' =>  $v['remark'],
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
     * 停车记录
     */
    public function actionStopLog ($token,$num=5,$page=1)
    {
        $user = User::findIdentityByAccessToken($token);
        if($user){
            $UserStopModel = UserStopLog::find()->where(['uid' => $user -> uid]);
            $StopInfo  = $UserStopModel ->limit($num)->offset(($page-1)*$num)->orderBy('create_time desc')->asArray()->all();
            $total = $UserStopModel -> count();
            $list = [];
            if($StopInfo){
                foreach ($StopInfo as $k => $v){
                    $list[] = [
                        'location' =>  $v['remark'],
                        'create_time' =>  intval($v['create_time']),
                        'car_num' =>  $user->plate_num,
                    ];
                }
            }
            unset($StopInfo);
            return ['code' => 200, 'msg' => 'ok' ,'data' =>  ['list' => $list,'total' => intval($total)]];
        }
        return ['code' => -2, 'msg' => '登录过期' ,'data' => null];
    }


    /**
     * 用户停车
     */
    public function actionStopCar ()
    {
        $token = Yii::$app->request->post('token');
        $lat = Yii::$app->request->post('lat');
        $lng = Yii::$app->request->post('lng');
        $location = Yii::$app->request->post('location');
        $user = User::findIdentityByAccessToken($token);
        if($user){
            $now = time();

            //判断当前用户是否是vip
            if(!$user -> is_vip){
                 return ['code' => 201, 'msg' => '您当前为普通用户' ,'data' => null];
            }

            // 判断是否存在还在停车中的记录
            $isStoping = UserStopLog::find()->where(['uid' => $user -> uid,'status' => 2])->one();
            if($isStoping){
                return ['code' => 203, 'msg' => '还存在停车未结束的记录' ,'data' => null];
            }

            // 记录停车记录
            $userStopCarLog = new UserStopLog();
            $userStopCarLog -> uid = $user->uid;
            $userStopCarLog -> latitude = $lat;
            $userStopCarLog -> longitude = $lng;
            $userStopCarLog -> remark = $location;
            $userStopCarLog -> create_time = $now;
            $userStopCarLog -> status = 2; //停车中
            $userStopCarLog -> save();

            $user -> stop_car_status = 2; //停车中
            $user -> update_time = time();

            $user -> save(false);

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

                        return ['code' => 200, 'msg' => '附近存在交警管制，当前位置停车不安全'];break;
                    }
                }
            }else{
                return ['code' => 202, 'msg' => '暂时安全' ,'data' => null];
            }

        }

        return ['code' => -2, 'msg' => '登录过期' ,'data' => null];
    }

    /**
     * 结束停车
     */
    public function actionEndStopCar ()
    {
        $token = Yii::$app->request->post('token');
        $user = User::findIdentityByAccessToken($token);
        if($user){

            // 结束正在停车中的状态
            $isStoping = UserStopLog::find()->where(['uid' => $user -> uid,'status' => 2])->one();
            if($isStoping){
                $isStoping -> status = 1; //停车结束
                $isStoping -> update_time = time();
                $isStoping -> save(false);

                // 改变用车的状态为停车结束
                $user -> stop_car_status = 1; //停车结束
                $user -> save(false);

                return ['code' => 200, 'msg' => 'ok','data' => null];
            }

            return ['code' => -1, 'msg' => '系统错误','data' => null];

        }
        return ['code' => -2, 'msg' => '登录过期' ,'data' => null];
    }

    /**
     * 提醒滚动列表
     */
    public function actionStopTipList ()
    {
        $str = range('A','Z');
        $routeArr = ['犀牛路','龙腾北路','龙岩大道','石锣鼓','龙川西路','登高西路','小溪路','黉门路','莲庄北路',
            '西郊路','罗龙西路','和平路','西安南路','人民路','曹溪中路','曹溪中路西','莲东北路','莲庄北路',
            '天平路','龙川北路','莲庄南路','乘风路'];
        $final_data = [
            ["name" =>"恭喜车主闽F".rand(1,9)."***".$str[rand(0,25)]."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".$str[rand(0,25)]."***".rand(1,9)."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".rand(1,9)."***".$str[rand(0,25)]."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽D".$str[rand(0,25)]."***".rand(1,9)."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".rand(1,9)."***".$str[rand(0,25)]."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".$str[rand(0,25)]."***".rand(1,9)."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".rand(1,9)."***".$str[rand(0,25)]."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽D".$str[rand(0,25)]."***".rand(1,9)."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".rand(1,9)."***".$str[rand(0,25)]."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".$str[rand(0,25)]."***".rand(1,9)."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".rand(1,9)."***".$str[rand(0,25)]."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".$str[rand(0,25)]."***".rand(1,9)."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽D".rand(1,9)."***".$str[rand(0,25)]."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".$str[rand(0,25)]."***".rand(1,9)."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".rand(1,9)."***".$str[rand(0,25)]."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽F".$str[rand(0,25)]."***".rand(1,9)."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽".rand(1,9)."***".$str[rand(0,25)]."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
            ["name" =>"恭喜车主闽D".$str[rand(0,25)]."***".rand(1,9)."在".$routeArr[rand(0,21)]."成功规避一张违停罚单"],
        ];
        return ['code' => 200, 'msg' => 'ok','data' => $final_data];

    }



    /**
     * 绑定用户信息(车牌和手机号)
     */
    public function actionBindUserInfo ()
    {
        $token = Yii::$app->request->post('token');
        $plate_num = Yii::$app->request->post('plate_num','');
        $mobile = Yii::$app->request->post('mobile','');
        $vcode = Yii::$app->request->post('vcode','');
        $user = User::findIdentityByAccessToken($token);
        if($user){
            if(!empty($plate_num)){
                $user -> plate_num = $plate_num;
            }
            if(!empty($mobile)){
                if(empty($vcode)){
                    return ['code' => -1, 'msg' => '手机验证码不能为空','data' => null];
                }

                if(Yii::$app->cache->get($mobile) != $vcode){
                    return ['code' => -1, 'msg' => '手机验证码错误','data' => null];
                }
                Yii::$app->cache->delete($mobile);

                $user -> mobile = $mobile;
            }
            $user -> update_time = time();
            $user -> save(false);

            return ['code' => 200, 'msg' => 'ok','data' => null];

        }
        return ['code' => -2, 'msg' => '登录过期' ,'data' => null];
    }

    /**
     * 微信支付成功回调页
     */
    public function actionNotify() {

        // TODO 尝试解耦这里的逻辑，只专注支付回调
        $payment = Wechat::construct_wx_payment();
        Yii::info("start to pay notify",__METHOD__);

        $response = $payment->handleNotify(function($notify, $successful){

            $db = Yii::$app->db;

            $date = date("Y-m-d H:i:s");
            Yii::info(sprintf("notify is (%s)",json_encode($notify->out_trade_no)),__METHOD__);
            try{

                // # 锁住这些记录
                $raw_sql = sprintf("select * from `yii2_user_recharge` where order_id = '%s' and status = %d for update;", $notify->out_trade_no, UserRecharge::STATUS_PAID_WECHAT); //未支付的订单
                $wx_order = $db->createCommand($raw_sql)->queryOne();
                if(!$wx_order) {
                    Yii::warning(sprintf("Fail to get the wx order with id (%d) at %s.\n", $notify->out_trade_no, $date),__METHOD__);
                    return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }

                // 用户是否支付成功

                if (!$successful) {

                    // 改变这个订单的状态为支付失败
                    $sql = sprintf("update yii2_user_recharge set status = %d where order_id = '%s';",UserRecharge::STATUS_PAID_WECHAT, $notify->out_trade_no);
                    $db->createCommand($sql)->execute();

                    Yii::error(sprintf("failed to pay for order id %d in data(%s)",$notify->out_trade_no,$date),__METHOD__);
                    return false;

                }else{

                    // 改变这个订单的状态为支付成功
                    $sql = sprintf("update yii2_user_recharge set status = %d where order_id = '%s';",UserRecharge::STATUS_PAID_SUCCESS, $notify->out_trade_no);
                    $db->createCommand($sql)->execute();

                    // 改变用户为vip会员，
                    $updateUserSql =  sprintf("update yii2_user set is_vip = %d,reg_vip_time = %d where uid = %d;",1, time(),$wx_order['uid']);
                    $db->createCommand($updateUserSql)->execute();


                    Yii::info(sprintf("success to pay for order id %d in data(%s)",$notify->out_trade_no,$date),__METHOD__);
                    return true;
                }



            } catch (\Exception $e) {
                // TODO 人工介入处理
                Yii::error($e->getMessage());
                return true;
            }

        });

        $response->send();
    }

    /**
     * 微信支付
     */
    public function actionPay(){
        $token = Yii::$app->request->get('token');
        $order_id = Yii::$app->request->get('order_id');
        $user = User::findIdentityByAccessToken($token);
        if($user){
            return $this->_pay_with_wechat($user,$order_id);
        }

    }

    /**
     * 微信支付配置
     */
    public function actionPayConfig(){
        $token = Yii::$app->request->get('token');
        $url = Yii::$app->request->get('url');
        $user = User::findIdentityByAccessToken($token);
        if($user){
            return $this->_pay_config($user,$url);
        }

    }

    /**
     * 支付配置
     */
    public function actionPayLocalOrder(){
        $token = Yii::$app->request->get('token');
        $user = User::findIdentityByAccessToken($token);
        if($user){
            return $this->_pay_local_order($user);
        }

    }
    /**
     * 创建本地订单
     */
    private function _pay_config($user,$url) {

        $wx_config = Wechat::construct_wx($url);

        return ["code"=>200, "msg"=>'ok', "data"=>$wx_config];

    }
    private function _pay_local_order($user) {
        $order_id = Wechat::genOrderId($user['openid']); // 本地的open id
        $wx_order = new UserRecharge();
        $wx_order->uid = $user['uid'];
        $wx_order->order_id = $order_id;
        $wx_order->wx_order_id = '';
        $wx_order->openid = $user['openid'];
        $wx_order->status = UserRecharge::STATUS_PAID_LOCAL; //订单本地状态
        $wx_order->create_time = time();
        $is_save = $wx_order->save();

        if($is_save){
            return ["code"=>200, "msg"=>'ok', "data"=>$order_id];
        }



    }
    private function _pay_with_wechat($user,$order_id) {

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $date = date("Y-m-d H:i:s");

        try {

            # 锁住用户记录
            $raw_sql = sprintf("select * from `yii2_user` where uid = %d for update;", $user['uid']);
            $raw_fans_user = $db->createCommand($raw_sql)->queryOne();
            if(!$raw_fans_user) {
                $transaction->rollback();
                Yii::warning(sprintf("Fail to get user with id(%d) at %s.\n",$user['uid'], $date));
                return ['code' => -1, 'msg' => '未知用户' ,'data' => null];
            }
            // 生成一个微信订单
            $wx_payment = Wechat::construct_wx_payment();

            $attributes = [
                'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
                'body'             => '停车服务年费',
                'detail'           => '停车服务年费',
                'out_trade_no'     => $order_id,
                'total_fee'        => 11800, // 单位：固定1元，使用分为单位
                'notify_url'       => Yii::$app->params['WX_PAY']['PAY_NOTIFY_URL'], // 支付结果通知网址，如果不设置则会使用配置里的默认地址
                'openid'           => $user['openid'], // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            ];
            $order = new Order($attributes);
            $result = $wx_payment->prepare($order);
            if ($result->return_code != 'SUCCESS' or $result->result_code != 'SUCCESS'){
                $err_msg = sprintf("Fail to get pre pay order id with response(%s)", json_encode($result));
                throw new Exception($err_msg);
            }

            $wx_order_id = $result->prepay_id;

            $wx_order = UserRecharge::find()->where(['order_id' => $order_id])->one();
            if($wx_order){

                $wx_order->wx_order_id = $wx_order_id;
                $wx_order->total_fee = 11800;
                $wx_order->wx_order_info_prepare = json_encode($result);
                $wx_order->status = UserRecharge::STATUS_PAID_WECHAT; //订单微信状态
                $wx_order->update_time = time();
                $is_saved = $wx_order->save();
                if(!$is_saved) {
                    throw new Exception('Fail to save the wx order.');
                }
            }
            $transaction->commit();

            $final_data = [
                "js_config" => $wx_payment->configForJSSDKPayment($result->prepay_id),
                "order_id" => $order_id
            ];

            return ["code"=>200, "msg"=>'ok', "data"=>$final_data];

        } catch(\Exception $e) {
            $transaction->rollBack();
            $err_msg = sprintf("Fail to check in cos reason:%s", $e->getMessage());
            Yii::error($err_msg,__METHOD__);
            return ["code"=>-1, "msg"=>'系统错误'];
        }
    }

    public function actionCheckOrder() {

        $token = Yii::$app->request->post('token');
        $order_id = Yii::$app->request->post('order_id');
        $user = User::findIdentityByAccessToken($token);
        if($user){
            $this->_check_order($user,$order_id);
        }

    }

    private function _check_order($user,$order_id){

        Yii::info($order_id,__METHOD__);
        $db = Yii::$app->db;
        if(!$order_id) {
            return ["code"=>-1, "msg"=>'order id 为空'];
        }

        $transaction =  $db->beginTransaction();
        try{

            $now = time();
            // 锁住这些记录
            $raw_sql = sprintf("select * from yii2_user_recharge where order_id = '%s' for update;", $order_id);
            $wx_order = $db->createCommand($raw_sql)->queryOne();
            if(!$wx_order) {
                $transaction->rollback();
                Yii::warning(sprintf("Fail to get the wx order  with id(%s) at %s.\n", $wx_order['id'], $now));
                return ["code"=>-1, "msg"=>"系统错误"];
            }

            // 支付成功 或者 支付失败
            if($wx_order['status'] == UserRecharge::STATUS_PAID_SUCCESSFULLY or $wx_order['status'] == UserRecharge::STATUS_PAID_FAILED) {

                $transaction->commit();

                // TODO 如何处理订单异常的情况

                $final_data = [
                    "order_info" => [
                        "status" => $wx_order['status']
                    ]
                ];

                return json_encode(["code"=>200, "msg"=>'ok', "data"=>$final_data]);
            }

            return ["code"=>-1, "msg"=>'系统错误'];

        } catch(\Exception $e) {

            $transaction->rollBack();

            $err_msg = sprintf("Fail to check order status cos reason:%s", $e);
            Yii::error($err_msg);

            return ["code"=>-1, "msg"=>'系统错误'];
        }
    }

    /**
     * 微信服务器访问接口
     */
    public function actionWxIndex(){

        $echoStr = Yii::$app->request->get("echostr",null);

        if ($this->_checkSignature()) {

            if(!is_null($echoStr)){
                echo $echoStr;exit;
            }
            $this->_requestMsg();
            exit;
        }
    }


    private function _checkSignature() {
        $token = Yii::$app->params['WECHAT']['TOKEN'];
        $signature = Yii::$app->request->get("signature");
        $timestamp = Yii::$app->request->get("timestamp");
        $nonce = Yii::$app->request->get("nonce");
        $tmpArr = array($token, $timestamp, $nonce);//C('token')
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            echo $token;
            return false;
        }
    }

    public function _requestMsg()
    {

        $wechat = Wechat::wxInit();
        $wechat->server->setMessageHandler(function ($message) {

            Yii::info(sprintf("MsgType(%s).Event(%s).user(%s).Key(%s).Msg(%s)",$message->MsgType,$message->Event,$message->FromUserName,$message->EventKey,$message->Content),__METHOD__);
            switch ($message->MsgType) {
                case 'event':
                    if ($message->Event == "subscribe") {

                        $news = new News([
                            'title'       => "停车无忧——关注您出行路上的每一天",
                            'description' => "哪个地方有点堵？\n哪个地方在修路？ \n哪个地方有临时交通管制？\n停车无忧——关注您出行路上的每一天，让您无忧出行！",
                            'url'         => 'https://www.xltcwy.cn/routeList',
                            'image'       => 'http://api.xltcwy.cn/storage/image/201803/1521788425321.png',
                        ]);

//                        $content =  "哪个地方有点堵？\n哪个地方在修路？ \n哪个地方有临时交通管制？\n停车无忧——关注您出行路上的每一天，让您无忧出行！";
                        return $news;
                    } elseif ($message->Event == "unsubscribe") {

                    } elseif ($message->Event == "CLICK") {

                    } elseif ($message->Event == "VIEW") {

                    }
                    break;
                case 'text':

                    $news = new News([
                        'title'       => "停车无忧——关注您出行路上的每一天",
                        'description' => "哪个地方有点堵？\n哪个地方在修路？ \n哪个地方有临时交通管制？\n停车无忧——关注您出行路上的每一天，让您无忧出行！",
                        'url'         => 'https://www.xltcwy.cn/routeList',
                        'image'       => 'http://api.xltcwy.cn/storage/image/201803/1521788425321.png',
                    ]);

//                        $content =  "哪个地方有点堵？\n哪个地方在修路？ \n哪个地方有临时交通管制？\n停车无忧——关注您出行路上的每一天，让您无忧出行！";
                    return $news;
                    break;
                case 'image':
                    break;
                case 'voice':
                    # 语音消息...
                    break;
                case 'video':
                    # 视频消息...
                    break;
                case 'location':
                    # 坐标消息...
                    break;
                case 'link':
                    # 链接消息...
                    break;
                // ... 其它消息
                default:
                    break;
            }
            // ...
        });

        $wechat->server->serve()->send();
    }

    /**
     * 创建菜单
     */
    public function actionCreateMenu(){
        $data = [
            [
                "type" =>"view",
                "name"=>"使用说明",
                "url"=>"https://www.xltcwy.cn/explain"
            ],
            [
                "type" =>"view",
                "name"=>"摩托管制",
                "url"=>"https://www.xltcwy.cn/routeList"
            ],
            [
                "type" =>"view",
                "name"=>"小车管制",
                "url"=>"https://www.xltcwy.cn/routeList?index=1"
            ]
        ];
        $wechat = Wechat::wxInit();
        $wechat -> menu -> add($data);
    }

    /**
     * 发送手机验证码
     */
    public function actionSendVcode(){
        $phone = Yii::$app->request->post('mobile');
        if(empty($phone)){
            return ["code"=>-1, "msg"=>'请填写手机号'];
        }
        if(!Formatter::isMobile($phone)){
            return ["code" => -1, "msg" => "手机格式错误"];
        }
        if(Yii::$app->cache->get($phone)){
            return ["code" => -1, "msg" => "系统错误"];
        }
        SmsDemo::sendVerifyCode($phone);

        return ["code"=>200, "msg"=>'ok'];

    }


    /**
     * 发送手机验证码
     */
    public function actionGetAllStop(){
        $userStopLog = UserStopLog::find()->select(['latitude','longitude'])->asArray()->all();
        return ["code"=>200,"msg" => 'ok',"data" =>$userStopLog ];

    }

    /**
     * 获取摩托车管制点
     */
    public function actionMotoList(){
        $userStopLog = Moto::find()->select(['latitude','longitude','start_time','end_time'])->asArray()->all();
        foreach($userStopLog as $k=> $v){
            $userStopLog[$k]['datetime'] = date("m月d",$v['start_time'])." ".date("H:i",$v['start_time'])."-".date("H:i",$v['end_time']);
        }
        return ["code"=>200,"msg" => 'ok',"data" =>$userStopLog ];
    }

    /**
     * ---------------------------------------
     * 添加/编辑摩托车管制位置
     * ---------------------------------------
     */
    public function actionEditMoto(){

        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post();
            if(isset($data['id'])){ //编辑
                $motoModel = Moto::find()->where(['id' =>$data['id']])->one();
                if($motoModel){
                    $motoModel -> start_time = strtotime($data['start_time']);
                    $motoModel -> end_time = strtotime($data['end_time']);
                    $motoModel -> latitude = $data['latitude'];
                    $motoModel -> longitude = $data['longitude'];
                    $motoModel -> remark = $data['remark'];
                    $motoModel -> update_time = time();
                    if( $motoModel -> save(false)){
                        return ['code' => 200,'msg' => 'ok'];
                    }
                }

            }else{//添加
                $motoModel = new Moto();
                $motoModel -> start_time = strtotime($data['start_time']);
                $motoModel -> end_time = strtotime($data['end_time']);
                $motoModel -> latitude = $data['latitude'];
                $motoModel -> longitude = $data['longitude'];
                $motoModel -> remark = $data['remark'];
                $motoModel -> create_time = time();

                if( $motoModel -> save(false)){
                    return ['code' => 200,'msg' => 'ok'];
                }

            }
            return ['code' => -1,'msg' => '系统错误'];
        }
    }


    /**
     * 获取摩托车/小车管制列表
     * @method get
     * @param string $token
     * @param int $type
     * @param int $page
     * @param int $num
     * @return array
     */
    public function actionRouteList(){

        $token = Yii::$app->request->get('token');
        $user = User::findIdentityByAccessToken($token);
        if($user){

            $page = Yii::$app->request->get('page',1);
            $num = Yii::$app->request->get('num',20);

            $motoList = Route::find()
                ->select(['route_date','type'])
                ->limit($num)
                ->offset(($page-1)*$num)
                ->orderBy('route_date desc')
                ->groupBy('route_date,type')
                ->asArray()
                ->all();
            $today = strtotime(date("Y-m-d"));
            foreach($motoList as $k=> $v){
                if($v['route_date'] == $today){
                    $motoList[$k]['is_news'] = 1;
                }else{
                    $motoList[$k]['is_news'] = 0;
                }
            }
            return ["code"=>200,"msg" => 'ok',"data" =>$motoList ];
        }
    }



    /**
     * 获取摩托车/小车管制点
     * @method get
     * @param string $token
     * @param int $id
     * @param int $type
     * @return array
     */
    public function actionRouteDot(){

        $token = Yii::$app->request->get('token');
        $user = User::findIdentityByAccessToken($token);
        if($user){

            $route_date = Yii::$app->request->get('route_date',strtotime(date("Y-m-d")));
            $type = Yii::$app->request->get('type');

            $motoDotList = Route::find()
                ->where(['route_date' => $route_date,'type' => $type])
                ->asArray()
                ->all();
            return ["code"=>200,"msg" => 'ok',"data" =>$motoDotList ];
        }
    }



}
