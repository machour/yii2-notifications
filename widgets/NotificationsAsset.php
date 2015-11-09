<?php

namespace machour\yii2\notifications\widgets;

use Yii;
use yii\web\AssetBundle;

class NotificationsAsset extends AssetBundle
{
    public $sourcePath = '@vendor/machour/yii2-notifications/assets/';

    public $js = [
        'notifications.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}