Email configuration
===================

User settings
-------------

To receive email notifications, users of Kanboard must have:

- Activated notifications in their profile
- Have a valid email address in their profile
- Be member of the project that will trigger notifications

Note: The logged user who performs the action doesn't receive any notifications, only other project members.

Email transports
----------------

There are several email transports available:

- SMTP
- Sendmail
- PHP native mail function
- Mailgun
- Postmark
- Sendgrid

Server settings
---------------

By default, Kanboard will use the bundled PHP mail function to send emails.
Usually that require no configuration if your server can already send emails.

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

### Mailgun HTTP API

You can use the HTTP API of Mailgun to send emails.

Configuration:

```php
// We choose "mailgun" as mail transport
define('MAIL_TRANSPORT', 'mailgun');

// Mailgun API key
define('MAILGUN_API_TOKEN', 'YOUR_API_KEY');

// Mailgun domain name
define('MAILGUN_DOMAIN', 'YOUR_DOMAIN_CONFIGURED_IN_MAILGUN');

// Be sure to use the sender email address configured in Mailgun
define('MAIL_FROM', 'sender-address-configured-in-mailgun@example.org');
```

### Postmark HTTP API

Postmark is a third-party email service.
If you already use the Postmark integration to receive emails in Kanboard you can use the same provider to send email too.

This system use their HTTP API instead of the SMTP protocol.

Here are the required settings for this configuration:

```php
// We choose "postmark" as mail transport
define('MAIL_TRANSPORT', 'postmark');

// Copy and paste your Postmark API token
define('POSTMARK_API_TOKEN', 'COPY HERE YOUR POSTMARK API TOKEN');

// Be sure to use the Postmark configured sender email address
define('MAIL_FROM', 'sender-address-configured-in-postmark@example.org');
```

### Sendgrid HTTP API

You can use the HTTP API of Sendgrid to send emails.

Configuration:

```php
// We choose "sendgrid" as mail transport
define('MAIL_TRANSPORT', 'sendgrid');

// Sendgrid username
define('SENDGRID_API_USER', 'YOUR_SENDGRID_USERNAME');

// Sendgrid password
define('SENDGRID_API_KEY', 'YOUR_SENDGRID_PASSWORD');
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

To do that, you have to specify the URL of your Kanboard installation in your [Application Settings](http://kanboard.net/documentation/application-configuration).
By default, nothing is defined, so no links will be displayed.

Examples:

- http://demo.kanboard.net/
- http://myserver/kanboard/
- http://kanboard.mydomain.com/

Don't forget the ending slash `/`.

You need to define that manually because Kanboard cannot guess the URL from a command line script and some people have very specific configuration.

Troubleshooting
---------------

If no emails are send and you are sure that everything is configured correctly:

- Check your spam folder
- Enable the debug mode and check the debug file `data/debug.log`, you should see the exact error
- Be sure that your server or your hosting provider allow you to send emails
- If you use SeLinux, allow PHP to send emails
