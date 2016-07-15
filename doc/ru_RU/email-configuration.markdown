Настройка email
===============


Настройки пользователя[¶](#user-settings "Ссылка на этот заголовок")
--------------------------------------------------------------------

Для получение уведомлений на email, пользователи Канборда должны иметь:

-   Включенные уведомления, должны быть включены в профиле пользователя
-   Должен быть прописан правильный email адрес в профиле пользователя
-   Быть участником проекта, который отсылает уведомления


Примечание: Пользователь, выполнивший вход в Канборд и выполняющий действие, не будет получать уведомления. Уведомления будут получать только другие участники проекта.



Email шлюзы[¶](#email-transports "Ссылка на этот заголовок")
------------------------------------------------------------

В Канборд доступны несколько шлюзов для email:

-   SMTP
-   Sendmail
-   Встроенная mail функция PHP
-   Другие методы могут предоставить внешние плагины: Postmark, Sendgrid and Mailgun


Настройки сервера[¶](#server-settings "Ссылка на этот заголовок")
-----------------------------------------------------------------

По умолчанию, Канборд использует встроенную в PHP функцию для передачи email сообщений. Обычно не требуется дополнительных настроек, если ваш сервер уже может отправлять email сообщения.

Если вы захотите использовать другие методы: SMTP протокол и Sendmail, то ниже приведены инструкции по настройке.

### Настройка SMTP[¶](#smtp-configuration "Ссылка на этот заголовок")

Переименуйте файл `config.default.php`{.docutils .literal} в `config.php`{.docutils .literal} и измените следующие значения:


    // We choose "smtp" as mail transport

    define('MAIL_TRANSPORT', 'smtp');



    // We define our server settings

    define('MAIL_SMTP_HOSTNAME', 'mail.example.com');

    define('MAIL_SMTP_PORT', 25);



    // Credentials for authentication on the SMTP server (not mandatory)

    define('MAIL_SMTP_USERNAME', 'username');

    define('MAIL_SMTP_PASSWORD', 'super password');



Возможно понадобится использовать шифрованное подключение TLS или SSL:


    define('MAIL_SMTP_ENCRYPTION', 'ssl'); // Valid values are "null", "ssl" or "tls"


### Настройка Sendmail[¶](#sendmail-configuration "Ссылка на этот заголовок")

По умолчанию команда отправки сообщений выглядит так `/usr/sbin/sendmail -bs`{.docutils .literal}, но вы можете изменить ее в файле конфигурации.

Например:



    // We choose "sendmail" as mail transport

    define('MAIL_TRANSPORT', 'sendmail');



    // If you need to change the sendmail command, replace the value

    define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');



### Встроенная mail функция PHP[¶](#php-native-mail-function "Ссылка на этот заголовок")

Это конфигурация по умолчанию:



    define('MAIL_TRANSPORT', 'mail');



### Email адрес отправителя[¶](#the-sender-email-address "Ссылка на этот заголовок")

По умолчанию, сообщения отправляются с адресом отправителя `notifications@kanboard.local`{.docutils .literal}. На этот адрес нельзя ответить.

Вы можете настроить этот адрес изменив значение константы `MAIL_FROM`{.docutils .literal} в вашем конфигурационном файле.


    define('MAIL_FROM', 'kanboard@mydomain.tld');


Это может быть полезным, если ваш SMTP сервер не принимает неправильные адреса.


### Как отобразить ссылку на задачу в уведомлении?[¶](#how-to-display-a-link-to-the-task-in-notifications "Ссылка на этот заголовок")

Чтобы сделать это, вы должны указать URL вашего установленного Канборда в [Настройках приложения](application-configuration.markdown).

Например:



-   [http://demo.kanboard.ru/](http://demo.kanboard.ru/)



-   <http:/>/имясервера/kanboard/



-   [http://kanboard.mydomain.com/](http://kanboard.mydomain.com/)



Не забудьте добавить в конце слеш `/`{.docutils .literal}.



Вы должны сделать это вручную, потому что Канборд не может угадать URL из скрипта командной строки и некоторые конфигурации веб серверов очень специфичны.


Решение проблем[¶](#troubleshooting "Ссылка на этот заголовок")
---------------------------------------------------------------

Если email сообщения не отправляются и вы уверены, что все настроили правильно:

-   Проверьте папку Спам
-   Включите режим отладки и посмотрите отладочный файл `data/debug.log`{.docutils .literal}, вы можете увидеть конкретную ошибку
-   Убедитесь, что ваш сервер или ваш хостинг провайдер позволяет вам отсылать email сообщения
-   Если вы используете SeLinux, разрешите PHP отсылать email сообщения.


 



[Русская документация Kanboard](http://kanboard.ru/doc/)

