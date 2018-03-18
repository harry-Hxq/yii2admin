<?php

use yii\helpers\Html;
use common\core\ActiveForm;
use kartik\datetime\DateTimePicker;

\backend\assets\EditRouteAsset::register($this);

/* @var $this yii\web\View */
/* @var $model backend\models\Route */
/* @var $form ActiveForm */

/* ===========================以下为本页配置信息================================= */
/* 页面基本属性 */
$this->title = '添加路线';
$this->params['title_sub'] = '添加路线';  // 在\yii\base\View中有$params这个可以在视图模板中共享的参数

?>

<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption font-red-sunglo">
            <i class="icon-settings font-red-sunglo"></i>
            <span class="caption-subject bold uppercase"> 内容信息</span>
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->

        <?php $form = ActiveForm::begin([
            'options' => [
                'class' => "form-aaa "
            ]
        ]); ?>

        <?= $form->field($model, 'title')->iconTextInput([
            'class' => 'form-control c-md-2',
            'iconPos' => 'left',
            'iconClass' => 'icon-user',
            'placeholder' => '请填写标题'
        ])->label('标题') ?>

        <?= $form->field($model, 'start_time')->widget(\kartik\widgets\DateTimePicker::classname(), [
            'language' => 'zh-CN',
            'type' => \kartik\widgets\DateTimePicker::TYPE_INPUT,
            'value' => '2016-07-15',
            'options' => ['class' => 'form-control'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii',
            ]
        ], ['class' => 'c-md-2'])->label('开始时间')->hint('开始时间') ?>

        <?= $form->field($model, 'end_time')->widget(\kartik\widgets\DateTimePicker::classname(), [
            'language' => 'zh-CN',
            'type' => \kartik\widgets\DateTimePicker::TYPE_INPUT,
            //'convertFormat' => 'yyyy-mm-dd',
            'value' => '2016-07-15',
            'options' => ['class' => 'form-control'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii'
            ]
        ], ['class' => 'c-md-2'])->label('结束时间')->hint('结束时间') ?>


        <div class="form-group field-route-remark">
            <div><label class="" for="route-remark">选择位置</label>

            </div>
            <div style="width: 800px;height: 400px">
            <div id="mapContainer"></div>
                <div id="tip">
                    <input type="text" id="keyword" name="keyword" value="请输入关键字：(选定后搜索)" onfocus='this.value=""'/>
                </div>
            </div>
        </div>

        <?= $form->field($model, 'remark')->iconTextInput([
            'class' => 'form-control c-md-4',
            'iconPos' => 'left',
            'iconClass' => 'icon-user',
            'id' => 'remark',
        ])->label('确认位置') ?>

        <?= $form->field($model, 'latitude')->iconTextInput([
            'class' => 'form-control c-md-4',
            'id' => 'latitude',
        ])->label('纬度') ?>

        <?= $form->field($model, 'longitude')->iconTextInput([
            'class' => 'form-control c-md-4',
            'id' => 'longitude',
        ])->label('经度') ?>

        <div id = 'message'></div>

        <div class="form-actions">
            <?= Html::submitButton('<i class="icon-ok"></i> 确定', ['class' => 'btn blue ajax-post', 'target-form' => 'form-aaa']) ?>
            <?= Html::button('取消', ['class' => 'btn']) ?>
        </div>
        <?php ActiveForm::end(); ?>

        <!-- END FORM-->
    </div>
</div>
<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.2&key=5df198198b1005b5800703e7c895f97d"></script>
<script type="text/javascript">

    var map = new AMap.Map('mapContainer',{
        resizeEnable: true,
        zoom: 13,
        center: [117.0171952227, 25.0750315393]
    });
    AMap.plugin('AMap.Geocoder',function(){
        var geocoder = new AMap.Geocoder({
            city: "龙岩"//城市，默认：“全国”
        });
        var marker = new AMap.Marker({
            map:map,
            bubble:true
        })
        var input = document.getElementById('keyword');
        var remark = document.getElementById('remark');
        var latitude = document.getElementById('latitude');
        var longitude = document.getElementById('longitude');
        var message = document.getElementById('message');
        map.on('click',function(e){
            marker.setPosition(e.lnglat);
            geocoder.getAddress(e.lnglat,function(status,result){
                if(status==='complete'){
                    console.log(e);
                    console.log(result);
                    input.value = result.regeocode.formattedAddress
                    remark.value = result.regeocode.formattedAddress
                    latitude.value = e.lnglat.N
                    longitude.value = e.lnglat.L
                    message.innerHTML = ''
                }else{
                    message.innerHTML = '无法获取地址'
                }
            })
        })

        input.onchange = function(e){
            var address = input.value;
            geocoder.getLocation(address,function(status,result){
                console.log(result);
                if(status==='complete' && result.geocodes.length){
                    console.log(result.geocodes[0].location)
                    marker.setPosition(result.geocodes[0].location);
                    map.setCenter(marker.getPosition())
                    remark.value = address
                    latitude.value = result.geocodes[0].location.N;
                    longitude.value = result.geocodes[0].location.L;
                    message.innerHTML = ''
                }else{
                    message.innerHTML = '无法获取位置'
                }
            })
        }

    });
    
</script>
<script type="text/javascript" src="https://webapi.amap.com/demos/js/liteToolbar.js"></script>

<!-- 定义数据块 -->
<?php $this->beginBlock('test'); ?>
jQuery(document).ready(function() {
highlight_subnav('moto/index'); //子导航高亮
});
<?php $this->endBlock() ?>
<!-- 将数据块 注入到视图中的某个位置 -->
<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?>
