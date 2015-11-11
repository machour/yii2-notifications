<?php

namespace machour\yii2\notifications\widgets;

use Yii;
use yii\web\AssetBundle;

class NotificationsAsset extends AssetBundle
{
    /**
     * @var string The assets source path
     */
    public $sourcePath = '@vendor/machour/yii2-notifications/assets/';

    /**
     * @var array The widget js library
     */
    public $js = [
        'notifications.js',
    ];

    /**
     * @var string The widget underlying JS library
     */
    public $theme = 'growl';

    /**
     * @var array We depend on jQuery
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    /**
     * Gets the required theme filename if it exists
     *
     * @param string $theme The theme name
     * @param string $type The resource type
     * @return bool|string Returns the filename if it exists, or FALSE.
     */
    public static function getFilename($theme, $type)
    {
        $filename = 'ui/' . $theme . '/' . $theme . '.' . $type;
        if (file_exists(__DIR__ . '/../assets/' . $filename)) {
            return $filename;
        }
        return false;
    }

}