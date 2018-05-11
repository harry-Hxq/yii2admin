<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'options'=>[
        //'class'=>"form-inline",
        'data-pjax' => true, //开启pjax搜索
    ]
]); ?>
<div class="row">

<!--    <div class="col-md-2">-->
<!--        --><?//=$form->field($model, 'route_date')->widget(
//            DatePicker::classname(), [
//            'options' => ['placeholder' => '全部'],
//            'pluginOptions' => [
//                'autoclose' => true,
//                'todayHighlight' => true,
//                'format' => 'yyyy-mm-dd',
//            ]
//        ])->label('日期'); ?>
<!--    </div>-->

    <div class="col-md-2">
    <?= $form->field($model, 'roomid')->textInput()->label('房间id') ?>
    </div>

    <div class="col-md-2">
    <?= $form->field($model, 'roomname')->textInput()->label('房间名字') ?>
    </div>

    <div class="col-md-2">
    <?= $form->field($model, 'roomadmin')->textInput()->label('房间账户') ?>
    </div>

    <div class="col-md-2">
        <div class="form-group" style="margin-top: 24px;">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
