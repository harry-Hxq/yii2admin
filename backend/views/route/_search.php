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
    <?= $form->field($model, 'remark')->textInput()->label('请输入位置') ?>
    </div>
    <div class="col-md-2">
        <?=$form->field($model, 'type')->dropDownList([''=>'全部',1=>'摩托',2=>'小车'],['class'=>'form-control'])->label('执勤类型'); ?>
    </div>
    <div class="col-md-2">
        <?=$form->field($model, 'time_type')->dropDownList([''=>'全部',1=>'上午',2=>'下午',3=>'晚上',4=>'全天'],['class'=>'form-control'])->label('执勤时间'); ?>
    </div>

    <div class="col-md-2">
        <div class="form-group" style="margin-top: 24px;">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
