<?php

namespace machour\yii2\notifications\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\AssetBundle;

class NotificationsWidget extends Widget
{

    const THEME_GROWL = 'growl';
    const THEME_NOTY = 'noty';

    /**
     * @var string the theme name to be used for styling the Select2
     */
    public $theme = self::THEME_GROWL;

    /**
     * @var integer the delay between pulls
     */
    public $delay = 5000;

    /**
     * @var integer the XHR timeout in milliseconds
     */
    public $timeout = 2000;

    /**
     * @var array List of built in themes
     */
    protected static $_builtinThemes = [
        self::THEME_GROWL,
        self::THEME_NOTY,
    ];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerAssets();
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $view = $this->getView();

        NotificationsAsset::register($view);

        if (in_array($this->theme, self::$_builtinThemes)) {
            /** @var AssetBundle $bundleClass */
            $bundleClass = __NAMESPACE__ . '\Theme' . ucfirst($this->theme) . 'Asset';
            $bundleClass::register($view);
        }
        $js = 'Notifications({' .
            'url:"' . Url::to(['/notifications/notifications/poll']) . '",' .
            'theme:"' . Html::encode($this->theme) . '",' .
            'timeout:"' . Html::encode($this->timeout) . '",' .
            'delay:"' . $this->delay . '"' .
        '});';

        $view->registerJs($js);
    }

}