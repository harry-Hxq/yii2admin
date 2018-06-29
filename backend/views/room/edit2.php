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
$this->title = '房间编辑';
$this->params['title_sub'] = '房间编辑';  // 在\yii\base\View中有$params这个可以在视图模板中共享的参数

?>

<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption font-red-sunglo">
            <i class="icon-settings font-red-sunglo"></i>
            <span class="caption-subject bold uppercase"> 修改房间</span>
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->

        <?php $form = ActiveForm::begin([
            'options' => [
                'class' => "form-aaa "
            ]
        ]); ?>

        <?= $form->field($model, 'roomname')->iconTextInput([
            'class' => 'form-control c-md-4',
        ])->label('房间名称') ?>

        <?= $form->field($model, 'roomadmin')->iconTextInput([
            'class' => 'form-control c-md-4',
        ])->label('房间账户') ?>

        <?= $form->field($model, 'roompass')->passwordInput([
            'class' => 'form-control c-md-4',
        ])->label('房间密码') ?>

        <?=$form->field($model, 'roomtime')->widget(
            DateTimePicker::classname(), [
            'options' => ['value' => $model['roomtime']],
            'pluginOptions' => [
                'autoclose' => true,
                'todayHighlight' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
            ]
        ])->label('过期时间'); ?>



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
highlight_subnav('room/index'); //子导航高亮
});
<?php $this->endBlock() ?>
<!-- 将数据块 注入到视图中的某个位置 -->
<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?>
