<?php
namespace api\models;

use EasyWeChat\Foundation\Application;
use Yii;
use yii\base\Model;
use api\models\User;

/**
 * WxLogin form
 */
class WxLoginForm extends Model
{
    public $openid;
    public $wechat;
    private $_user;

    public $username;
    public $password;

    const GET_API_TOKEN = 'generate_api_token';

    public function getCode($targetUrl){
        $options = [
            'app_id' => Yii::$app->params['WECHAT']['APPID'],
            'secret' => Yii::$app->params['WECHAT']['APPSECRET'],
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/api/v1/user/oauth-callback?targetUrl='.$targetUrl,
            ],
        ];
        $this->wechat = new Application($options);
        $oauth = $this->wechat->oauth;
        return $oauth->redirect()->send();
    }

    public function getOpenid(){
        $options = [
            'app_id' => Yii::$app->params['WECHAT']['APPID'],
            'secret' => Yii::$app->params['WECHAT']['APPSECRET'],
        ];
        $this->wechat = new Application($options);
        $oauth = $this->wechat->oauth;
        $user = $oauth->user();
        return $user -> id;
    }

    public function init ()
    {
        parent::init();
        $this->on(self::GET_API_TOKEN, [$this, 'onGenerateApiToken']);
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
        ];
    }
    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function loginByOpenid($openid)
    {
        if ($this->getUserByOpenid($openid)) {
            $this->trigger(self::GET_API_TOKEN);
            return $this->_user;
        } else {
            return null;
        }
    }

    /**
     * 根据用户名获取用户的认证信息
     *
     * @return User|null
     */
    protected function getUserByOpenid($openid)
    {
        if ($this->_user === null) {
            $this->_user = User::findByOpenid($openid);
        }

        return $this->_user;
    }

    /**
     * 登录校验成功后，为用户生成新的token
     * 如果token失效，则重新生成token
     */
    public function onGenerateApiToken ()
    {
        if (!User::apiTokenIsValid($this->_user->api_token)) {
            $this->_user->generateApiToken();
            $this->_user->save(false);
        }
    }




}