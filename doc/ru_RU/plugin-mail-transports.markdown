Plugin: Add Mail Transport[¶](#plugin-add-mail-transport "Ссылка на этот заголовок")

====================================================================================



By default Kanboard supports 3 standards mail transports:



-   Mail (PHP mail function)

-   Smtp

-   Sendmail command



With the plugin API you can add a driver for any email provider. For example, your plugin can add a mail transport for a provider that uses an HTTP API.



Implementation[¶](#implementation "Ссылка на этот заголовок")

-------------------------------------------------------------



Your plugin must implement the interface `Kanboard\Core\Mail\ClientInterface`{.docutils .literal} and extends from `Kanboard\Core\Base`{.docutils .literal}.



The only method you need to implement is `sendEmail()`{.docutils .literal}:



    interface ClientInterface

    {

        /**

         * Send a HTML email

         *

         * @access public

         * @param  string  $email

         * @param  string  $name

         * @param  string  $subject

         * @param  string  $html

         * @param  string  $author

         */

        public function sendEmail($email, $name, $subject, $html, $author);

    }



To register your new mail transport, use the method `setTransport($transport, $class)`{.docutils .literal} from the class `Kanboard\Core\Mail\Client`{.docutils .literal}:



    $this->emailClient->setTransport('myprovider', '\Kanboard\Plugin\MyProvider\MyEmailHandler');



The second argument contains the absolute name space of your concrete class.



Examples of mail transport plugins[¶](#examples-of-mail-transport-plugins "Ссылка на этот заголовок")

-----------------------------------------------------------------------------------------------------



-   [Sendgrid](https://github.com/kanboard/plugin-sendgrid)

-   [Mailgun](https://github.com/kanboard/plugin-mailgun)

-   [Postmark](https://github.com/kanboard/plugin-postmark)



### [Оглавление](index.markdown)



-   [Plugin: Add Mail Transport](#)

    -   [Implementation](#implementation)

    -   [Examples of mail transport plugins](#examples-of-mail-transport-plugins)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

