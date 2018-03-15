<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MapAsset extends AssetBundle
{
    public $sourcePath = '@common/bmapAsset';
    /* 全局CSS文件 */
    public $css = [

    ];
    /* 全局JS文件 */
    public $js = [
        "http://api.map.baidu.com/api?v=2.0&ak=vFXoHFCpIsou67qZj4IbPEdEctOGGRel",
        'bmap.js'
    ];
    /* 依赖关系 */
    public $depends = [
        'backend\assets\CoreAsset',
    ];
}
