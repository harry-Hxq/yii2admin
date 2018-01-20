<?php
/**
 * 签名过滤
 */
namespace api\filters;
use app\config\Error;
use app\helpers\client\AppClient;
use app\helpers\EncryptTool;
use yii\base\ActionFilter;
use app\models\ar\BbShop;
use Yii;
use yii\helpers\VarDumper;

defined('YII_REQUEST_START_TIME') or define('YII_REQUEST_START_TIME','');

class Signature extends ActionFilter
{
    public function beforeAction($action)
    {
        return $this->filterSign();
    }
    /**
     * 用以做签名检查
     */
    public function filterSign(){
        Yii::beginProfile('FILTER-SIGN-'.YII_REQUEST_START_TIME);
        $host = Yii::$app->request->getHostInfo();
        $method = Yii::$app->request->getIsPost()?'post':'get';
        $uri = Yii::$app->request->getPathInfo();
        if($method == 'post'){
            $params = Yii::$app->request->post();
        }else{
            $params = Yii::$app->request->get();
        }
        if(!isset($params['appid'])){
             Yii::$app->response->error(Error::COMMON_SIGN, '没有传递参数：appid');
        }
        $dataShop['app_id'] = $params['appid'];
        // $dataShop['app_secret'] = $params['appsecret'];dwZgNb7W9NCx8hHA3yBxxLEzpUk=
        
        if(!isset($params['token'])){
             Yii::$app->response->error(Error::COMMON_SIGN, '没有传递参数：token');
        }
        $dataShop['token'] = $params['token'];

        $instanceShop = new BbShop();
        $dataValidation = $instanceShop->getCurrentShopInfo($dataShop);
        if(!$dataValidation){
            Yii::$app->response->error(Error::COMMON_SIGN, '签名错误11');
        }
        $secretKey = $dataValidation->app_secret;

//        Yii::info("accept request:METHOD[$method],URI[$uri],params is \r\n ".VarDumper::dumpAsString($params)."]", 'application.service.request');
        if(!isset($params['signature'])) {
            Yii::error("sign check error,no sign data found for uri[$uri]", 'application.service');
            Yii::endProfile('FILTER-SIGN-'.YII_REQUEST_START_TIME);
            Yii::$app->response->error(Error::COMMON_SIGN,'缺少数据签名');
        }
        // $secretKey = AppClient::getInstance()->getSecretKey();
        // $secretKey = Yii::$app->params['shopClient'];

        if(is_null($secretKey)) {
            Yii::error("client type not allowed,client info:".VarDumper::dumpAsString(AppClient::getInstance()->info()), 'application.service');
            Yii::endProfile('FILTER-SIGN-'.YII_REQUEST_START_TIME);
            Yii::$app->response->error(Error::COMMON_ILLEGAL_CLIENT,'非法客户端类型');
        }
        $sign = $params['signature'];
        unset($params['signature']);
        unset($params['_']);
        ksort($params);
        $signString = $host.'/'.$uri.'&'.$method;
        // http://apicenter-service.liechengcf.me/common/user/getcetification?appid=cuSpEr9Nn4jFkdRFJ4s09YqaEipyw3RU&token=tKt1Pbchv0M8TiAI&MerchantID=&MemberID=&tokenId=&signature=2c5ab495f6de7347c92b8e7949369655&b_nonce=1498039818&_=1498039818429
        // Yii::$app->response->error(Error::COMMON_SIGN,var_dump($params));exit;
        foreach($params as $key => $value) {
                $signString .= '&'.$key.'='.$value;
        }
        $signString = strtolower($signString);
//        Yii::info($signString);
        // Yii::$app->response->error(Error::COMMON_SIGN,var_dump($signString));exit;
        if(self::signIt($signString, $secretKey) != $sign) {
            Yii::error("sign check error,signString:{$signString},secretKey:{$secretKey}", 'application.service');
            Yii::endProfile('FILTER-SIGN-'.YII_REQUEST_START_TIME);
            Yii::$app->response->error(Error::COMMON_SIGN, '签名错误');
        }
        Yii::endProfile('FILTER-SIGN-'.YII_REQUEST_START_TIME);
        return true;
    }


    /**
     * 生成签名
     * @param $data
     * @param $secret
     */
    public static function signIt($data, $secret)
    {
        return $mineSign = md5(md5(strtolower($data).'&'.$secret).'&'.$secret);
    }

}