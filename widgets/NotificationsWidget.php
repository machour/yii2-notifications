<?php

namespace machour\yii2\notifications\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\AssetBundle;

/**
 * This widget can be used to regularly poll the server for new notifications
 * and trigger them visually using either jQuery Growl, or Noty.
 *
 * This widget should be used in your main layout file as follows:
 *
 * <code>
 * use machour\yii2\notifications\widgets\NotificationsWidget;
 *
 * NotificationsWidget::widget([
 * 'theme' => NotificationsWidget::THEME_NOTY
 * ]);
 * </code>
 *
 * @package machour\yii2\notifications\widgets
 */
class NotificationsWidget extends Widget
{
    /**
     * Use jQuery Growl
     * @see http://ksylvest.github.io/jquery-growl/
     */
    const THEME_GROWL = 'growl';
    /**
     * Use Noty
     * @see http://ned.im/noty/
     */
    const THEME_NOTY = 'noty';

    /**
     * @var array additional options to be passed to the notification library.
     * Please refer to the plugin project page for available options.
     */
    public $clientOptions = [];

    /**
     * @var string the library name to be used for notifications
     * One of the THEME_XXX constants
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

        $params = [
            'url' => Url::to(['/notifications/notifications/poll']),
            'theme' => Html::encode($this->theme),
            'timeout' => Html::encode($this->timeout),
            'delay' => Html::encode($this->delay),
            'options' => $this->clientOptions,
        ];

        $js = 'Notifications(' . Json::encode($params) . ');';

        $view->registerJs($js);
    }

}