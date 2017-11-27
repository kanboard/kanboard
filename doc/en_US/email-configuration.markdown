Email configuration
===================

User settings
-------------

To receive email notifications, users of Kanboard must have:

- Activated notifications in their profile
- Have a valid email address in their profile
- Be a member of the project that will trigger notifications

Note: The logged user who performs the action doesn't receive any notifications, only other project members.

Email transports
----------------

There are several email transports available:

- SMTP
- Sendmail
- PHP native mail function
- Other methods can be provided by external plugins: Postmark, Sendgrid and Mailgun

Server settings
---------------

By default, Kanboard will use the bundled PHP mail function to send emails.
Usually that requires no configuration if your server can already send emails.

However, it's possible to use other methods, the SMTP protocol and Sendmail.

### SMTP configuration

Rename the file `config.default.php` to `config.php` and change these values:

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

### Sendmail configuration

By default the sendmail command will be `/usr/sbin/sendmail -bs` but you can customize that in your config file.

Example:

```php
// We choose "sendmail" as mail transport
define('MAIL_TRANSPORT', 'sendmail');

// If you need to change the sendmail command, replace the value
define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');
```

### PHP native mail function

This is the default configuration:

```php
define('MAIL_TRANSPORT', 'mail');
```

### The sender email address

By default, emails will use the sender address `notifications@kanboard.local`.
It's not possible to reply to this address.

You can customize this address by changing the value of the constant `MAIL_FROM` in your config file.

```php
define('MAIL_FROM', 'kanboard@mydomain.tld');
```

That can be useful if your SMTP server configuration doesn't accept the default address.

### How to display a link to the task in notifications?

To do that, you have to specify the URL of your Kanboard installation in your [Application Settings](application-configuration.markdown).
By default, nothing is defined, so no links will be displayed.

Examples:

- http://myserver/kanboard/
- http://kanboard.mydomain.com/

Don't forget the ending slash `/`.

You need to define that manually because Kanboard cannot guess the URL from a command line script and some people have a very specific configuration.

Troubleshooting
---------------

If no emails are sent and you are sure that everything is configured correctly:

- Check your spam folder
- Enable the debug mode and check the debug file `data/debug.log`, you should see the exact error
- Be sure that your server or your hosting provider allows you to send emails
- If you use SeLinux, allow PHP to send emails
