<?php

namespace machour\yii2\notifications\widgets;

use Yii;
use yii\web\AssetBundle;

/**
 * Assets for the NotificationsWidget
 *
 * @package machour\yii2\notifications\widgets
 */
class NotificationsAsset extends AssetBundle
{
    /**
     * @var string The assets source path
     */
    public $sourcePath = '@vendor/machour/yii2-notifications/assets/';

    /**
     * @var string The assets directory
     */
    public static $assetsDirectory = '@vendor/machour/yii2-notifications/assets/';

    /**
     * @var array The widget js files
     */
    public $js = [
        'jquery.timeago.js',
        'notifications.js',
    ];

    /**
     * @var array The widget css files
     */
    public $css = [
        'notifications.less',
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
        'rmrevin\yii\fontawesome\AssetBundle',
    ];

    /**
     * Gets the required theme filename if it exists
     *
     * @param string $theme The theme name
     * @param string $type The resource type. Either "js" or "css"
     * @return bool|string Returns the filename if it exists, or FALSE.
     */
    public static function getFilename($theme, $type)
    {
        $filename = 'themes/' . $theme . '.' . $type;
        if (file_exists(Yii::getAlias(self::$assetsDirectory) . $filename)) {
            return $filename;
        }
        return false;
    }

    /**
     * Gets the jQuery timeago locale file based on the current Yii language
     *
     * @return bool|string Returns the path to the locale file, or FALSE if
     *                     it does not exist.
     */
    public static function getTimeAgoI18n($locale)
    {
        $filename = 'locales/jquery.timeago.' . $locale . '.js';
        if (file_exists(Yii::getAlias(self::$assetsDirectory) . $filename)) {
            return $filename;
        }
        return false;
    }

}
