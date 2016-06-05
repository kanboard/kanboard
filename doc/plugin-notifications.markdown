Add Notification Types with Plugins
===================================

You can send notifications to almost any system by adding a new type.
There are two kinds of notifications: project and user.

- Project: Notifications configured at the project level
- User: Notifications sent individually and configured at the user profile

Register a new notification type
--------------------------------

In your plugin registration file call the method `setType()`:

```php
$this->userNotificationTypeModel->setType('irc', t('IRC'), '\Kanboard\Plugin\IRC\Notification\IrcHandler');
$this->projectNotificationTypeModel->setType('irc', t('IRC'), '\Kanboard\Plugin\IRC\Notification\IrcHandler');
```

Your handler can be registered for user or project notification. You don't necessarily need to support both.

When your handler is registered, the end-user can choose to receive the new notification type or not.

Notification Handler
--------------------

Your notification handler must implement the interface `Kanboard\Core\Notification\NotificationInterface`:

```php
interface NotificationInterface
{
    /**
     * Send notification to a user
     *
     * @access public
     * @param  array     $user
     * @param  string    $event_name
     * @param  array     $event_data
     */
    public function notifyUser(array $user, $event_name, array $event_data);

    /**
     * Send notification to a project
     *
     * @access public
     * @param  array     $project
     * @param  string    $event_name
     * @param  array     $event_data
     */
    public function notifyProject(array $project, $event_name, array $event_data);
}
```

Example of notification plugins
-------------------------------

- [Slack](https://github.com/kanboard/plugin-slack)
- [Hipchat](https://github.com/kanboard/plugin-hipchat)
- [Jabber](https://github.com/kanboard/plugin-jabber)

