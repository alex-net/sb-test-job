<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class NvsSAsset extends AssetBundle
{
    public $baseUrl = '@web/js';
    public $basePath = '@webroot/js';
    public $js = ['s-vs-s.js'];
    public $depends = [
        YaMapsAsset::class,
        YiiAsset::class,
    ];
}