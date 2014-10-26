Email configuration
===================

User settings
-------------

To receive email notifications, users of Kanboard must have:

- Activated notifications in the settings page
- Have a valid email address in their profile

Server settings
---------------

By default, Kanboard will use the bundled PHP mail function to send emails.
Usually that require no configuration if your server can already send emails.

However, it's possible to use other methods, the SMTP protocol and Sendmail.

### SMTP configuration

Create a blank `config.php` file or use the template `config.default.php` and set those values:

```php
// We choose "smtp" as mail transport
define('MAIL_TRANSPORT', 'smtp');

// We define our server settings
define('MAIL_SMTP_HOSTNAME', 'mail.example.com');
define('MAIL_SMTP_PORT', 25);

// Credentials for authentication on the SMTP server (not mandatory)
define('MAIL_SMTP_USERNAME', 'username');
define('MAIL_SMTP_PASSWORD', 'super password');
```

It's also possible to use a secure connection, TLS or SSL:

```php
define('MAIL_SMTP_ENCRYPTION', 'ssl'); // Valid values are "null", "ssl" or "tls"
```

Here an example with Google:

```php
define('MAIL_SMTP_HOSTNAME', 'smtp.gmail.com');
define('MAIL_SMTP_PORT', 465);
define('MAIL_SMTP_USERNAME', 'my_account@gmail.com');
define('MAIL_SMTP_PASSWORD', 'my google password');
define('MAIL_SMTP_ENCRYPTION', 'ssl');
```

To use Google, you might need to allow Kanboard to use your Google account, see ["Allowing less secure apps to access your account"](https://support.google.com/accounts/answer/6010255) and ["My client isn't accepting my username and password"](https://support.google.com/mail/answer/14257).

### Sendmail configuration

By default the sendmail command will be `/usr/sbin/sendmail -bs` but you can customize that in your config file.

Example:

```php
// We choose "sendmail" as mail transport
define('MAIL_TRANSPORT', 'sendmail');

// If you need to change the sendmail command, replace the value
define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');
```

### The sender email address

By default, emails will use the sender address `notifications@kanboard.net`.
It's not possible to reply to this address.

You can customize this address by changing the value of the constant `MAIL_FROM` in your config file.

```php
define('MAIL_FROM', 'notifications@kanboard.net');
```

That can be useful if your SMTP server configuration doesn't accept the default address.


### Check for due tasks

Every day, Kanboard can check for due tasks, to do that you have to setup a cronjob on your server and use the Kanboard command line interface.

Here a example:

```bash
# Everyday at 8am we check for due tasks
0 8 * * *  cd /path/to/kanboard && ./kanboard send-notifications-due-tasks >/dev/null 2>&1
```

### How to display a link to the task in notifications?

To do that, you have to specify the URL of your Kanboard installation in your [Application Settings](http://kanboard.net/documentation/application-configuration).
By default, nothing is defined, so no links will be displayed.

Examples:

- http://demo.kanboard.net/
- http://myserver/kanboard/
- http://kanboard.mydomain.com/

Don't forget the ending `/`.

You need to define that manually because Kanboard can't guess the URL from a command line script and some people have very specific configuration.
