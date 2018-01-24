<?php

use yii\helpers\Html;
use common\core\ActiveForm;
use kartik\datetime\DateTimePicker;

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
            'options'=>[
                'class'=>"form-aaa "
            ]
        ]); ?>

        <?= $form->field($model, 'title')->iconTextInput([
            'class'=>'form-control c-md-2',
            'iconPos' => 'left',
            'iconClass' => 'icon-user',
            'placeholder' => '请填写标题'
        ])->label('标题') ?>

        <?=$form->field($model, 'start_time')->widget(\kartik\widgets\DateTimePicker::classname(),[
            'language' => 'zh-CN',
            'type' => \kartik\widgets\DateTimePicker::TYPE_INPUT,
            'value' => '2016-07-15',
            'options' => ['class' => 'form-control'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd hh:ii',
            ]
        ],['class' => 'c-md-2'])->label('开始时间')->hint('开始时间')?>

        <?=$form->field($model, 'end_time')->widget(\kartik\widgets\DateTimePicker::classname(),[
            'language' => 'zh-CN',
            'type' => \kartik\widgets\DateTimePicker::TYPE_INPUT,
            //'convertFormat' => 'yyyy-mm-dd',
            'value' => '2016-07-15',
            'options' => ['class' => 'form-control'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd hh:ii'
            ]
        ],['class' => 'c-md-2'])->label('结束时间')->hint('结束时间')?>


        <?=$form->field($model, 'remark')->textarea(['class'=>'form-control c-md-4', 'rows'=>5])->label('备注') ?>

        

        <div class="form-actions">
            <?= Html::submitButton('<i class="icon-ok"></i> 确定', ['class' => 'btn blue ajax-post','target-form'=>'form-aaa']) ?>
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
