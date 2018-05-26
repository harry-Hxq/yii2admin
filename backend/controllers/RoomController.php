<?php

namespace backend\controllers;

use backend\models\Pc\Room;
use backend\models\Pc\Search\RoomSearch;
use kartik\form\ActiveForm;
use Yii;
use yii\web\Response;

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

            $model->setAttributes($data);
            /* 保存用户数据到数据库 */
            if ($model->save()) {
                $roomid = $model->id;
                $this->_setRoomDefault($roomid);
                $this->success('操作成功', $this->getForward());
            } else {

                $this->error('操作错误');
            }
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



        $dbpc->createCommand("INSERT INTO `fn_lottery1` (`id`, `roomid`, `gameopen`, `da`, `xiao`, `dan`, `shuang`, `long`, `hu`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `heda`, `hexiao`, `hedan`, `heshuang`, `he341819`, `he561617`, `he781415`, `he9101213`, `he11`, `tema`, `daxiao_min`, `daxiao_max`, `danshuang_min`, `danshuang_max`, `longhu_min`, `longhu_max`, `tema_min`, `tema_max`, `he_min`, `he_max`, `zuhe_min`, `zuhe_max`, `fengtime`, `rules`) VALUES(" . $room . ", " . $room . ", 'true', '1.988', '1.988', '1.988', '1.988', '1.988', '1.988', '2.4', '3', '3', '2.4', '1.988', '1.988', '1.988', '1.988', '40.5', '20.5', '13.5', '10.125', '8.1', '9.88', '5', '10000', '5', '10000', '5', '10000', '5', '5000', '5', '3000', '5', '5000', 50, '【未来娱乐房间规则】')")->execute();

        $dbpc->createCommand("INSERT INTO `fn_lottery2` (`id`, `roomid`, `gameopen`, `da`, `xiao`, `dan`, `shuang`, `long`, `hu`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `heda`, `hexiao`, `hedan`, `heshuang`, `he341819`, `he561617`, `he781415`, `he9101213`, `he11`, `tema`, `daxiao_min`, `daxiao_max`, `danshuang_min`, `danshuang_max`, `longhu_min`, `longhu_max`, `tema_min`, `tema_max`, `he_min`, `he_max`, `zuhe_min`, `zuhe_max`, `fengtime`, `rules`) VALUES(" . $room . ", " . $room . ", 'true', '1.988', '1.988', '1.988', '1.988', '1.988', '1.988', '2.4', '3', '3', '2.4', '1.988', '1.988', '1.988', '1.988', '40.5', '20.5', '13.5', '10.125', '8.1', '9.88', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', '0', '30000000', 100, '【未来娱乐房间规则】')")->execute();

        $dbpc->createCommand("INSERT INTO `fn_lottery3` (`id`, `roomid`, `gameopen`, `da`, `xiao`, `dan`, `shuang`, `tema`, `zongda`, `zongxiao`, `zongdan`, `zongshuang`, `long`, `hu`, `he`, `q_baozi`, `z_baozi`, `h_baozi`, `q_duizi`, `z_duizi`, `h_duizi`, `q_shunzi`, `z_shunzi`, `h_shunzi`, `q_banshun`, `z_banshun`, `h_banshun`, `q_zaliu`, `z_zaliu`, `h_zaliu`, `dx_min`, `ds_min`, `lh_min`, `tm_min`, `zh_min`, `bz_min`, `dz_min`, `sz_min`, `bs_min`, `zl_min`, `dx_max`, `ds_max`, `lh_max`, `tm_max`, `zh_max`, `bz_max`, `dz_max`, `sz_max`, `bs_max`, `zl_max`, `fengtime`, `rules`) VALUES(" . $room . ", " . $room . ", 'true', '1.95', '1.95', '1.95', '1.95', '9.68', '1.95', '1.95', '1.95', '1.95', '1.95', '1.95', '9', '65', '65', '65', '2.5', '2.5', '2.5', '12', '12', '12', '2.5', '2.5', '2.5', '2.2', '2.2', '2.2', 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 50000, 50000, 50000, 20000, 50000, 10000, 50000, 20000, 50000, 50000, 40, '【未来娱乐房间规则】')")->execute();

        $dbpc->createCommand("INSERT INTO `fn_lottery4` (`id`, `roomid`, `gameopen`, `0027`, `0126`, `0225`, `0324`, `0423`, `0522`, `0621`, `0720`, `891819`, `10111617`, `1215`, `1314`, `jida`, `jixiao`, `baozi`, `duizi`, `shunzi`, `dxds`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `dxds_zongzhu1`, `dxds_1314_1`, `dxds_zongzhu2`, `dxds_1314_2`, `dxds_zongzhu3`, `dxds_1314_3`, `zuhe_zongzhu1`, `zuhe_1314_1`, `zuhe_zongzhu2`, `zuhe_1314_2`, `zuhe_zongzhu3`, `zuhe_1314_3`, `danzhu_min`, `zongzhu_max`, `shuzi_max`, `zuhe_max`, `dxds_max`, `jidx_max`, `baozi_max`, `shunzi_max`, `duizi_max`, `setting_shazuhe`, `setting_fanxiangzuhe`, `setting_tongxiangzuhe`, `setting_liwai`, `fengtime`, `rules`) VALUES(" . $room . ", " . $room . ",'true','300','150','60','40','30','25','20','18','16','14','10','10','8','8','61','3.5','10','1.95','2','3.5','3.5','2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10','20000','20000','20000','20000','20000','20000','20000','20000','false','false','false','',30,'【未来娱乐房间规则】')")->execute();

        $dbpc->createCommand("INSERT INTO `fn_lottery5` (`id`, `roomid`, `gameopen`, `0027`, `0126`, `0225`, `0324`, `0423`, `0522`, `0621`, `0720`, `891819`, `10111617`, `1215`, `1314`, `jida`, `jixiao`, `baozi`, `duizi`, `shunzi`, `dxds`, `dadan`, `xiaodan`, `dashuang`, `xiaoshuang`, `dxds_zongzhu1`, `dxds_1314_1`, `dxds_zongzhu2`, `dxds_1314_2`, `dxds_zongzhu3`, `dxds_1314_3`, `zuhe_zongzhu1`, `zuhe_1314_1`, `zuhe_zongzhu2`, `zuhe_1314_2`, `zuhe_zongzhu3`, `zuhe_1314_3`, `danzhu_min`, `zongzhu_max`, `shuzi_max`, `zuhe_max`, `dxds_max`, `jidx_max`, `baozi_max`, `shunzi_max`, `duizi_max`, `setting_shazuhe`, `setting_fanxiangzuhe`, `setting_tongxiangzuhe`, `setting_liwai`, `fengtime`, `rules`) VALUES(" . $room . ", " . $room . ",'true','300','150','60','40','30','25','20','18','16','14','10','10','8','8','61','3.5','10','1.95','2','3.5','3.5','2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10000','2.2','10','20000','20000','10000','20000','500','200','500','10000','false','false','false','',30,'【未来娱乐房间规则】')")->execute();

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
