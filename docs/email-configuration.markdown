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
