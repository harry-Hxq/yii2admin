<?php

use yii\helpers\Html;
use common\core\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Ad */
/* @var $form common\core\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'options'=>[
        'class'=>"form-aaa "
    ]
]); ?>

<?= $form->field($model, 'username')->iconTextInput([
    'class'=>'form-control c-md-2',
    'iconPos' => 'left',
    'iconClass' => 'icon-user',
    'placeholder' => 'username',
    'readonly' => true
])->label('用户名') ?>

<div class="form-group">
    <label>密码</label>
    <div class="">
        <div class="input-icon left">
            <i class="icon-lock"></i>
            <input type="password" class="form-control c-md-2" name="Admin[password]" placeholder="密码不变请留空" />
        </div>
    </div>
</div>


<div class="form-actions">
    <?= Html::submitButton('<i class="icon-ok"></i> 确定', ['class' => 'btn blue ajax-post','target-form'=>'form-aaa']) ?>
    <?= Html::button('取消', ['class' => 'btn']) ?>
</div>
<?php ActiveForm::end(); ?>

