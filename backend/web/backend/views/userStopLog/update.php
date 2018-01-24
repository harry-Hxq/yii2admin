<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\UserStopLog */

$this->title = 'Update User Stop Log: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'User Stop Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-stop-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
