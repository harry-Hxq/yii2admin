<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

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
    <div class="col-md-2">
    <?= $form->field($model, 'username')->textInput()->label('用户名') ?>
    </div>
    <div class="col-md-2">
    <?= $form->field($model, 'remark')->textInput()->label('停车路段') ?>
    </div>
    <div class="col-md-1">
        <?=$form->field($model, 'status')->dropDownList([''=>'全部',1=>'已结束',2=>'停车中'],['class'=>'form-control'])->label('停车状态'); ?>
    </div>
    <div class="col-md-1">
        <?=$form->field($model, 'is_tip')->dropDownList([''=>'全部',1=>'未提醒',2=>'已提醒'],['class'=>'form-control'])->label('提醒状态'); ?>
    </div>

    <div class="col-md-2">
        <div class="form-group" style="margin-top: 24px;">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
