<?php
/**
 * 登录状态过滤
 */
namespace api\filters;

use app\events\SessionEvent;
use app\helpers\client\AppClient;
use yii\base\ActionFilter;
use Yii;
use yii\base\Event;

defined('YII_REQUEST_START_TIME') or define('YII_REQUEST_START_TIME','');

class Session extends ActionFilter
{
    /**
     * Session通过验证事件
     */
    const EVENT_ACCEPT_SESSION = 'acceptSession';

    public function beforeAction($action)
    {
        return $this->filterSession();
    }

    /**
     * 用以检查用户session时候有效
     */
    public function filterSession()
    {
        Yii::beginProfile('FILTER-SESSION-'.YII_REQUEST_START_TIME);
        $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : null;
        $sid = isset($_REQUEST['sid']) ? $_REQUEST['sid'] : null;

        //验证内部触发Session事件
        $userInfo = AppClient::checkUidSid($uid, $sid);

        Yii::endProfile('FILTER-SESSION-'.YII_REQUEST_START_TIME);
        return true;
    }
}