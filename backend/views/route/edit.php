<?php

use yii\helpers\Html;
use common\core\ActiveForm;
use kartik\datetime\DateTimePicker;

\backend\assets\RouteAsset::register($this);

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

        <?= $form->field($model, 'type')->radioList([
            '1' => '摩托','2'=>'小车'
        ])->label('类型') ?>

        <?= $form->field($model, 'time_type')->radioList([
            '1' => '上午','2'=>'下午','3' => '晚上', '4' => '全天'
        ])->label('执勤时间') ?>


        <div class="form-group field-route-remark">

            <div id="r-result" style="width: 100%">选择位置:<input class="form-control" type="text" id="suggestId" size="20" value="百度" style="width:150px;" /></div>
            <div id="searchResultPanel" style="border:1px solid #C0C0C0;width:150px;height:auto; display:none;"></div>

            <div style="width: 800px;height: 400px" id="allmap">

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

<!-- 定义数据块 -->
<?php $this->beginBlock('test'); ?>
jQuery(document).ready(function() {
highlight_subnav('route/index'); //子导航高亮
});
<?php $this->endBlock() ?>
<!-- 将数据块 注入到视图中的某个位置 -->
<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?>
