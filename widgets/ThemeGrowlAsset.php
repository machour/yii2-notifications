<?php

namespace machour\yii2\notifications\widgets;

use Yii;
use yii\web\AssetBundle;

/**
 * Growl based notifications theme
 *
 * @see https://ksylvest.github.io/jquery-growl/
 */
class ThemeGrowlAsset extends AssetBundle
{
    public $sourcePath = '@bower/growl/';

    public $css = [
        'stylesheets/jquery.growl.css',
    ];

    public $js = [
        'javascripts/jquery.growl.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}