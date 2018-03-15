<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $model common\modelsgii\Route */
/* @var $dataProvider yii\data\ActiveDataProvider  */
/* @var $searchModel backend\models\search\RouteSearch */

/* ===========================以下为本页配置信息================================= */
/* 页面基本属性 */
$this->title = '停车管理';
$this->params['title_sub'] = '停车记录';  // 在\yii\base\View中有$params这个可以在视图模板中共享的参数

/* 加载页面级别资源 */
\backend\assets\TablesAsset::register($this);

$columns = [
    [
        'class' => \common\core\CheckboxColumn::className(),
        'name'  => 'id',
        'options' => ['width' => '20px;'],
        'checkboxOptions' => function ($model, $key, $index, $column) {
            return ['value' => $key,'label'=>'<span></span>','labelOptions'=>['class' =>'mt-checkbox mt-checkbox-outline','style'=>'padding-left:19px;']];
        }
    ],
    [
        'header' => '用户名',
        'attribute' => 'username',
        'options' => ['width' => '150px;'],
        'value' => 'username'
    ],
    [
        'header' => '位置',
        'attribute' => 'remark',
        'options' => ['width' => '150px;']
    ],
    [
        'header' => '停车状态',
        'options' => ['width' => '150px;'],
        'content' => function($model){
            return  $model['status'] == 1 ?
                Html::tag('span','已结束',['class'=>'badge badge-important ']) :
                Html::tag('span','停车中',['class'=>'badge badge-success']);
        },
    ],
    [
        'header' => '是否提醒',
        'options' => ['width' => '150px;'],
        'content' => function($model){
            return  $model['is_tip'] == 1 ?
                Html::tag('span','未提醒',['class'=>'badge badge-success ']) :
                Html::tag('span','已提醒',['class'=>'badge badge-important']);
        }
    ],
    [
        'header' => '停车时间',
        'attribute' => 'create_time',
        'options' => ['width' => '150px;'],
        'format' => ['date', 'php:Y-m-d H:i']
    ],
    [
        'header' => '结束时间',
        'attribute' => 'update_time',
        'options' => ['width' => '150px;'],
        'format' => ['date', 'php:Y-m-d H:i']
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => '操作',
        'template' => '{tip}',
        'options' => ['width' => '200px;'],
        'buttons' => [
            'tip' => function ($url, $model, $key) {
                if($model['status'] == 2 && $model['is_tip'] == 1){
                    return Html::a('提醒',  $url, [
                        'title' => Yii::t('app', '提醒'),
                        'class' => 'btn btn-xs purple ajax-get',
                    ]);
                }
            }
        ],
    ],
];
?>
<div class="portlet light portlet-fit portlet-datatable bordered">
    <div class="portlet-body">
        <?php \yii\widgets\Pjax::begin(['options'=>['id'=>'pjax-container']]); ?>
        <div>
            <?php echo $this->render('_search', ['model' => $searchModel]); ?> <!-- 条件搜索-->
        </div>
        <div class="table-container">
            <form class="ids">
            <?= GridView::widget([
                'dataProvider' => $dataProvider, // 列表数据
                //'filterModel' => $searchModel, // 搜索模型
                'options' => ['class' => 'grid-view table-scrollable'],
                /* 表格配置 */
                'tableOptions' => ['class' => 'table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer'],
                /* 重新排版 摘要、表格、分页 */
                'layout' => '{items}<div class=""><div class="col-md-5 col-sm-5">{summary}</div><div class="col-md-7 col-sm-7"><div class="dataTables_paginate paging_bootstrap_full_number" style="text-align:right;">{pager}</div></div></div>',
                /* 配置摘要 */
                'summaryOptions' => ['class' => 'pagination'],
                /* 配置分页样式 */
                'pager' => [
                    'options' => ['class'=>'pagination','style'=>'visibility: visible;'],
                    'nextPageLabel' => '下一页',
                    'prevPageLabel' => '上一页',
                    'firstPageLabel' => '第一页',
                    'lastPageLabel' => '最后页'
                ],
                /* 定义列表格式 */
                'columns' => $columns,
            ]); ?>
            </form>
        </div>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>

<!-- 定义数据块 -->
<?php $this->beginBlock('test'); ?>
jQuery(document).ready(function() {
    highlight_subnav('user-stop-log/index'); //子导航高亮
});
<?php $this->endBlock() ?>
<!-- 将数据块 注入到视图中的某个位置 -->
<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?>
