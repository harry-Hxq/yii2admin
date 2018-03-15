<?php

\backend\assets\MapAsset::register($this);
$this->beginPage();
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