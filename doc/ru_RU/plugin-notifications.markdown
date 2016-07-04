Add Notification Types with Plugins[¶](#add-notification-types-with-plugins "Ссылка на этот заголовок")

=======================================================================================================



You can send notifications to almost any system by adding a new type. There are two kinds of notifications: project and user.



-   Project: Notifications configured at the project level

-   User: Notifications sent individually and configured at the user profile



Register a new notification type[¶](#register-a-new-notification-type "Ссылка на этот заголовок")

-------------------------------------------------------------------------------------------------



In your plugin registration file call the method `setType()`{.docutils .literal}:



    $this->userNotificationType->setType('irc', t('IRC'), '\Kanboard\Plugin\IRC\Notification\IrcHandler');

    $this->projectNotificationType->setType('irc', t('IRC'), '\Kanboard\Plugin\IRC\Notification\IrcHandler');



Your handler can be registered for user or project notification. You don’t necessarily need to support both.



When your handler is registered, the end-user can choose to receive the new notification type or not.



Notification Handler[¶](#notification-handler "Ссылка на этот заголовок")

-------------------------------------------------------------------------



Your notification handler must implement the interface `Kanboard\Notification\NotificationInterface`{.docutils .literal}:



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



Example of notification plugins[¶](#example-of-notification-plugins "Ссылка на этот заголовок")

-----------------------------------------------------------------------------------------------



-   [Slack](https://github.com/kanboard/plugin-slack)

-   [Hipchat](https://github.com/kanboard/plugin-hipchat)

-   [Jabber](https://github.com/kanboard/plugin-jabber)



### [Оглавление](index.markdown)



-   [Add Notification Types with Plugins](#)

    -   [Register a new notification type](#register-a-new-notification-type)

    -   [Notification Handler](#notification-handler)

    -   [Example of notification plugins](#example-of-notification-plugins)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

