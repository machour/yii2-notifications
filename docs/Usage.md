Usage
-----

### Triggering a notification


```php

// $message was just created by the logged in user, and sent to $recipient_id
Notification::notify(Notification::KEY_NEW_MESSAGE, $recipient_id, $message->id);

// You may also use the following static methods to set the notification type:
Notification::warning(Notification::KEY_NEW_MESSAGE, $recipient_id, $message->id);
Notification::success(Notification::ORDER_PLACED, $admin_id, $order->id);
Notification::error(Notification::KEY_NO_DISK_SPACE, $admin_id);

```
          
### Listening and showing notifications in the UI

This package comes with a `NotificationsWidget` that is used to regularly poll the server for new
notifications.
 
The widget will then trigger visual notifications using the library selected with the `theme` option.
Here's an example using `NotificationsWidget::THEME_GROWL`:

![Growl notification](docs/growl.png)

The widget can also maintain a HTML list of notifications in your UI as well as updating one or more
notifications counters, if the `listSelector` option is defined.
Here's an example of a notification menu in the application header: 

![Notifications list](docs/list.png)

When clicked, a notification will be marked as seen, and the user will be redirected to the notification
route.

The samples images were generated using this code in my main layout file:
 
```php

NotificationsWidget::widget([
    'theme' => NotificationsWidget::THEME_GROWL,
    'clientOptions' => [
        'location' => 'br',
    ],
    'counters' => [
        '.notifications-header-count',
        '.notifications-icon-count'
    ],
    'listSelector' => '#notifications',
]);

?>

<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell-o"></i>
        <span class="label label-warning notifications-icon-count">0</span>
    </a>
    <ul class="dropdown-menu">
        <li class="header">You have <span class="notifications-header-count">0</span> notifications</li>
        <li>
            <div id="notifications"></div>
        </li>
    </ul>
</li>
```

| Parameter            | Description                                                                 | Default     |
| -------------------- | --------------------------------------------------------------------------- | -----------:|
| theme                | One of the THEME_XXX constants. See supported libraries for a full list     | null        |
| clientOptions        | An array of options to be passed to the underlying UI notifications library | []          |
| delay                | The time to leave the notification shown on screen                          | 5000        |
| pollInterval         | The delay in milliseconds between polls                                     | 5000        |
| pollSeen             | Whether to show already seen notifications                                  | false       |
| xhrTimeout           | The XHR request timeout in milliseconds                                     | 2000        |
| counters             | An array of jQuery selectors to update with the current notifications count | []          |
| listSelector         | A jQuery selector for your UI element that will holds the notification list | null        |
| listItemTemplate     | An optional template for the list item.                                     | built-in    |
| listItemBeforeRender | An optional callback to tweak the list item layout before rendering         | empty cb    |


Supported libraries
-------------------

Currently supported libraries are:

| Library        | Constant      | Shipped version | Project homepage                         |
| -------------- | ------------- | --------------- | ---------------------------------------- |
| jQuery Growl   | THEME_GROWL   | 1.3.1           | https://github.com/ksylvest/jquery-growl |
| Notify.js      | THEME_NOTIFY  | 0.3.4           | https://notifyjs.com/                    |
| Noty           | THEME_NOTY    | 2.3.7           | http://ned.im/noty/                      |
| PNotify        | THEME_PNOTIFY | 2.1             | http://sciactive.com/pnotify/            |
| Toastr         | THEME_TOASTR  | 1.2.2           | https://github.com/CodeSeven/toastr      |
| NotifIt!       | THEME_NOTIFIT | master          | http://naoxink.hol.es/notifIt/           |

If you'de like to add support for another notification UI library, edit the `assets/notifications.js` file
ad add a new entry into the `Notification.themes` property.

Your library must be added as an object with the `show` callback field defined and used to trigger the visual
notification, and an optional `types` translation map that will be used to translate natives types into the
used library notification type.

You will also need to add the library javascript file and optional CSS file to the `assets/themes/` directory,
and declare the new theme in `widgets/NotificationsWidget.php`.

Don't forget to send a patch afterwards!