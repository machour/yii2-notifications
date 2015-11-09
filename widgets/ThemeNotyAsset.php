<?php

namespace machour\yii2\notifications\widgets;

use Yii;
use yii\web\AssetBundle;


/**
 * Noty based notifications theme
 *
 * @see http://ned.im/noty/#/about
 */
class ThemeNotyAsset extends AssetBundle
{
    public $sourcePath = '@bower/noty/';

    public $js = [
        'js/noty/packaged/jquery.noty.packaged.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}