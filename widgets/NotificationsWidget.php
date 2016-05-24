<?php

namespace machour\yii2\notifications\widgets;

use Yii;
use yii\base\Exception;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

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
 *     'theme' => NotificationsWidget::THEME_GROWL,
 *     // If the notifications count changes, the $('.notifications-count') element
 *     // will be updated with the current count
 *     'counters' => ['.notifications-count'],
 *     'clientOptions' => [
 *         'size' => 'large',
 *     ],
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
     * Use Notie
     * @see https://jaredreich.com/projects/notie/
     */
    const THEME_NOTIE = 'notie';
    /**
     * Use NotifIt!
     * @see http://naoxink.hol.es/notifIt/
     */
    const THEME_NOTIFIT = 'notifit';
    /**
     * Use Notify
     * @see https://notifyjs.com/
     */
    const THEME_NOTIFY = 'notify';
    /**
     * Use Pnotify
     * @see http://sciactive.com/pnotify/
     */
    const THEME_PNOTIFY = 'pnotify';
    /**
     * Use Toastr
     * @see https://github.com/CodeSeven/toastr
     */
    const THEME_TOASTR = 'toastr';

    /**
     * @var array additional options to be passed to the notification library.
     * Please refer to the plugin project page for available options.
     */
    public $clientOptions = [];

    /**
     * @var string the library name to be used for notifications
     * One of the THEME_XXX constants
     */
    public $theme = null;

    /**
     * @var integer The time to leave the notification shown on screen
     */
    public $delay = 5000;

    /**
     * @var integer the XHR timeout in milliseconds
     */
    public $xhrTimeout = 2000;

    /**
     * @var integer The delay between pulls
     */
    public $pollInterval = 5000;

    /**
     * @var boolean Whether to show already seen notifications
     */
    public $pollSeen = false;

    /**
     * @var array An array of jQuery selector to be updated with the current
     *            notifications count
     */
    public $counters = [];

    /**
     * @var string The locale to be used for jQuery timeago. Defaults to the
     *             current Yii language
     */
    public $timeAgoLocale;

    /**
     * @var string The jQuery selector in which the notifications list should
     *             be rendered
     */
    public $listSelector = null;

    /**
     * @var string The list item HTML template
     */
    public $listItemTemplate = null;

    /**
     * @var string The list item before render callback
     */
    public $listItemBeforeRender = null;

    /**
     * @var array List of built in themes
     */
    protected static $_builtinThemes = [
        self::THEME_GROWL,
        self::THEME_NOTY,
        self::THEME_NOTIFY,
        self::THEME_NOTIE,
        self::THEME_PNOTIFY,
        self::THEME_TOASTR,
        self::THEME_NOTIFIT
    ];

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!isset($this->timeAgoLocale)) {
            $this->timeAgoLocale = Yii::$app->language;
        }
        $this->registerAssets();
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $view = $this->getView();

        $asset = NotificationsAsset::register($view);

        // Register the theme assets
        if (!is_null($this->theme)) {
            if (!in_array($this->theme, self::$_builtinThemes)) {
                throw new Exception("Unknown theme: " . $this->theme, 501);
            }
            foreach (['js' => 'registerJsFile', 'css' => 'registerCssFile'] as $type => $method) {
                $filename = NotificationsAsset::getFilename($this->theme, $type);
                if ($filename) {
                    $view->$method($asset->baseUrl . '/' . $filename, [
                        'depends' => NotificationsAsset::className()
                    ]);
                }
            }
        }

        // Register timeago i18n file
        if ($filename = NotificationsAsset::getTimeAgoI18n($this->timeAgoLocale)) {
            $view->registerJsFile($asset->baseUrl . '/' . $filename, [
                'depends' => NotificationsAsset::className()
            ]);
        }

        $params = [
            'url' => Url::to(['/notifications/notifications/poll']),
            'xhrTimeout' => Html::encode($this->xhrTimeout),
            'delay' => Html::encode($this->delay),
            'options' => $this->clientOptions,
            'pollSeen' => !!$this->pollSeen,
            'pollInterval' => Html::encode($this->pollInterval),
            'counters' => $this->counters,
        ];

        if ($this->theme) {
            $params['theme'] = Html::encode($this->theme);
        }

        if ($this->listSelector) {
            $params['seenUrl'] = Url::to(['/notifications/notifications/read']);
            $params['deleteUrl'] = Url::to(['/notifications/notifications/delete']);
            $params['listSelector'] = $this->listSelector;
            if ($this->listItemTemplate) {
                $params['listItemTemplate'] = $this->listItemTemplate;
            }
            if ($this->listItemBeforeRender instanceof JsExpression) {
                $params['listItemBeforeRender'] = $this->listItemBeforeRender;
            }
        }

        $js = 'Notifications(' . Json::encode($params) . ');';

        $view->registerJs($js);
    }

}
