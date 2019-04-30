Configuration
-------------

Before using this module, you have to run its migrations scripts:

```bash
./yii migrate/up --migrationPath=vendor/machour/yii2-notifications/migrations/
```

You also need to enable the module in Yii `modules` section of the application configuration file:
```php
return [
    // ...
    'modules' => [
        'notifications' => [
            'class' => 'machour\yii2\notifications\NotificationsModule',
            // Point this to your own Notification class
            // See the "Declaring your notifications" section below
            'notificationClass' => 'common\components\Notification',
            // Allow to have notification with same (user_id, key, key_id)
            // Default to FALSE
            'allowDuplicate' => false,
            // Allow custom date formatting in database
            'dbDateFormat' => 'Y-m-d H:i:s',
            // This callable should return your logged in user Id
            'userId' => function () {
                return \Yii::$app->user->id;
            },
        ],
        // your other modules ..
    ],
    // ...
]
```
### If UrlManager for url rewriting is active add these lines on your rules
```php
'urlManager' => [
    // ...
    'rules' => [
    // ...
    'notifications/poll' => '/notifications/notifications/poll',
    'notifications/rnr' => '/notifications/notifications/rnr',
    'notifications/read' => '/notifications/notifications/read',
    'notifications/read-all' => '/notifications/notifications/read-all',
    'notifications/delete-all' => '/notifications/notifications/delete-all',
    'notifications/delete' => '/notifications/notifications/delete',
    'notifications/flash' => '/notifications/notifications/flash',
    // ...
    ]
    // ...
]

```

### Declaring your notifications in common\components\Notification.php

```php

namespace common\components;

use Yii;
use common\models\Meeting;//example models
use common\models\Message;//example models
use machour\yii2\notifications\models\Notification as BaseNotification;

class Notification extends BaseNotification
{

    /**
     * A new message notification
     */
    const KEY_NEW_MESSAGE = 'new_message';
    /**
     * A meeting reminder notification
     */
    const KEY_MEETING_REMINDER = 'meeting_reminder';
    /**
     * No disk space left !
     */
    const KEY_NO_DISK_SPACE = 'no_disk_space';

    /**
     * @var array Holds all usable notifications
     */
    public static $keys = [
        self::KEY_NEW_MESSAGE,
        self::KEY_MEETING_REMINDER,
        self::KEY_NO_DISK_SPACE,
    ];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        switch ($this->key) {
            case self::KEY_MEETING_REMINDER:
                return Yii::t('app', 'Meeting reminder');

            case self::KEY_NEW_MESSAGE:
                return Yii::t('app', 'You got a new message');

            case self::KEY_NO_DISK_SPACE:
                return Yii::t('app', 'No disk space left');
        }
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        switch ($this->key) {
            case self::KEY_MEETING_REMINDER:
                $meeting = Meeting::findOne($this->key_id);
                return Yii::t('app', 'You are meeting with {customer}', [
                    'customer' => $meeting->customer->name
                ]);

            case self::KEY_NEW_MESSAGE:
                $message = Message::findOne($this->key_id);
                return Yii::t('app', '{customer} sent you a message', [
                    'customer' => $meeting->customer->name
                ]);

            case self::KEY_NO_DISK_SPACE:
                // We don't have a key_id here, simple message
                return 'Please buy more space immediately';
        }
    }

    /**
     * @inheritdoc
     */
    public function getRoute()
    {
        switch ($this->key) {
            case self::KEY_MEETING_REMINDER:
                return ['meeting', 'id' => $this->key_id];

            case self::KEY_NEW_MESSAGE:
                return ['message/read', 'id' => $this->key_id];

            case self::KEY_NO_DISK_SPACE:
                return 'https://aws.amazon.com/';//simple route on external link
        };
    }

}
```
