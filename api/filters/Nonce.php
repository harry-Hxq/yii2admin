<?php
/**
 * 防重放攻击过滤
 */
namespace api\filters;

use app\config\Error;
use yii\base\ActionFilter;
use Yii;

class Nonce extends ActionFilter
{
    public function beforeAction($action)
    {
        return $this->filterNonce();
    }

    public function filterNonce()
    {
        if(Yii::$app->request->getIsPost()) {
            $nonce = Yii::$app->request->post('b_nonce',null);
            if($nonce == null) {
                Yii::$app->response->error(Error::COMMON_NONCE_ERROR,'请求已过期，请重试!');
            }
            $timestamp = Yii::$app->request->post('timestamp',null);
            if($timestamp == null) {
                Yii::$app->response->error(Error::COMMON_NONCE_ERROR,'请求已过期，请重试!');
            }
            if(Yii::$app->cache->get($nonce)){
                Yii::$app->response->error(Error::COMMON_NONCE_ERROR,'请求已过期，请重试!');
            }
            Yii::$app->cache->set($nonce,1,Yii::$app->params['nonceTime']);

            if((time()-$timestamp) > Yii::$app->params['nonceTime']) {
                Yii::$app->response->error(Error::COMMON_NONCE_ERROR,'请求已过期，请重试!');
            }
        }
        return true;
    }
}