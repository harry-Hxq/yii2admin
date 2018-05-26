<?php
/* ===========================以下为本页配置信息================================= */
/* 页面基本属性 */
$this->title = '控制台';
$this->params['title_sub'] = '控制台';

/* 渲染其他文件 */
//echo $this->renderFile('@app/views/public/login.php');

/* 加载页面级别JS */
//$this->registerJsFile('@web/static/common/js/app.js');

function get_query_val($table,$column,$where){
    $db = Yii::$app->db2;
    $sql = sprintf("select %s from %s where %s",$column,$table,$where);
    $res = $db ->createCommand($sql)->queryOne();
    return $res[$column];
}

function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
    if(is_array($arrays)){
        foreach ($arrays as $array){
            if(is_array($array)){
                $key_arrays[] = $array[$sort_key];
            }else{
                return false;
            }
        }
    }else{
        return false;
    }
    array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
    return array_slice($arrays,0,9);
}

$arr = array();
$m = (int)get_query_val('fn_order', 'sum(`money`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
$m += (int)get_query_val('fn_pcorder', 'sum(`money`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
$m += (int)get_query_val('fn_sscorder', 'sum(`money`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
$m += (int)get_query_val('fn_jsscorder', 'sum(`money`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
$m += (int)get_query_val('fn_jssscorder', 'sum(`money`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
$m += (int)get_query_val('fn_mtorder', 'sum(`money`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
$z = (int)get_query_val('fn_order', 'sum(`status`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
$z += (int)get_query_val('fn_pcorder', 'sum(`status`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
$z += (int)get_query_val('fn_sscorder', 'sum(`status`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
$z += (int)get_query_val('fn_jsscorder', 'sum(`status`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
$z += (int)get_query_val('fn_jssscorder', 'sum(`status`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
$z += (int)get_query_val('fn_mtorder', 'sum(`status`)', " `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
$arr['zyk'] = $z - $m;
$arr['allsf'] = (int)get_query_val('fn_upmark', 'sum(`money`)', "time like '" . date('Y-m-d') . "%' and status = '已处理' and type = '上分' and `jia` = 'false'");
$arr['allpeople'] = (int)get_query_val('fn_user', 'count(*)', " `jia` = 'false' and `money` > '0'");
$arr['allmoney'] = (int)get_query_val('fn_user', 'sum(`money`)', " `jia` = 'false' and `money` > '0'");
$sql = sprintf("select count(*) as online from fn_user where `statustime` > %d and `jia` = '%s' ",time()-300,'false');
$res =  Yii::$app->db2-> createCommand($sql)->queryOne();
$arr['online'] = $res['online'];
$sql = sprintf("select roomid,roomname from fn_room where `roomtime` > '%s'",date("Y-m-d H:i:s"));
$roominfo = Yii::$app->db2-> createCommand($sql)->queryAll();
$zyk = array();
$zrs= array();
$zsf= array();
foreach ($roominfo as $room){
    $m1 = (int)get_query_val('fn_order', 'sum(`money`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
    $m1 += (int)get_query_val('fn_pcorder', 'sum(`money`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
    $m1 += (int)get_query_val('fn_sscorder', 'sum(`money`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
    $m1 += (int)get_query_val('fn_jsscorder', 'sum(`money`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
    $m1 += (int)get_query_val('fn_jssscorder', 'sum(`money`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
    $m1 += (int)get_query_val('fn_mtorder', 'sum(`money`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and (`status` > 0 or `status` < 0)");
    $z1 = (int)get_query_val('fn_order', 'sum(`status`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
    $z1 += (int)get_query_val('fn_pcorder', 'sum(`status`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
    $z1 += (int)get_query_val('fn_sscorder', 'sum(`status`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
    $z1 += (int)get_query_val('fn_jsscorder', 'sum(`status`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
    $z1 += (int)get_query_val('fn_jssscorder', 'sum(`status`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");
    $z1 += (int)get_query_val('fn_mtorder', 'sum(`status`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `addtime` like '" . date('Y-m-d') . "%' and status >= 0");

    $allsf = (int)get_query_val('fn_upmark', 'sum(`money`)', "roomid = {$room['roomid']} and time like '" . date('Y-m-d') . "%' and status = '已处理' and type = '上分' and `jia` = 'false'");
    $allpeople = (int)get_query_val('fn_user', 'count(*)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `money` > '0'");
//    $data['allmoney'] = (int)get_query_val('fn_user', 'sum(`money`)', "`roomid` = '{$room['roomid']}' and `jia` = 'false' and `money` > '0'");

    $zyk[] = [
        "roomid" => $room['roomid'],
        "roomname" => $room['roomname'],
        "yk" => $z1 - $m1
    ];
    $zrs[] = [
        "roomid" => $room['roomid'],
        "roomname" => $room['roomname'],
        "rs" => $allpeople
    ];
    $zsf[] = [
        "roomid" => $room['roomid'],
        "roomname" => $room['roomname'],
        "sf" => $allsf
    ];
}
$zyk = my_sort($zyk,'yk',SORT_DESC);
$zrs = my_sort($zrs,'rs',SORT_DESC);
$zsf = my_sort($zsf,'sf',SORT_DESC);

?>

<!--<div class="note note-info">-->
<!--    <p> Dark mega menu style. 这里是提示信息 </p>-->
<!--</div>-->

<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="#">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="1349"><?php echo $arr['zyk'] ?></span>
                </div>
                <div class="desc"> 今日玩家总盈亏 </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red" href="#">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="12,5"><?php echo $arr['allsf'] ?></span></div>
                <div class="desc"> 今日上分金额 </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green" href="#">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="549"><?php echo $arr['allpeople'] ?></span>
                </div>
                <div class="desc"> 真实用户总数 </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 purple" href="#">
            <div class="visual">
                <i class="fa fa-globe"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="89"><?php echo $arr['online'] ?></span></div>
                <div class="desc"> 当前在线人数 </div>
            </div>
        </a>
    </div>
</div>
<div class="clearfix"></div>

<div class="row-fluid margin-bottom-30 col-md-6 col-lg-6 col-xs-12" >
    <div class="span6">
        <h3>今日盈亏排行榜</h3>
        <?php foreach ($zyk as $yk){?>
        <li class="list-group-item">
            <span class="badge"><?php echo $yk['yk'] ?></span>
            <?php echo $yk['roomname'].'('.$yk['roomid'].')'; ?>
        </li>
        <?php } ?>
        <!-- Blockquotes -->
    </div>
</div>
<div class="row-fluid margin-bottom-30 col-md-6 col-lg-6 col-xs-12">
    <div class="span6">
        <h3>房间人数排行榜</h3>
        <ul class="list-group">
            <?php foreach ($zrs as $yk){?>
                <li class="list-group-item">
                    <span class="badge"><?php echo $yk['rs'] ?></span>
                    <?php echo $yk['roomname'].'('.$yk['roomid'].')'; ?>
                </li>
            <?php } ?>
        </ul>
        <!-- Blockquotes -->
    </div>
</div>
<div class="row-fluid margin-bottom-30 col-md-6 col-lg-6 col-xs-12">
    <div class="span6">
        <h3>房间上分排行榜</h3>
        <ul class="list-group">
            <?php foreach ($zsf as $yk){?>
                <li class="list-group-item">
                    <span class="badge"><?php echo $yk['sf'] ?></span>
                    <?php echo $yk['roomname'].'('.$yk['roomid'].')'; ?>
                </li>
            <?php } ?>
        </ul>
        <!-- Blockquotes -->
    </div>
</div>


<!-- 定义数据块 -->
<?php $this->beginBlock('test'); ?>
jQuery(document).ready(function() {
    highlight_subnav('room/manager'); //子导航高亮
});
<?php $this->endBlock() ?>
<!-- 将数据块 注入到视图中的某个位置 -->
<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?>
