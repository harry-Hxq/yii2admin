<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserStopLog */

$this->title = 'Create User Stop Log';
$this->params['breadcrumbs'][] = ['label' => 'User Stop Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-stop-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
