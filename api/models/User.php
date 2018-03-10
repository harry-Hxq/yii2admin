<?php

namespace api\models;

use function EasyWeChat\Payment\get_client_ip;
use Yii;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;
use common\helpers\StringHelper;

/**
 * 实现User组件中的身份识别类 参见 yii\web\user
 * This is the model class for table "{{%user}}".
 *
 */
class User extends \common\modelsgii\User implements IdentityInterface,RateLimitInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE  = 1;

    /**
     * --------------------------------------------------------------
     * 实现IdentityInterface
     * 资料参考：http://www.yiichina.com/doc/guide/2.0/rest-authentication
     * --------------------------------------------------------------
     */

    /**
     * 根据UID获取账号信息
     */
    public static function findIdentity($uid)
    {
        return static::findOne(['uid' => $uid, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * ---------------------------------------
     * 由token获取用户信息
     * @param mixed $token
     * @param null $type
     * @return null|static
     * ---------------------------------------
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /* 这里为了简便将数据库token字段设置成username */
        /* 使用 /api/v1/user?access-token=e282486518 即可访问 */

        // 如果token无效的话，
        if(!static::apiTokenIsValid($token)) {
            throw new \yii\web\UnauthorizedHttpException("token is invalid.");
        }

        return static::findOne(['api_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * ---------------------------------------
     * 获取主键
     * @return mixed
     * ---------------------------------------
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * ---------------------------------------
     * 获取密码干扰字符串
     * @return string
     * ---------------------------------------
     */
    public function getAuthKey()
    {
        return $this->salt;
    }

    /**
     * ---------------------------------------
     * 验证
     * @param string $authKey
     * @return bool
     * ---------------------------------------
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * --------------------------------------------------------------
     * 实现RateLimitInterface
     * 以下是接口调用速率限制信息，记得在数据库中添加两个字段allowance,allowance_updated_at
     * 这两个字段也可以存储到缓存中，这里方便就存储到数据库中了
     * 资料参考：http://www.yiichina.com/doc/guide/2.0/rest-rate-limiting
     * --------------------------------------------------------------
     */

    /**
     * ---------------------------------------
     * 返回某一时间允许请求的最大数量，比如设置10秒内最多5次请求（小数量方便我们模拟测试）
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @return array
     * ---------------------------------------
     */
    public  function getRateLimit($request, $action){
        return [5, 10];
    }

    /**
     * ---------------------------------------
     * 回剩余的允许的请求和相应的UNIX时间戳数 当最后一次速率限制检查时
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @return array
     * ---------------------------------------
     */
    public  function loadAllowance($request, $action){
        return [$this->allowance, $this->allowance_updated_at];
    }

    /**
     * ---------------------------------------
     * 保存允许剩余的请求数和当前的UNIX时间戳
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @param int $allowance
     * @param int $timestamp
     * ---------------------------------------
     */
    public  function saveAllowance($request, $action, $allowance, $timestamp){
        $this->allowance = $allowance;
        $this->allowance_updated_at = $timestamp;
        $this->save();
    }

    /**
     * ---------------------------------------
     * 生成 api_token
     * ---------------------------------------
     */
    public function generateApiToken()
    {
        $this->api_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * ---------------------------------------
     * 校验api_token是否有效
     * ---------------------------------------
     */
    public static function apiTokenIsValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.apiTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * 获取用户信息
     */
    public static function findByUsername ($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * 获取用户信息(byopenid)
     */
    public static function findByOpenid ($wxUserInfo)
    {
        $user = static::findOne(['openid' => $wxUserInfo ->id, 'status' => self::STATUS_ACTIVE]);
        if($user){
            return $user;
        }else{

            $userModel = new User();

            $userModel -> username = StringHelper::replace_emoji($wxUserInfo -> nickname);
            $userModel -> password = '$2y$13$oO.xRlrKjMMF/bykb7476.zBIH2RkR6rtv8j5jrYgSxi71AvV3lFG';
            $userModel -> salt     = 'kXGkWeNSeoK7vakqRfUAviocq-5uy0cN';
            $userModel -> email    = 'phphome@qq.com';
            $userModel -> reg_time = time();
            $userModel -> reg_ip   = get_client_ip();
            $userModel -> last_login_time = time();
            $userModel -> last_login_ip = get_client_ip();
            $userModel -> status   = 1;
            $userModel -> openid   = $wxUserInfo -> id;
            $userModel -> is_vip   = 0;
            $userModel -> headimg   = $wxUserInfo -> avatar;

            $userModel -> allowance   = 0;
            $userModel -> allowance_updated_at   = 0;

            if($userModel -> save(false)){
                return static::findOne(['openid' => $wxUserInfo -> id, 'status' => self::STATUS_ACTIVE]);
            }

        }
    }

    /**
     * 验证密码
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * 退出
     */
    public static function logoutByToken($token)
    {
        $user =  static::findOne(['api_token' => $token, 'status' => self::STATUS_ACTIVE]);
        if($user){
            $user -> api_token = '';
            $user -> save();
            return $user;
        }
    }





}
