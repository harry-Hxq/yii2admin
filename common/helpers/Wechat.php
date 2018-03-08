<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/3/4
 * Time: 15:11
 */
namespace common\helpers;
use EasyWeChat\Foundation\Application;
use Yii;
class Wechat
{


    public static function construct_wx_payment()
    {

        $options = [

            "app_id" => Yii::$app->params['WX_PAY']['WX_PAY_APP_ID'],
            "secret" => Yii::$app->params['WX_PAY']['WX_PAY_APP_SECRET'],

            // payment
            'payment' => [
                'merchant_id' => Yii::$app->params['WX_PAY']['WX_PAY_MERCHANT_ID'],
                'key' => Yii::$app->params['WX_PAY']['WX_PAY_KEY_FOR_SIGN'],
                'cert_path' => Yii::$app->params['WX_PAY']['WX_CERT_PATH'],
                'key_path' => Yii::$app->params['WX_PAY']['WX_KEY_PATH'],
                'notify_url' => Yii::$app->request->hostInfo ."/api/v1/user/". Yii::$app->params['WX_PAY']['PAY_NOTIFY_URL']
            ],
        ];

        $app = new Application($options);

        $payment = $app->payment;

        return $payment;
    }



    public static function construct_wx($url)
    {

        $options = [

            "app_id" => Yii::$app->params['WX_PAY']['WX_PAY_APP_ID'],
            "secret" => Yii::$app->params['WX_PAY']['WX_PAY_APP_SECRET'],
        ];

        $app = new Application($options);

        $js_config = $app->js->setUrl($url)->config([''],true,false,false);

        return $js_config;
    }


    public static function construct_enterprise_payment()
    {

        $options = [

            "app_id" => Yii::$app->params['WX_PAY']['WX_PAY_APP_ID'],
            "secret" => Yii::$app->params['WX_PAY']['WX_PAY_APP_SECRET'],

            // payment
            'payment' => [
                'merchant_id' => Yii::$app->params['WX_PAY']['WX_PAY_MERCHANT_ID'],
                'key' => Yii::$app->params['WX_PAY']['WX_PAY_KEY_FOR_SIGN'],
                'cert_path' => Yii::$app->params['WX_PAY']['WX_CERT_PATH'],
                'key_path' => Yii::$app->params['WX_PAY']['WX_KEY_PATH'],
                'notify_url' => Yii::$app->request->hostInfo . Yii::$app->params['WX_PAY']['PAY_NOTIFY_URL']
            ],
        ];

        $app = new Application($options);

        $payment = $app->merchant_pay;

        return $payment;
    }

    public static function genOrderId($ran) {

        // TODO 分布式的时候需要调整这里的算法，解决冲突

        list($usec, $sec) = explode(" ", microtime());

        $t = substr(md5(explode(".", $usec)[1] . $sec), 0, 10) . substr(md5($ran), 0, 6);

        return $t;
    }

}
