<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $model common\modelsgii\Route */
/* @var $dataProvider yii\data\ActiveDataProvider  */
/* @var $searchModel backend\models\search\RouteSearch */

/* ===========================以下为本页配置信息================================= */
/* 页面基本属性 */
$this->title = '路线管理';
$this->params['title_sub'] = '管理路线信息';  // 在\yii\base\View中有$params这个可以在视图模板中共享的参数

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
        'header' => '执勤类型',
        'attribute' => 'title',
        'options' => ['width' => '150px;'],
        'content' => function($model){
            return $model['type'] == 1 ? Html::tag('span','摩托',['class'=>'badge badge-important']):
                Html::tag('span','小车',['class'=>'badge badge-success']);
        }
    ],
    [
        'header' => '执勤时间点',
        'attribute' => 'start_time',
        'options' => ['width' => '150px;'],
        'content' => function($model){
            if($model['time_type'] == 1){
                return  Html::tag('span','上午',['class'=>'badge badge-success']);
            }elseif($model['time_type'] == 2){
                return  Html::tag('span','下午',['class'=>'badge badge-warning']);
            }elseif($model['time_type'] == 3){
                return  Html::tag('span','晚上',['class'=>'badge badge-important']);
            }else{
                return  Html::tag('span','全天',['class'=>'badge badge-info']);
            }
        }
    ],
    [
        'header' => '地点',
        'attribute' => 'remark',
        'options' => ['width' => '300px;']
    ],
    [
        'header' => '创建时间',
        'attribute' => 'create_time',
        'options' => ['width' => '150px;'],
        'format' => ['date', 'php:Y-m-d H:i']
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => '操作',
        'template' => '{edit} {delete}',
        //'options' => ['width' => '200px;'],
        'buttons' => [
            'edit' => function ($url, $model, $key) {
                return Html::a('<i class="fa fa-edit"></i> 编辑', ['edit','uid'=>$key], [
                    'title' => Yii::t('app', '编辑'),
                    'class' => 'btn btn-xs purple'
                ]);
            },
            'delete' => function ($url, $model, $key) {
                return Html::a('<i class="fa fa-times"></i>', ['delete', 'id'=>$key], [
                    'title' => Yii::t('app', '删除'),
                    'class' => 'btn btn-xs red ajax-get confirm'
                ]);
            }
        ],
    ],
];
?>
<div class="portlet light portlet-fit portlet-datatable bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-settings font-dark"></i>
            <span class="caption-subject font-dark sbold uppercase">管理信息</span>
        </div>
        <div class="actions">
            <div class="btn-group btn-group-devided">
                <?=Html::a('添加 <i class="icon-plus"></i>',['add'],['class'=>'btn green'])?>
            </div>
        </div>
    </div>
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
    highlight_subnav('route/index'); //子导航高亮
});
<?php $this->endBlock() ?>
<!-- 将数据块 注入到视图中的某个位置 -->
<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?>
