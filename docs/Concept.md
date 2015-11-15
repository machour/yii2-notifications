
Concepts
--------

Usually, a notification displayed to the end user (on your web interface, or a push message), is made of a title (New
friendship request), a description (Foo Bar wants to be your friend), and a action url (/friends/add/FOOBAR_ID).
 
Notifications texts often needs to be translated, changed based on the current time or other parameters. The approach
taken by this module is to compute the notification title, description and url at run time, in order to give you a good
flexibility.

A notification may also be tied to a foreign object. In our friend ship request above, the foreign object will be the 
record representing the user Foo Bar.

This module represents a notification in the database using the following structure:

| Field      | Description                                                                           | 
| ---------- | ------------------------------------------------------------------------------------- |
| id         | The unique ID for the notification                                                    |
| type       | The notification severity (can be of `notification`, `success`, `error` or `warning`) |
| key        | The notification key (You decide here, for example: new_message, canceled_event)      |
| key_id     | The foreign object id, tied to your notification key. Defaults to NULL.               |
| user_id    | The notified user id                                                                  |
| seen       | Is the notification seen by the user or not                                           |
| created_at | Notification creation date                                                            |

See the *Declaring your notifications* in the **Usage** section to see how to declare your available notifications types
and dynamically compute their data.

