<?php

\backend\assets\MapAsset::register($this);
$this->beginPage();
$date = date("Y-m-d");
$timeS =  date("H:i",strtotime(date("Y-m-d")));
$timeN =  date("H:i",strtotime(date("Y-m-d"))+86399);
$defaultStartData = $date."T".$timeS;
$defaultEndData =  $date."T".$timeN;
?>
<style type="text/css">
    body, html,#allmap {width: 100%;height: 100vh;overflow: hidden;margin:0;font-family:"微软雅黑";}
    #l-map{height:100%;width:78%;float:left;border-right:2px solid #bcbcbc;}
    #r-result{height:100%;width:20%;float:left;}
</style>
<?php $this->beginBody() ?>
<div class="portlet light portlet-fit portlet-datatable bordered">
    <div class="portlet-body">
        <div class="container">
            <div id="allmap"></div>
        </div>

        <div class="modal fade" id="confirmMoto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="exampleModalLabel">当前位置</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">当前位置:</label>
                                <input type="text" class="form-control" id="address">
                            </div>
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">开始时间:</label>
                                <input type="datetime-local" class="form-control" id="start_time" value="<?=$defaultStartData ?>">
                            </div>
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">结束时间:</label>
                                <input type="datetime-local" class="form-control" id="end_time" value="<?=$defaultEndData ?>">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="submitMoto()" >确定</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
<!-- 定义数据块 -->
<?php $this->beginBlock('test'); ?>
jQuery(document).ready(function() {
    highlight_subnav('user-stop-log/stop-map'); //子导航高亮
});
<?php $this->endBlock() ?>
<!-- 将数据块 注入到视图中的某个位置 -->
<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?>
<script type="text/javascript">
    // 百度地图API功能
</script>
<?php $this->endPage() ?>