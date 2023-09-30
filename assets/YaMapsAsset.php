<?php

namespace app\assets;

use yii\web\AssetBundle;

class YaMapsAsset extends AssetBundle
{
    public function init()
    {
        parent::init();
        $this->js[] = 'https://api-maps.yandex.ru/2.1?apikey=' . getenv('YAMAP_APIKEY') . '&lang=ru_RU';
    }
}