<?php

namespace backend\controllers;

use backend\models\Admin;
use backend\models\Pc\Room;
use backend\models\Pc\Search\RoomSearch;
use backend\models\User;
use kartik\form\ActiveForm;
use Yii;
use yii\web\Response;
use yii\db\Exception;

/**
 * 路线控制器
 * @author longfei <phphome@qq.com>
 */
class RoomController extends BaseController
{
    /**
     * ---------------------------------------
     * 构造方法
     * ---------------------------------------
     */
    public function init()
    {
        parent::init();
    }

    /**
     * ---------------------------------------
     * 控制台
     * ---------------------------------------
     */
    public function actionManager()
    {
        return $this->render('manager');
    }

    /**
     * ---------------------------------------
     * 列表
     * ---------------------------------------
     */
    public function actionIndex()
    {
        /* 添加当前位置到cookie供后续操作调用 */
        $this->setForward();

        $searchModel = new RoomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * ---------------------------------------
     * 添加
     * ---------------------------------------
     */
    public function actionAdd()
    {

        $model = new Room();
        $tr = Yii::$app->db->beginTransaction();
        if (Yii::$app->request->isPost) {
            /* 表单验证 */
            $data = Yii::$app->request->post('Room');
            $data['agent'] = 'Xsoul';
            $data['version'] = '尊享版';
            $data['roompassshow'] = $data['roompass'];
            $data["roomname"] = "未来科技娱乐房间";
            $data['roompass'] = md5($data['roompass']);

            $data['roomtime'] = $this->_getRoomtime($data['roomtime']);
            $data['create_time'] = time();

            if(Yii::$app->user->getId() == 11){
                $data['admin_id'] = 11;
            }


            $model->setAttributes($data);
            /* 保存用户数据到数据库 */
            if ($model->save()) {
                $roomid = $model->id;
                if($roomid == 0){
                    Yii::error("$roomid");
                    $tr->rollBack();
                    $this->error('操作错误1');exit;
                }
                $res = $this->_setRoomDefault($roomid);
                if($res){
                    $tr->commit();
                    $this->success('操作成功', $this->getForward());
                }
                $tr->rollBack();
                $this->error('操作错误2');
            } else {
                $tr->rollBack();
                $this->error('操作错误3');
            }
        }

        if(Yii::$app->user->getId() == 11){
            return $this->render('add2', [
                'model' => $model,
            ]);
        }
        return $this->render('add', [
            'model' => $model,
        ]);
    }

    public function actionRoomValidate(){
        $model = new Room();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function _existRoomadmin($roomadmin){
        return Room::find()->where(['roomadmin' => $roomadmin])->one();
    }


    /**
     * ---------------------------------------
     * 编辑
     * ---------------------------------------
     */
    public function actionEdit($uid)
    {
        $model = Room::findOne($uid);

        if (Yii::$app->request->isPost) {
            /* 表单验证 */
            $data = Yii::$app->request->post('Room');

            if ($data['roompass'] == $model->roompass) {
                // 没有修改密码
            } else {
                $data['roompassshow'] = $data['roompass'];
                $data['roompass'] = md5($data['roompass']);
            }
            $data['update_time'] = time();

            $model->setAttributes($data);
            /* 保存用户数据到数据库 */
            if ($model->save()) {
                $this->success('操作成功', $this->getForward());
            } else {
                $this->error('操作错误');
            }
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }


    /**
     * ---------------------------------------
     * 编辑
     * ---------------------------------------
     */
    public function actionUserEdit()
    {
        $model = Admin::findOne(Yii::$app->user->getId());

        if (Yii::$app->request->isPost) {
            /* 表单验证 */
            $data = Yii::$app->request->post('Admin');
            $data['update_time'] = time();
            /* 如果设置密码则重置密码，否则不修改密码 */
            if (!empty($data['password'])) {
                $model->generateAuthKey();
                $model->setPassword($data['password']);
            }
            unset($data['password']);

            $model->setAttributes($data);
            /* 保存用户数据到数据库 */
            if ($model->save()) {
                $this->success('操作成功', $this->getForward());
            } else {
                $this->error('操作错误');
            }
        }

        return $this->render('useredit', [
            'model' => $model,
        ]);
    }


    /**
     * ---------------------------------------
     * 删除
     * ---------------------------------------
     */
    public function actionDelete()
    {
        $idss = Yii::$app->request->param('id', 0);
        $ids = implode(',', array_unique((array)$idss));

        if (empty($ids)) {
            $this->error('请选择要操作的数据!');
        }

        $_where = 'id in(' . $ids . ')';
        if (Room::deleteAll($_where)) {
            // delete room set
            $this->_deleteRoomSet($idss);
            $this->success('删除成功', $this->getForward());
        } else {
            $this->error('删除失败！');
        }
    }

    public function _getRoomtime($roomtime)
    {
        if ($roomtime == 1) {
            return date("Y-m-d 23:23:59", strtotime("+1 month"));
        } elseif ($roomtime == 2) {
            return date("Y-m-d 23:23:59", strtotime("+2 month"));
        } elseif ($roomtime == 3) {
            return date("Y-m-d 23:23:59", strtotime("+3 month"));
        } elseif ($roomtime == 6) {
            return date("Y-m-d 23:23:59", strtotime("+6 month"));
        } elseif ($roomtime == 12) {
            return date("Y-m-d 23:23:59", strtotime("+12 month"));
        } elseif ($roomtime == 0) {
            return date("Y-m-d 23:23:59", strtotime("+99 month"));
        } elseif ($roomtime == -1) {
            return date("Y-m-d 23:23:59", strtotime("+1 day"));
        }  elseif ($roomtime == -2) {
            return date("Y-m-d H:i:s",time()+3600); //1小时
        }  elseif ($roomtime == -3) {
            return date("Y-m-d H:i:s",time()+7200); //2小时
        }  elseif ($roomtime == -4) {
            return date("Y-m-d H:i:s",time()+10800); //3小时
        } else {
            return date("Y-m-d 23:23:59", strtotime("+1 month"));
        }
    }

    public function _setRoomDefault($room)
    {
        $dbpc = Yii::$app->db2;



        $rule1 = '<div class="RuleT1">【北京赛车】</div>
    <div class="RuleT2">【游戏介绍】</div>
    北京赛车是经国家财政部批准，由北京国家福利彩票管理中心统一发行的赛车主题排列型高频彩票；每期开奖车号共十个,每个车号除了整合玩法，第1~10名、冠亚和值、冠亚组合分别为一个竞猜组，大小/单双/龙虎、车道数字、冠亚大小、冠亚特码、十球全接、特码区段。<br>
    北京赛车、幸运飞艇同以「1~10」十个号码作为开奖依据，完全公平公正公开、开奖透明、无法作弊！<br>
    <br>
    <div class="RuleT2">【相关资料】</div>
    【开奖官网】www.bwlc.net 或www.uc3039.com/<br>
    【官方APP下载】请直接搜索「北京赛车」<br>
    【开奖时间】北京赛车为每天上午09:07~晚上23:57每五分钟开奖一期，每天179期，与官网完全同步。<br>
    <br>
    <div class="RuleT2">【玩法】</div>
    <div class="RuleT3">【1~10名猜大小单双】</div>
    <div class="RuleT3">第一名～第十名车号：开出之号码大于或等于6为大，小于或等于5为小。开出的号码偶数为双，号码奇数为单。</div>
    ■奖历：含本1.95倍<br>
    ■限额：10-20,000<br>
    ■格式：名次/大小单双/金额<br>
    　例：12345/双/100 = 1~5车道买双各100 = 总500<br>
    <div class="RuleT3">【1~10名猜车号】</div>
    <div class="RuleT3">每一个车号为一竞猜组合，开奖结果「竞猜车号」对应所猜「车道」视为中奖，其余情形视为不中奖。</div>
    ■奖历：含本9.6倍<br>
    ■限额：10-10,00<br>
    ■格式：名次/号码/金额<br>
    　例：12345/89/20 = 1~5车道的8号、9号各买20 = 总200<br>
    　例：1357/890/20 = 1.3.5.7车道的8号、9号、10号各买20 = 总240<br>
    <div class="RuleT3">【1~5名猜龙虎】</div>
    <div class="RuleT3">(1)第一名vs第十名，(2)第二名vs第九名，(3)第三名vs第八名，(4)第四名vs第七名，(5)第五名vs第六名，前比后大为龙，反之为虎。</div>
    ■奖历：含本1.95倍<br>
    ■限额：10-20,000<br>
    ■格式：名次/号码/金额<br>
    　例：123/龙/100 = 1~3车道买龙各100=总300<br>
   <div class="RuleT3">【猜冠亚号码】 </div>
<div class="RuleT3">猜冠军及亚军车号（前2名），每次竞猜2个号码，顺序不限。 </div>
■格式：组/号码/金额 <br>
例：组/5-6/50=5号.6号在冠亚军（顺序不限）=总下注50 <br>
例：组/1-9.3-7/100=1.9号车或3.7号车再冠亚军（顺序不限）=总下注200<br>
    <div class="RuleT3">【冠亚和值(特码)猜大小单双】</div>
    <div class="RuleT3">冠军车号+亚军车号 = 冠亚和值 = 特码 = 数字3~19</div>
    <div class="RuleT3">冠亚和值大于或等于12为大，小于或等于11为小。开出的号码偶数为双，号码奇数为单。</div>
    ■奖历：<br>
    　「大」、「双」含本2.1倍。<br>
    　「小」、「单」含本1.7倍。<br>
    ■限额：10-20,000<br>
    ■格式：和/大小单双/金额<br>
    　例：和双100 = 「冠亚和」的双100<br>
    　例：和大100 = 「冠亚和」的大100<br>
    <div class="RuleT3">【冠亚和值(特码)猜数字】</div>
    <div class="RuleT3">「冠亚和值」为「特码」可能出现的结果为3~19，竞猜中对应「冠亚和值」数字的视为中奖，其余视为不中奖。</div>
    ■奖历：<br>
    　3.4.18.19，含本40倍，限额10-1,000<br>
    　5.6.16.17，含本21倍，限额10-2,000<br>
    　7.8.14.15，含本13倍，限额10-3,000<br>
    　9.10.12.13，含本10倍，限额10-4,000<br>
    　11，含本8倍，限额10-5,000<br>
    ■格式：和(特)/数字/金额<br>
    　例：和567/100 = 竞猜「冠亚和」的值为5或6或7各100 = 总300<br>
    <div class="RuleT2">每期下注：总额10万封顶！</div>
    <br>
    <div class="RuleT2">【说明】</div>
    ■没带名次默认竞猜第一名，如「双/100」第一名双100、「123/100」第一名123号各100<br>
    ■竞猜时因各地网路品质不定，可能有1-2秒延迟，以系统判定是否竞猜成功为准。<br>
    ■0号即为10号，竞猜时输入0即为10，输入10即为1、10号。<br>
    　例：0/0/100 = 视为竞猜第10名10号车冠军<br>
    　例：0/100 = 视为竞猜第1名10号车冠军<br>
    <br>
    <div class="RuleT2">若因任何无法抗拒之外力因素导致临时关盘，或是官网问题临时关盘，会员不得在没有下注的情况下以结果论的要求赔偿损失，所有投注皆以会员投注记录明细为主。</div>
';
        $rule2 = '<div class="RuleT1">【幸运飞艇】</div>
    <div class="RuleT2">【游戏介绍】</div>
    幸运飞艇：是马耳他共和国瓦莱塔福利联合委员会独家发行的一款高效率快乐猜，源于F1赛艇的彩票游戏；幸运飞艇除了整合玩法，第1~10名、冠亚和值、冠亚组合分别为一个竞猜组，大小/单双/龙虎、船号数字、冠亚大小、冠亚特码、十球全接、特码区段。<br>
    幸运飞艇、北京赛车同以「1~10」十个号码作为开奖依据，完全公平公正公开、开奖透明、无法作弊！<br>
    <br>
    <div class="RuleT2">【相关资料】</div>
    【开奖官网】luckyairship.com 或 http://www.uc3039.com/<br>
    【官方APP下载】请直接搜索「幸运飞艇」<br>
    【开奖时间】幸运飞艇为每天13点09分开始，第二天凌晨04点04分结束，五分钟一期，一共是179期，与官网完全同步。<br>
    <br>
    <div class="RuleT2">【玩法】</div>
    <div class="RuleT3">【1~10名猜大小单双】</div>
    <div class="RuleT3">第一名～第十名车号：开出之号码大于或等于6为大，小于或等于5为小。开出的号码偶数为双，号码奇数为单。</div>
    ■奖历：含本1.95倍<br>
    ■限额：10-20,000<br>
    ■格式：名次/大小单双/金额<br>
    　例：12345/双/100 = 1~5车道买双各100 = 总500<br>
    <div class="RuleT3">【1~10名猜车号】</div>
    <div class="RuleT3">每一个车号为一竞猜组合，开奖结果「竞猜车号」对应所猜「车道」视为中奖，其余情形视为不中奖。</div>
    ■奖历：含本9.6倍<br>
    ■限额：10-10,000<br>
    ■格式：名次/号码/金额<br>
    　例：12345/89/20 = 1~5车道的8号、9号各买20 = 总200<br>
    　例：1357/890/20 = 1.3.5.7车道的8号、9号、10号各买20 = 总240<br>
    <div class="RuleT3">【1~10名猜组合】</div>
    <div class="RuleT3">竞猜内容为「大单」「小双」「小单」「大双」，共4种。</div>
    ■奖历：<br>
    　「大单」、「小双」含本2.4倍，<br>
    　「小单」、「大双」含本3倍。<br>
    ■限额：10-10,000<br>
    ■格式：名次/组合/金额<br>
    　例：890/大单/50 = 8.9.10车道大单各买50 = 总150<br>
    <div class="RuleT3">【1~5名猜龙虎】</div>
    <div class="RuleT3">(1)第一名vs第十名，(2)第二名vs第九名，(3)第三名vs第八名，(4)第四名vs第七名，(5)第五名vs第六名，前比后大为龙，反之为虎。</div>
    ■奖历：含本1.95倍<br>
    ■限额：10-20,000<br>
    ■格式：名次/号码/金额<br>
    　例：123/龙/100 = 1~3车道买龙各100=总300<br>
    <div class="RuleT3">【冠亚和值(特码)猜大小单双】</div>
    <div class="RuleT3">冠军车号+亚军车号 = 冠亚和值 = 特码 = 数字3~19</div>
    <div class="RuleT3">冠亚和值大于或等于12为大，小于或等于11为小。开出的号码偶数为双，号码奇数为单。</div>
    ■奖历：<br>
    　「大」、「双」含本2.1倍。<br>
    　「小」、「单」含本1.7倍。<br>
    ■限额：10-20,000<br>
    ■格式：和(特)/大小单双/金额<br>
    　例：和双100 = 「冠亚和」的双100<br>
    　例：和大100 = 「冠亚和」的大100<br>
    <div class="RuleT3">【冠亚和值(特码)猜数字】</div>
    <div class="RuleT3">「冠亚和值」为「特码」可能出现的结果为3~19，竞猜中对应「冠亚和值」数字的视为中奖，其余视为不中奖。</div>
    ■奖历：<br>
    　3.4.18.19，含本40倍，限额10-1,000<br>
    　5.6.16.17，含本21倍，限额10-2,000<br>
    　7.8.14.15，含本13倍，限额10-3,000<br>
    　9.10.12.13，含本10倍，限额10-4,000<br>
    　11，含本8倍，限额10-5,000<br>
    ■格式：和/数字/金额<br>
    　例：和567/100 = 竞猜「冠亚和」的值为5或6或7各100 = 总300<br>
    <div class="RuleT2">【说明】</div>
    ■没带名次默认竞猜第一名，如「双/100」第一名双100、「123/100」第一名123号各100<br>
    ■竞猜时因各地网路品质不定，可能有1-2秒延迟，以系统判定是否竞猜成功为准。<br>
    ■0号即为10号，竞猜时输入0即为10，输入10即为1、10号。<br>
    　例：0/0/100 = 视为竞猜第10名10号车冠军<br>
    　例：0/100 = 视为竞猜第1名10号车冠军<br>
    <div class="RuleT2">每期下注：总额10万封顶！</div>
    <br>
    <br>
    <div class="RuleT2">若因任何无法抗拒之外力因素导致临时关盘，或是官网问题临时关盘，会员不得在没有下注的情况下以结果论的要求赔偿损失，所有投注皆以会员投注记录明细为主。</div>';

        $rule3 = '<div class="RuleT1">【重庆时时彩】</div>
<div class="RuleT2">【游戏介绍】</div>
重庆时时彩是经中国国家财政部批准由中国重庆福利彩票发行中心统一发行的『时时彩』具玩法简单、中奖率高、开奖快。重庆时时彩是国内首个快开彩票，也是国内第一个开设夜场的快开彩票。<br>
<br>
<div class="RuleT2">【相关资料】</div>
【开奖官网】www.cqcp.net 或 www.uc3039.com<br>
【开奖时间】白天10:00至晚上21:55,10分钟开奖一次，晚上22:00至凌晨02:00，5分钟开奖一次，全天共120期。<br>
<br>
<div class="RuleT2">【玩法】</div>
<div class="RuleT2">位数即为第几球<br>
【万=1球】【千=2球】【百=3球】【十=4球】【个=5球】<r=br>
</div>
<div class="RuleT3">【一星定位】</div>
<div class="RuleT3">万、千、百、十、个位数中分别从0～9中任意选择一个或一个以上号码竞猜。开奖结果与竞猜位数、号码相同即视为中奖，其余情形则视为不中奖。</div>
举例：竞猜【万】1【千】3【百】2【十】4【个】5，开奖结果为：【万】1【千】1【百】2【十】2【个】5，其中[万][百][个]位数奖号与竞猜位数号码相符，视为中奖。 其余[千][十]位数奖号与竞猜位数号码不相符，视为不中奖。<br>
■奖历：含本9.6倍<br>
■限额：10-10,000<br>
■格式：位数/号码/金额<br>
例：1/5/100 = 买万位数的5号100 <br>
例：5/5/100 = 买个位数的5号100 <br>
例：123/5/100 = 买万位、千位、百位的5号各100 = 总300 <br>
<div class="RuleT3">【双面盘】</div>
<div class="RuleT3">万、千、百、十、个位数中的"大、小、单、双"。0-4为小，5-9为大；1、3、5、7、9为单，0、2、4、6、8为双。</div>
举例：竞猜万位数 "大"。开奖结果为：59436 【万】位数号码为"大"，视为中奖。<br>
举例：竞猜百位数 "双"。开奖结果为：59336 【百】位数号码为"单"，视为不中奖。<br>
■奖历：含本1.95倍<br>
■限额：10-20,000<br>
■格式：球号/大、小、单、双/金额<br>
例：2/单/100 = 买千位的单100<br>
例：5/大/200 = 买个位的大200<br>
例：123/大/100 = 买万位、千位、百位的大各100 = 总300 <br>
<div class="RuleT3">【前、中、后三总和】</div>
<div class="RuleT3">分为选择前三总和【万千百】、中三总和【千百十】、后三总和【百十个】所竞猜位置的三位数开出号码为豹子、顺子、对子、半顺、杂六。</div>
豹子：如000、111..999等<br>
顺子：如234、890、901…等(顺序不限)<br>
对子：如001、288、585…等(不包括豹子)<br>
半顺：所竞猜位置的三位数开出号码任意两个顺序数字相连（不包括顺子、对子，号码9、0、1相连）。如235、378、283…等。<br>
※如果开奖号码为前三顺子、前三对子，则前三半顺视为不中奖。 <br>
杂六：所竞猜位置的三位数开出号码皆不相同且不能为连号，视为杂六。如179、264、802…等。<br>
※如果开奖号码为中三豹子、中三顺子、中三对子、中三半顺，则杂六视为不中奖。<br>
■奖历、限额：<br>
豹子含本70倍，限额10-2,000<br>
顺子含本13倍，限额10-10,000<br>
对子含本3.3倍，限额10-20,000<br>
半顺含本2.5倍，限额10-20,000<br>
杂六含本2倍，限额10-20,000<br>
■格式：定位/种类/金额<br>
例：前/对子/300 = 买前对子300<br>
例：中/豹子/100 = 买中豹子100<br>
例：后/杂六/100 = 买后杂六100<br>
<div class="RuleT3">【五球总和】</div>
<div class="RuleT3">总和大小：万、千、百、十、个五个位数加总作为开奖依据，五位数总和0~22为小，23~45为大。 </div>
<div class="RuleT3">总和单双：万、千、百、十、个五个位数加总作为开奖依据，五位数总和1、3、5…43、45为单，总和0、2、4、6…42、44为双。 </div>
■奖历：含本1.95倍<br>
■限额：10-20,000<br>
■格式：大、小、单、双/金额<br>
例：总大200 <br>
例：总单100<br>
<div class="RuleT3">【龙虎】</div>
<div class="RuleT3">万位数为龙、个位数为虎，以万、个两位数比大小，号码0为最小、9为最大。开奖第一球万大于第五球个为龙，反之为虎。万、个两位数号码相同，则为和。</div>
■奖历：<br>
「龙」含本1.95倍<br>
「虎」含本1.95倍<br>
「和」含本8.9倍<br>
■限额：10-20,000<br>
■格式：龙、虎、和/金额<br>
例：龙/200<br>
例：和/100<br>
<div class="RuleT3">【包号】</div>
<div class="RuleT3">包号投注与一星投注的结算方法相同。例如下1/100(不加球号)，则下注将为12345/1/100，开奖结果5球内如果有1则视为中奖，反之未中奖！</div>
■奖历：<br>
含本XXXX倍<br>
■限额：10-20,000<br>
■格式：包/位数/金额<br>
例：包/1/200<br>
例：1/100<br>
<div class="RuleT2" style="color: red;">
注意！如若不加位数默认识别为万位投注（除包号玩法以外）
</div>
<div class="RuleT2">每期下注：总额10万封顶！</div>
<br>
<br>
<div class="RuleT2">若因任何无法抗拒之外力因素导致临时关盘，或是官网问题临时关盘，会员不得在没有竞猜的情况下以结果论的要求赔偿损失，所有竞猜皆以会员竞猜记录明细为主。</div>';


        $rule4 = '<div class="RuleT1">【北京28】</div>
<div class="RuleT2">【游戏介绍】</div>
北京28是PC蛋蛋首创的竞猜游戏。开奖号码源于国家福利彩票【国家福利彩票官网：bwlc.net】北京28开奖号码为三个（0 - 9）中随机产生的数字之和，总共有28种结果（0 - 27）。
<br>
<br>
<div class="RuleT2">北京28是根据什么开奖的？</div>
北京28开奖结果来源于国家福利彩票北京快乐8开奖号码，北京快乐8每期开奖共开出20个数字，北京28将这20个开奖号码按照由小到大的顺序依次排列；取其1-6位开奖号码相加，和值的末位数作为北京28开奖第一个数值；取其7-12位开奖号码相加，和值的末位数作为北京28开奖第二个数值，取其13-18位开奖号码相加，和值的末位数作为北京28开奖第三个数值；三个数值相加即为北京28最终的开奖结果。<br>
<br>
例如：快乐8第"641841"期数据从小到大排序01,03,13,16,23,27,40,41,45,49,53,54,57,62,63,67,68,71,72,78<br>
第一区[第1/2/3/4/5/6位数字] 1,3,13,16,23,27<br>
计算：1+3+13+16+23+27= 83<br>
结果为3<br>
第二区[第7/8/9/10/11/12位数字] 40,41,45,49,53,54<br>
计算：40+41+45+49+53+54= 282<br>
结果为2<br>
第三区[第13/14/15/16/17/18位数字] 57,62,63,67,68,71<br>
计算：57+62+63+67+68+71= 388<br>
结果为8<br>
最终游戏开奖为：3+2+8=13<br>
<br>
<div class="RuleT2">【相关资料】</div>
【视频开奖官网】www.bwlc.net 或 http://www.uc3039.com/<br>
【开奖时间】北京28为每天早上9:05至23:55，每5分钟一期，共179期。<br>
<br>
<div class="RuleT2">【玩法】</div>
<div class="RuleT3">【猜特码大小单双】</div>
<div class="RuleT3">开出之号码小于或等于13为小，大于或等于14为大。开出的号码偶数为双，号码奇数为单。开出结果为13,14时大小单双赔含本2.2倍 </div>
■奖历：含本1.95倍<br>
■限额：50-30,000<br>
■格式：大小单双+金额<br>
　例：大100；单200<br>
<div class="RuleT3">【猜特码组合】</div>
<div class="RuleT3">开出之号码小于或等于13为小，大于或等于14为大，偶数为双，奇数为单，竞猜「大单」「小双」「小单」「大双」，共4种。开出结果为13,14时中组合回本</div>
■奖历：<br>
「小单」「大双」含本3.5倍<br>
「大单」「小双」含本2倍<br>
■限额：50-20,000<br>
■格式：组合+金额<br>
　例：大单100；小双200<br>
<div class="RuleT3">【猜和值(特码)数字】</div>
<div class="RuleT3">开出的三个号码加总为和值(特码)，可能的结果为0至27，以下赔率皆含本。</div>
■奖历、限额：<br>
00、27含本300倍，限额50-500<br>
01、26含本150倍，限额50-1,000<br>
02、25含本60倍，限额50-3,000<br>
03、24含本40倍，限额50-5,000<br>
04、23含本30倍，限额50-6,000<br>
05、22含本25倍，限额50-8,000<br>
06、21含本20倍，限额50-9,000<br>
07、20含本18倍，限额50-10,000<br>
08、19含本16倍，限额50-10,000<br>
09、18含本16倍，限额50-10,000<br>
10、17含本14倍，限额50-10,000<br>
11、16含本14倍，限额50-10,000<br>
12、13、14、15含本10倍，限额50-10,000<br>
■格式：点or / 符号<br>
　例：9.100<br>
<div class="RuleT3">【猜极大、极小】</div>
<div class="RuleT3">开出的三个号码加总为和值(特码)，可能的结果为0至27，00至05为极小，22至27为极大</div>
■奖历：含本8倍<br>
■限额：50-10,000<br>
■格式：极大or极小+金额<br>
　例：极大100；极小200<br>
<div class="RuleT3">【猜对子、顺子、豹子】</div>
<div class="RuleT3">以三个开奖数字为准，三个开奖数字任意两个数字相同为对子，三个相同的数字为豹子，三个相邻的数字为顺子（0-9个数字头尾不相连）</div>
■奖历、限额：<br>
对子含本3.5倍，限额50-30,000（豹子不属于对子）<br>
顺子含本10倍，限额50-10,000<br>
豹子含本61倍，限额50-3,000<br>
例：<br>
2+1+2、5+8+8 为对子<br>
2+0+1、7+6+5 为顺子<br>
8+9+0、9+1+0 不属于顺子<br>
■格式：对子or顺子or豹子+金额 <br>
　例：对子100，豹子200<br>
<div class="RuleT2">每期下注：总额15万封顶！</div>
<br>
<br>
<div class="RuleT2">若因任何无法抗拒之外力因素导致临时关盘，或是官网问题临时关盘，会员不得在没有下注的情况下以结果论的要求赔偿损失，所有投注皆以会员投注记录明细为主。</div>
<div class="RuleT2">若发现多个帐号为同一人所有，或同一帐号进行无风险投注，将永久取消帐号。本平台最终解释权归澎湃娱乐所有，并保留修改以上条款的最终权力。</div>';

        $rule5 = '<div class="RuleT1">【加拿大28】</div>
<div class="RuleT2">【游戏介绍】</div>
加拿大28采用加拿大彩票公司开奖数据，每三分半钟开一期。【官网:lotto.bclc.com】为了方便玩家可以更快更直观的了解当期的计算和结果，我们在导航上设置了开奖数据，每期实时更新！<br>
<br>
<div class="RuleT2">加拿大28是根据什么开奖的？</div>
加拿大彩票公司BCLC彩，每期开奖共开出20个数字。加拿大28将这20个开奖号码按照由小到大的顺序依次排列；取其第2/5/8/11/14/17位开奖号码相加，和值的末位数作为加拿大28开奖第一个数值；取其第3/6/9/12/15/18位开奖号码相加，和值的末位数作为加拿大开奖第二个数值，取其第4/7/10/13/16/19位开奖号码相加，和值的末位数作为加拿大28开奖第三个数值；三个数值相加即为加拿大28最终的开奖结果。<br>
<br>
例如：加拿大BCLC第"1749110"期数据从小到大排序 7,8,14,16,17,22,26,34,39,41,42,48,54,58,63,64,69,72,73,79<br>
第一区[第2/5/8/11/14/17位数字] 8,17,34,42,58,69<br>
计算：8+17+34+42+58+69= 228<br>
结果为：8<br>
第二区[第3/6/9/12/15/18位数字] 14,22,39,48,63,72<br>
计算：14+22+39+48+63+72= 258<br>
结果为：8<br>
第三区[第4/7/10/13/16/19位数字] 16,26,41,54,64,73<br>
计算：16+26+41+54+64+73= 274<br>
结果为：4<br>
最终游戏开奖为：8+8+4=20<br>
<br>
<div class="RuleT2">【相关资料】</div>
【视频开奖官网】lotto.bclc.com 或 http://www.uc3039.com<br>
【开奖时间】加拿大28为全天候开奖，每三分半钟开一期，每天维护时间约：晚上19:00点到20:00点，周一可能会有延迟。<br>
<br>
<div class="RuleT2">【玩法】</div>
<div class="RuleT3">【猜特码大小单双】</div>
<div class="RuleT3">开出之号码小于或等于13为小，大于或等于14为大。开出的号码偶数为双，号码奇数为单。开出结果为13,14时大小单双赔含本2.2倍 </div>
■奖历：含本1.95倍<br>
■限额：50-30,000<br>
■格式：大小单双+金额<br>
　例：大100；单200<br>
<div class="RuleT3">【猜特码组合】</div>
<div class="RuleT3">开出之号码小于或等于13为小，大于或等于14为大，偶数为双，奇数为单，竞猜「大单」「小双」「小单」「大双」，共4种。开出结果为13,14时中组合回本</div>
■奖历：<br>
「小单」「大双」含本3.5倍<br>
「大单」「小双」含本 2 倍<br>
■限额：50-20,000<br>
■格式：组合+金额<br>
　例：大单100；小双200<br>
<div class="RuleT3">【猜和值(特码)数字】</div>
<div class="RuleT3">开出的三个号码加总为和值(特码)，可能的结果为0至27，以下赔率皆含本。</div>
■奖历、限额：<br>
00、27含本300倍，限额50-500<br>
01、26含本150倍，限额50-1,000<br>
02、25含本60倍，限额50-3,000<br>
03、24含本40倍，限额50-5,000<br>
04、23含本30倍，限额50-6,000<br>
05、22含本25倍，限额50-8,000<br>
06、21含本20倍，限额50-9,000<br>
07、20含本18倍，限额50-10,000<br>
08、19含本16倍，限额50-10,000<br>
09、18含本16倍，限额50-10,000<br>
10、17含本14倍，限额50-10,000<br>
11、16含本14倍，限额50-10,000<br>
12、13、14、15含本10倍，限额50-10,000<br>
■格式：单点有效字眼：点or/符号<br>
　例：8点100<br>
<div class="RuleT3">【猜极大、极小】</div>
<div class="RuleT3">开出的三个号码加总为和值(特码)，可能的结果为0至27，00至05为极小，22至27为极大</div>
■奖历：含本8倍<br>
■限额：50-10,000<br>
■格式：极大or极小+金额<br>
　例：极大100；极小200<br>
<div class="RuleT3">【猜对子、顺子、豹子】</div>
<div class="RuleT3">以三个开奖数字为准，三个开奖数字任意两个数字相同为对子，三个相同的数字为豹子，三个相邻的数字为顺子（0-9个数字头尾不相连）</div>
■奖历、限额：<br>
对子含本3.5倍，限额50-30,000（豹子不属于对子）<br>
顺子含本10倍，限额50-10,000<br>
豹子含本61倍，限额50-3,000<br>
例：<br>
2+1+2、5+8+8 为对子<br>
2+0+1、7+6+5 为顺子<br>
8+9+0、9+1+0 不属于顺子<br>
■格式：对子or顺子or豹子+金额 <br>
　例：对子100，豹子200<br>
<div class="RuleT2">每期下注：总额15万封顶！</div>
<br>
<br>
<div class="RuleT2">若因任何无法抗拒之外力因素导致临时关盘，或是官网问题临时关盘，会员不得在没有下注的情况下以结果论的要求赔偿损失，所有投注皆以会员投注记录明细为主。</div>
<div class="RuleT2">若发现多个帐号为同一人所有，或同一帐号进行无风险投注，将永久取消帐号。本平台最终解释权归澎湃娱乐所有，并保留修改以上条款的最终权力。</div>';

        $rule1 = strval($rule1);
        $rule2 = strval($rule2);
        $rule3 = strval($rule3);
        $rule4 = strval($rule4);
        $rule5 = strval($rule5);

        try{
            $dbpc->createCommand("INSERT INTO `fn_lottery1` (`id`, `roomid`, `gameopen`, `da`, `xiao`, `dan`, `shuang`, `long`, `hu`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `heda`, `hexiao`, `hedan`, `heshuang`, `he341819`, `he561617`, `he781415`, `he9101213`, `he11`, `tema`, `daxiao_min`, `daxiao_max`, `danshuang_min`, `danshuang_max`, `longhu_min`, `longhu_max`, `tema_min`, `tema_max`, `he_min`, `he_max`, `zuhe_min`, `zuhe_max`, `fengtime`, `rules`) VALUES(" . $room . ", " . $room . ", 'true', '1.988', '1.988', '1.988', '1.988', '1.988', '1.988', '2.4', '3', '3', '2.4', '1.988', '1.988', '1.988', '1.988', '40.5', '20.5', '13.5', '10.125', '8.1', '9.88', '5', '10000', '5', '10000', '5', '10000', '5', '5000', '5', '3000', '5', '5000', 50, '{$rule1}')")->execute();

            $dbpc->createCommand("INSERT INTO `fn_lottery2` (`id`, `roomid`, `gameopen`, `da`, `xiao`, `dan`, `shuang`, `long`, `hu`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `heda`, `hexiao`, `hedan`, `heshuang`, `he341819`, `he561617`, `he781415`, `he9101213`, `he11`, `tema`, `daxiao_min`, `daxiao_max`, `danshuang_min`, `danshuang_max`, `longhu_min`, `longhu_max`, `tema_min`, `tema_max`, `he_min`, `he_max`, `zuhe_min`, `zuhe_max`, `fengtime`, `rules`) VALUES(" . $room . ", " . $room . ", 'true', '1.988', '1.988', '1.988', '1.988', '1.988', '1.988', '2.4', '3', '3', '2.4', '1.988', '1.988', '1.988', '1.988', '40.5', '20.5', '13.5', '10.125', '8.1', '9.88', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', 100, '{$rule2}')")->execute();

            $dbpc->createCommand("INSERT INTO `fn_lottery3` (`id`, `roomid`, `gameopen`, `da`, `xiao`, `dan`, `shuang`, `tema`, `zongda`, `zongxiao`, `zongdan`, `zongshuang`, `long`, `hu`, `he`, `q_baozi`, `z_baozi`, `h_baozi`, `q_duizi`, `z_duizi`, `h_duizi`, `q_shunzi`, `z_shunzi`, `h_shunzi`, `q_banshun`, `z_banshun`, `h_banshun`, `q_zaliu`, `z_zaliu`, `h_zaliu`, `dx_min`, `ds_min`, `lh_min`, `tm_min`, `zh_min`, `bz_min`, `dz_min`, `sz_min`, `bs_min`, `zl_min`, `dx_max`, `ds_max`, `lh_max`, `tm_max`, `zh_max`, `bz_max`, `dz_max`, `sz_max`, `bs_max`, `zl_max`, `fengtime`, `rules`) VALUES(" . $room . ", " . $room . ", 'true', '1.95', '1.95', '1.95', '1.95', '9.68', '1.95', '1.95', '1.95', '1.95', '1.95', '1.95', '9', '65', '65', '65', '2.5', '2.5', '2.5', '12', '12', '12', '2.5', '2.5', '2.5', '2.2', '2.2', '2.2', 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 50000, 50000, 50000, 20000, 50000, 10000, 50000, 20000, 50000, 50000, 40, '{$rule3}')")->execute();

            $dbpc->createCommand("INSERT INTO `fn_lottery4` (`id`, `roomid`, `gameopen`, `0027`, `0126`, `0225`, `0324`, `0423`, `0522`, `0621`, `0720`, `891819`, `10111617`, `1215`, `1314`, `jida`, `jixiao`, `baozi`, `duizi`, `shunzi`, `dxds`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `dxds_zongzhu1`, `dxds_1314_1`, `dxds_zongzhu2`, `dxds_1314_2`, `dxds_zongzhu3`, `dxds_1314_3`, `zuhe_zongzhu1`, `zuhe_1314_1`, `zuhe_zongzhu2`, `zuhe_1314_2`, `zuhe_zongzhu3`, `zuhe_1314_3`, `danzhu_min`, `zongzhu_max`, `shuzi_max`, `zuhe_max`, `dxds_max`, `jidx_max`, `baozi_max`, `shunzi_max`, `duizi_max`, `setting_shazuhe`, `setting_fanxiangzuhe`, `setting_tongxiangzuhe`, `setting_liwai`, `fengtime`, `rules`) VALUES(" . $room . ", " . $room . ",'true','300','150','60','40','30','25','20','18','16','14','10','10','8','8','61','3.5','10','1.95','2','3.5','3.5','2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10','20000','20000','20000','20000','20000','20000','20000','20000','false','false','false','',30,'{$rule4}')")->execute();

            $dbpc->createCommand("INSERT INTO `fn_lottery5` (`id`, `roomid`, `gameopen`, `0027`, `0126`, `0225`, `0324`, `0423`, `0522`, `0621`, `0720`, `891819`, `10111617`, `1215`, `1314`, `jida`, `jixiao`, `baozi`, `duizi`, `shunzi`, `dxds`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `dxds_zongzhu1`, `dxds_1314_1`, `dxds_zongzhu2`, `dxds_1314_2`, `dxds_zongzhu3`, `dxds_1314_3`, `zuhe_zongzhu1`, `zuhe_1314_1`, `zuhe_zongzhu2`, `zuhe_1314_2`, `zuhe_zongzhu3`, `zuhe_1314_3`, `danzhu_min`, `zongzhu_max`, `shuzi_max`, `zuhe_max`, `dxds_max`, `jidx_max`, `baozi_max`, `shunzi_max`, `duizi_max`, `setting_shazuhe`, `setting_fanxiangzuhe`, `setting_tongxiangzuhe`, `setting_liwai`, `fengtime`, `rules`) VALUES(" . $room . ", " . $room . ",'true','300','150','60','40','30','25','20','18','16','14','10','10','8','8','61','3.5','10','1.95','2','3.5','3.5','2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10','20000','20000','10000','20000','500','200','500','10000','false','false','false','',30,'{$rule5}')")->execute();

            $dbpc -> createCommand("INSERT INTO `fn_lottery6` (`id`, `roomid`, `gameopen`, `da`, `xiao`, `dan`, `shuang`, `long`, `hu`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `heda`, `hexiao`, `hedan`, `heshuang`, `he341819`, `he561617`, `he781415`, `he9101213`, `he11`, `tema`, `daxiao_min`, `daxiao_max`, `danshuang_min`, `danshuang_max`, `longhu_min`, `longhu_max`, `tema_min`, `tema_max`, `he_min`, `he_max`, `zuhe_min`, `zuhe_max`, `fengtime`, `rules`) VALUES(".$room.", ".$room.", 'false', '1.988', '1.988', '1.988', '1.988', '1.988', '1.988', '2.4', '3', '3', '2.4', '1.988', '1.988', '1.988', '1.988', '40.5', '20.5', '13.5', '10.125', '8.1', '9.88', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', 100, '【未来娱乐房间规则】')")->execute();

            $dbpc -> createCommand("INSERT INTO `fn_lottery7` (`id`, `roomid`, `gameopen`, `da`, `xiao`, `dan`, `shuang`, `long`, `hu`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `heda`, `hexiao`, `hedan`, `heshuang`, `he341819`, `he561617`, `he781415`, `he9101213`, `he11`, `tema`, `daxiao_min`, `daxiao_max`, `danshuang_min`, `danshuang_max`, `longhu_min`, `longhu_max`, `tema_min`, `tema_max`, `he_min`, `he_max`, `zuhe_min`, `zuhe_max`, `fengtime`, `rules`) VALUES(".$room.", ".$room.", 'false', '1.988', '1.988', '1.988', '1.988', '1.988', '1.988', '2.4', '3', '3', '2.4', '1.988', '1.988', '1.988', '1.988', '40.5', '20.5', '13.5', '10.125', '8.1', '9.88', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', 100, '【未来娱乐房间规则】')")->execute();

            $dbpc -> createCommand("INSERT INTO `fn_lottery8` (`id`, `roomid`, `gameopen`, `da`, `xiao`, `dan`, `shuang`, `tema`, `zongda`, `zongxiao`, `zongdan`, `zongshuang`, `long`, `hu`, `he`, `q_baozi`, `z_baozi`, `h_baozi`, `q_duizi`, `z_duizi`, `h_duizi`, `q_shunzi`, `z_shunzi`, `h_shunzi`, `q_banshun`, `z_banshun`, `h_banshun`, `q_zaliu`, `z_zaliu`, `h_zaliu`, `dx_min`, `ds_min`, `lh_min`, `tm_min`, `zh_min`, `bz_min`, `dz_min`, `sz_min`, `bs_min`, `zl_min`, `dx_max`, `ds_max`, `lh_max`, `tm_max`, `zh_max`, `bz_max`, `dz_max`, `sz_max`, `bs_max`, `zl_max`, `fengtime`, `rules`) VALUES(".$room.", ".$room.", 'false', '1.95', '1.95', '1.95', '1.95', '9.88', '1.988', '1.988', '1.988', '1.988', '1.988', '1.988', '9', '61', '61', '61', '3.3', '3.3', '3.3', '14.5', '14.5', '14.5', '2.5', '2.5', '2.5', '3', '3', '3', 5, 5, 5, 5, 5, 0, 5, 5, 5, 5, 3000000, 3000000, 3000000, 3000000, 3000000, 3000000, 3000000, 3000000, 3000000, 3000000, 30, '【未来娱乐房间规则】')")->execute();

            $dbpc -> createCommand("INSERT INTO `fn_lottery9` (`id`, `roomid`, `gameopen`, `da`, `xiao`, `dan`, `shuang`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `tong_baozi`, `tong_duizi`, `tong_shunzi`, `tong_sanza`, `tong_erza`, `zhi_baozi`, `zhi_duizi`, `zhi_shunzi`, `zhi_sanza`, `zhi_erza`, `zhi_sanjun`, `dx_min`, `dx_max`, `ds_min`, `ds_max`, `dadan_min`, `dadan_max`, `xiaodan_min`, `xiaodan_max`, `dashuang_min`, `dashuang_max`, `xiaoshuang_min`, `xiaoshuang_max`, `tong_baozi_min`, `tong_baozi_max`, `tong_shunzi_min`, `tong_shunzi_max`, `tong_duizi_min`, `tong_duizi_max`, `tong_sanza_min`, `tong_sanza_max`, `tong_erza_min`, `tong_erza_max`, `zhi_baozi_min`, `zhi_baozi_max`, `zhi_shunzi_min`, `zhi_shunzi_max`, `zhi_duizi_min`, `zhi_duizi_max`, `zhi_sanza_min`, `zhi_sanza_max`, `zhi_erza_min`, `zhi_erza_max`, `zhi_sanjun_min`, `zhi_sanjun_max`, `setting_10shazuhe`, `setting_baozitongsha`, `setting_open`, `fengtime`, `rules`) VALUES
(".$room.", ".$room.",	'false',	'1.98',	'1.98',	'1.99',	'1.99',	'3.98',	'3.99',	'3.98',	'3.99',	'19',	'19',	'19',	'19',	'19',	'19',	'19',	'19',	'19',	'19',	'60',	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	1,	300000,	'true',	'true',	1,	50,	'<div class=\"RuleT1\">【快三娱乐】</div>\r\n    <div class=\"RuleT2\">【购买联系客服】</div>\r\n    快3游戏投注是指以三个号码组合为一注进行单式投注，每个投注号码为1-6共六个自然数中的任意一个，一组三个号码的组合称为一注。购买者可对其选定的投注号码进行多倍投注，投注倍数范围为7-190倍。<br>\r\n    <br>\r\n    <div class=\"RuleT2\">【相关资料】</div>\r\n    【开奖官网】 www.uc3039.com<br>\r\n    【开奖时间】8:30 - 22:10 全天共82期<br>\r\n    <br>\r\n    <div class=\"RuleT2\">【玩法】</div>\r\n    <div class=\"RuleT3\">【总和大小单双】</div>\r\n    <div class=\"RuleT3\">由三粒骰子的结果相加，所得的数值3到10为小，11到18为大。奇数为单，偶数为双。</div>\r\n    举例：竞猜总小100，开奖结果为：1+2+3=6，开奖结果小于11，视为中奖。否则视为不中奖。<br>\r\n    ■奖历：含本1.98倍<br>\r\n    ■限额：10-10,000<br>\r\n    ■格式：总/大小单双/金额<br>\r\n    例：总/大/100 = 买总和大于11, 100元 <br>\r\n    <div class=\"RuleT3\">【总和组合玩法】</div>\r\n    <div class=\"RuleT3\">与总和大小单双相同, 组合玩法为:大单/小单/大双/小双。</div>\r\n    举例：竞猜总和大单100。开奖结果为：6+2+3=11（即为大又为单），视为中奖。<br>\r\n    举例：竞猜总和大单100。开奖结果为：6+2+3=11 （只为大不为单），视为不中奖。<br>\r\n    ■奖历：<br>\r\n    大单: 含本20倍<br>\r\n    小单: 含本2.0倍<br>\r\n    大双: 含本2.0倍<br>\r\n    小双: 含本2.4倍<br>\r\n    ■限额：10-20,000<br>\r\n    ■格式：总/大单、小单、大双、小双/金额<br>\r\n    例：总/大单/100 = 买总和的大单100<br>\r\n    <div class=\"RuleT3\">【通选玩法】</div>\r\n    <div class=\"RuleT3\">开奖结果的三位数开出号码为豹子、顺子、对子、三杂、二杂。</div>\r\n    豹子：如222、111..999等<br>\r\n    顺子：如123、234、456…等<br>\r\n    对子：如112、233、556…等(不包括豹子)<br>\r\n    三杂：如621、789、421...等<br>\r\n    二杂：如128、133、326...等<br>\r\n    ※如果开奖号码为豹子、对子、顺子、三杂、二杂则视为中奖。<br>\r\n    ■奖历、限额：<br>\r\n    豹子含本32倍，限额10-2,000<br>\r\n    顺子含本8倍，限额10-10,000<br>\r\n    对子含本2倍，限额10-20,000<br>\r\n    三杂含本1.8倍，限额10-20,000<br>\r\n    二杂含本1.5倍，限额10-20,000<br>\r\n    ■格式：特/种类/金额<br>\r\n    例：特/对子/300 = 买对子300<br>\r\n    例：特/豹子/100 = 买豹子100<br>\r\n    例：特/三杂/100 = 买三杂100<br>\r\n    <div class=\"RuleT3\">【直选玩法】</div>\r\n    <div class=\"RuleT3\">与通选玩法类似,直接选择豹子、对子、顺子、三杂、二杂会出现的号码 </div>\r\n    举例：豹子/222/100 , 如果开奖号码为222 则为中奖<br>\r\n    举例：三杂/135/100 , 如果开奖号码为135 则为中奖<br>\r\n    ■奖历：<br>\r\n    豹子含本190倍，限额10-2,000<br>\r\n    顺子含本32倍，限额10-10,000<br>\r\n    对子含本12倍，限额10-20,000<br>\r\n    三杂含本32倍，限额10-20,000<br>\r\n    二杂含本6.5倍，限额10-20,000<br>\r\n    ■限额：10-20,000<br>\r\n    ■格式：种类/号码/金额<br>\r\n    例：豹子/111-222-333/100 = 买豹子111/222/333 各100元 = 300元 <br>\r\n    例：顺子/123-456/100 = 买顺子123/456各100元 = 200元<br>\r\n    <div class=\"RuleT2\">每期下注：总额10万封顶！</div>\r\n    <br>\r\n    <br>\r\n    <div class=\"RuleT2\">若因任何无法抗拒之外力因素导致临时关盘，或是官网问题临时关盘，会员不得在没有竞猜的情况下以结果论的要求赔偿损失，所有竞猜皆以会员竞猜记录明细为主。</div>')")->execute();

            $dbpc->createCommand("INSERT INTO `fn_setting` (`id`, `roomid`, `setting_game`, `setting_wordkeys`, `setting_kefu`, `setting_cancelbet`, `setting_ischat`, `setting_tishi`, `setting_video`, `setting_qrcode`, `setting_people`, `setting_sysimg`, `setting_robotsimg`, `setting_robots`, `setting_robot_min`, `setting_robot_max`, `setting_robot_pointmin`, `setting_robot_pointmax`, `setting_templates`, `setting_flyorder`, `setting_downmark`, `display_custom`, `display_extend`, `display_plan`, `display_game`, `msg1_time`, `msg1_cont`, `msg2_time`, `msg2_cont`, `msg3_time`, `msg3_cont`, `flyorder_type`, `flyorder_user`, `flyorder_pass`, `flyorder_site`, `flyorder_session`, `flyorder_duichong`, `flyorder_pk10`, `flyorder_xyft`, `flyorder_cqssc`) VALUES(" . $room . ", " . $room . ", 'pk10', '垃圾|操|傻逼|黑|艹|妈|娘|逼|日', '欢迎光临，未来娱乐城。 上下分请添加客服微信', 'disable', 'open', 'open', '未来娱乐系统', '/upload/201710251508918115.png', 320, '/upload/201710251508917502.png', '/upload/201710251508917501.png', 0, 0, 15, 0, 300, 'old', 'false', '0', 'true', 'true', 'true', 'true', 0, '0', 0, '0', 0, '0', '0', '0', '0', '0', '0', 'false', 'false', 'false', 'false')")->execute();


            $dbpc -> createCommand() ->update("fn_room",["roomid" => $room],["id" => $room])->execute();

            $updown_content = '500|800|1000|2000|3000';
            $bet_content_1 = '12345/2/50|12345/3/59|12345/4/50|12345/5/60|12345/6/70|12345/7/50|12345/8/33|12345/9/44|12345/0/50|67890/1/88|67890/2/88|67890/3/100|67890/4/99|67890/5/88|67890/6/66|67890/7/88|67890/8/88|67890/9/98|67890/0/100|19852/2/77|157845/3/66|78901/9/67|45679/4/88|67890/2/100|13580/0/66|67890/4/100|和/341819/20|和/671117/20|和/15141312/20|和/大/30|和/双/30|和/小/30|和/789/20|和/45678/20|和/大/20|和/大/50|和/341819/20|1大200|2大58|3大200|4大147|5大200|6大200|7大170|8大230|9大200|0大200|1小200|2小200|3小95|4小150|5小200|6小123|7小200|8小169|9小200|0小96|1单120|2单222|3单65|4单230|5单200|6单89|7单200|8单200 9单200|0单200|1双188|2双200|3双200|4双200|5双200|6双200|7双211|8双200|9双200|0双158|1龙110|2龙260|3龙200|4龙240|5龙200 5虎137|4虎200|3虎200|2虎80|1虎180|345/1/100|12/2/150|89/3/100|234/4/100|890/5/100|1/6/480|567/7/160|123/8/220|9/9/200|345/0/100|12345/1/300|468/2/100';
            $bet_content_2 = $bet_content_1;
            $bet_content_3 = '1/12369/30|5/789/55|4/1564/50|9/50|4/40|虎250|总大100|总双150|12/45/60|532/64/44|4/4562/20|12/12322/55|2双150|5双100|单150|3小200|4/145656/60|5/60|3/40|龙300|虎180|123/45445/33|双223|单55|45/14511/20|1/89899/33|325/45/23|7/45|5/456456/22|7/80|3/753196/66|4/1632/40|4单77|3小220|4大240|总双80|小120|大350|4/45632332/42|124/52/60|5单150|龙145|虎200|45/5645/56|8/55|5/1235644455/20|4/545584/30|4双150|12/12323/42|5/456/75|4/789541/36|虎150|2/3256/111|5/58955/62|1/4568/76|5/56/200|2/3263236/55|4/569/55|12/2321212524/31|4/45897/66|54/56/53|4/456/150|7/33|双400|单160|4/85264/60|4/6/150|412/3232/35|4/789123/44|虎300|龙100|12/12/55|45/562/60|2/65|4/60|45/56/40|5/78956/50|4/5236/33|2单180|5双200|2/60|9/60|4/1245222542121/20|2/1245987/40|5/456/150|3/454566442145/21|5/89546/55|4/12456699866/22|单250|虎300|虎150|龙120|5/78989898454877/20|321/23/50|总大150|大400|小260';
            $bet_content_4 = '单100|大单50|双120 大双100 |13.50 |14.50|小50 小单80 |11点70|双90|大160 大双110 |18点80 |16点60|小110 小双100 双150|单88 小单77|08点50|小双200 小200|单300 大单90|15点88|双250 小双99 极小120|04点80|大180 双110|小188|双120 小双100 |10草100|双300|大320|大双200 大280|13点50 |14点50 单50|大单50|10点50 |小110 小单120|单420|大双120 大120|小双88 小80|小111 小双138|大130 双140|单100 小单100|小双123双116|单200大250|大单250|双80大90| 8.50 小80|大100大双66|13草50|极大220大230大双100|极小150单50|小单50|单165 小150|大单110 大120|大双120 18草50|19草50|小110 小单90|大134|大200|大156 大单100|双280|大双90 大50|17草80 大单177|单200 大单200|小99 小双80|小77 小单66 11草50|小单100 14点50|大双100 双133|大350|18草100 单200 大单100|小100|大145 大双152|小236 小双173|大113 大单132 17点90|双142 14点100|单130 大单88|双120 大双188 13.50| 14.50|小130 小单140 11点70|双350|大200 大双50|小110 小双170|单200 小单121|06点70|单180 大单112|04点60|大180 双120|小399|双100 小双120 12草80|双290|大80|大双200 大50|13点50 14点50 单50 大单50|20点50 21点50 大120 大单100|大230|大双180 大170|小双110 小130|小180 小双140|大200 |双122|单170 小单160 |小170|小双120 双80|单88 大99|大单111|双113 大114|大双400 大400|双200 16草100|大双200 14.100|大单200 大200|小双300 小300|大500|单600|小600|双600|大单400 17草100|17草50 15草50 大单400|小100 双100 小双100';
            $bet_content_5 = $bet_content_4;
            $data = [];
            $codes =  [1 => 'bjpk10',2 =>'mlaft',3 =>'cqssc',4 =>'bjkl8',5 => 'cakeno'];

            // 添加方案5个
            foreach ($codes as $type => $game){
                $res = $dbpc ->createCommand(sprintf("select * from fn_robotplan2 where roomid = %d and `game` = %d",$room,$type))->queryOne();
                if($res){
                    continue;
                }
                if($type == 1){
                    $data[] = [$type,$room,$updown_content,$bet_content_1,date("Y-m-d H:i:s")];
                }
                if($type == 2){
                    $data[] = [$type,$room,$updown_content,$bet_content_2,date("Y-m-d H:i:s")];
                }
                if($type == 3){
                    $data[] = [$type,$room,$updown_content,$bet_content_3,date("Y-m-d H:i:s")];
                }
                if($type == 4){
                    $data[] = [$type,$room,$updown_content,$bet_content_4,date("Y-m-d H:i:s")];
                }
                if($type == 5){
                    $data[] = [$type,$room,$updown_content,$bet_content_5,date("Y-m-d H:i:s")];
                }
            }

            if(!empty($data)){
                $dbpc ->createCommand()->batchInsert("fn_robotplan2",['game','roomid','updown_content','bet_content','addtime'],$data)->execute();
            }

            // 添加机器人
            $datarobot = [];

            // 添加方案5个
            foreach ($codes as $type => $game){
                $plan = $dbpc ->createCommand(sprintf("select * from fn_robotplan2 where roomid = %d and `game` = %d",$room,$type))->queryOne();
                $plan = $plan['id'];
                if($type == 1){
                    $name = 'pk10';
                }elseif($type == 2){
                    $name = 'xyft';
                }elseif($type == 3){
                    $name = 'xyft';
                }elseif($type == 4){
                    $name = 'xy28';
                }else{
                    $name = 'jnd28';
                }

                $datarobot[] = [$name.'_1','/upload/201805231527090892.jpg',$type,$room,3000,800,4000,$plan];
                $datarobot[] = [$name.'_2','/upload/201805231527090906.png',$type,$room,3000,800,4000,$plan];
                $datarobot[] = [$name.'_3','/upload/201805231527090919.png',$type,$room,3000,800,4000,$plan];
                $datarobot[] = [$name.'_4','/upload/201805231527090933.jpg',$type,$room,3000,800,4000,$plan];
                $datarobot[] = [$name.'_5','/upload/201805241527091267.png',$type,$room,3000,800,4000,$plan];
            }

            if(!empty($datarobot)){
                $dbpc ->createCommand()->batchInsert("fn_robots2",['name','headimg','game','roomid','money','up_money','down_money','plan'],$datarobot)->execute();
            }

            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage());
            return false;
        }




    }

    public function _deleteRoomSet($room)
    {
        $dbpc = Yii::$app->db2;


        $dbpc->createCommand("delete from `fn_lottery1` where roomid = {$room}")->execute();
        $dbpc->createCommand("delete from `fn_lottery2` where roomid = {$room}")->execute();
        $dbpc->createCommand("delete from `fn_lottery3` where roomid = {$room}")->execute();
        $dbpc->createCommand("delete from `fn_lottery4` where roomid = {$room}")->execute();
        $dbpc->createCommand("delete from `fn_lottery5` where roomid = {$room}")->execute();
        $dbpc->createCommand("delete from `fn_lottery6` where roomid = {$room}")->execute();
        $dbpc->createCommand("delete from `fn_lottery7` where roomid = {$room}")->execute();
        $dbpc->createCommand("delete from `fn_lottery8` where roomid = {$room}")->execute();
        $dbpc->createCommand("delete from `fn_lottery9` where roomid = {$room}")->execute();

        $dbpc->createCommand("delete from `fn_robots2` where roomid = {$room}")->execute();
        $dbpc->createCommand("delete from `fn_robotplan2` where roomid = {$room}")->execute();

        $dbpc->createCommand("delete from `fn_setting` where roomid = {$room}")->execute();

    }

    public function actionAjax_roomurl(){
        $roomurl = Yii::$app->request->get('roomurl');
        Yii::$app->cache->set('roomurl',$roomurl);
        return json_encode(["code" => 200,"msg"=>"ok"]);
    }


}
