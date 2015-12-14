Config file
===========

You can customize the default settings of Kanboard by adding a file `config.php` at the project root or in the `data` folder.
You can also rename the file `config.default.php` to `config.php` and change the desired values.

Enable/Disable debug mode
-------------------------

```php
define('DEBUG', false);
```

The debug mode logs all SQL queries and the time taken to generate pages.

Debug file path
---------------

```php
define('DEBUG_FILE', __DIR__.'/data/debug.log');
```

All debug information are saved in this file.
If you prefer to send logs to `stdout` or `stderr` replace the value by `php://stdout` or `php://stderr`.

Plugins folder
--------------

```php
// Plugin directory
define('PLUGINS_DIR', 'data/plugins');
```

Folder for uploaded files
-------------------------

```php
define('FILES_DIR', 'data/files');
```

Enable/disable url rewrite
--------------------------

```php
define('ENABLE_URL_REWRITE', false);
```

Email configuration
-------------------

```php
// E-mail address for the "From" header (notifications)
define('MAIL_FROM', 'notifications@kanboard.local');

// Mail transport to use: "smtp", "sendmail" or "mail" (PHP mail function)
define('MAIL_TRANSPORT', 'mail');

// SMTP configuration to use when the "smtp" transport is chosen
define('MAIL_SMTP_HOSTNAME', '');
define('MAIL_SMTP_PORT', 25);
define('MAIL_SMTP_USERNAME', '');
define('MAIL_SMTP_PASSWORD', '');
define('MAIL_SMTP_ENCRYPTION', null); // Valid values are "null", "ssl" or "tls"

// Sendmail command to use when the transport is "sendmail"
define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');
```

Database settings
-----------------

```php
// Database driver: sqlite, mysql or postgres (sqlite by default)
define('DB_DRIVER', 'sqlite');

// Mysql/Postgres username
define('DB_USERNAME', 'root');

// Mysql/Postgres password
define('DB_PASSWORD', '');

// Mysql/Postgres hostname
define('DB_HOSTNAME', 'localhost');

// Mysql/Postgres database name
define('DB_NAME', 'kanboard');

// Mysql/Postgres custom port (null = default port)
define('DB_PORT', null);
```

LDAP settings
-------------

```php
// Enable LDAP authentication (false by default)
define('LDAP_AUTH', false);

// LDAP server hostname
define('LDAP_SERVER', '');

// LDAP server port (389 by default)
define('LDAP_PORT', 389);

// By default, require certificate to be verified for ldaps:// style URL. Set to false to skip the verification
define('LDAP_SSL_VERIFY', true);

// Enable LDAP START_TLS
define('LDAP_START_TLS', false);

// By default Kanboard lowercase the ldap username to avoid duplicate users (the database is case sensitive)
// Set to true if you want to preserve the case
define('LDAP_USERNAME_CASE_SENSITIVE', false);

// LDAP bind type: "anonymous", "user" or "proxy"
define('LDAP_BIND_TYPE', 'anonymous');

// LDAP username to use with proxy mode
// LDAP username pattern to use with user mode
define('LDAP_USERNAME', null);

// LDAP password to use for proxy mode
define('LDAP_PASSWORD', null);

// LDAP DN for users
// Example for ActiveDirectory: CN=Users,DC=kanboard,DC=local
// Example for OpenLDAP: ou=People,dc=example,dc=com
define('LDAP_USER_BASE_DN', '');

// LDAP pattern to use when searching for a user account
// Example for ActiveDirectory: '(&(objectClass=user)(sAMAccountName=%s))'
// Example for OpenLDAP: 'uid=%s'
define('LDAP_USER_FILTER', '');

// LDAP attribute for username
// Example for ActiveDirectory: 'samaccountname'
// Example for OpenLDAP: 'uid'
define('LDAP_USER_ATTRIBUTE_USERNAME', 'uid');

// LDAP attribute for user full name
// Example for ActiveDirectory: 'displayname'
// Example for OpenLDAP: 'cn'
define('LDAP_USER_ATTRIBUTE_FULLNAME', 'cn');

// LDAP attribute for user email
define('LDAP_USER_ATTRIBUTE_EMAIL', 'mail');

// LDAP attribute to find groups in user profile
define('LDAP_USER_ATTRIBUTE_GROUPS', 'memberof');

// Allow automatic LDAP user creation
define('LDAP_USER_CREATION', true);

// LDAP DN for administrators
// Example: CN=Kanboard-Admins,CN=Users,DC=kanboard,DC=local
define('LDAP_GROUP_ADMIN_DN', '');

// LDAP DN for managers
// Example: CN=Kanboard Managers,CN=Users,DC=kanboard,DC=local
define('LDAP_GROUP_MANAGER_DN', '');

// Enable LDAP group provider for project permissions
// The end-user will be able to browse LDAP groups from the user interface and allow access to specified projects
define('LDAP_GROUP_PROVIDER', false);

// LDAP Base DN for groups
define('LDAP_GROUP_BASE_DN', '');

// LDAP group filter
// Example for ActiveDirectory: (&(objectClass=group)(sAMAccountName=%s*))
define('LDAP_GROUP_FILTER', '');

// LDAP attribute for the group name
define('LDAP_GROUP_ATTRIBUTE_NAME', 'cn');
```

Google Authentication settings
------------------------------

```php
// Enable/disable Google authentication
define('GOOGLE_AUTH', false);

// Google client id (Get this value from the Google developer console)
define('GOOGLE_CLIENT_ID', '');

// Google client secret key (Get this value from the Google developer console)
define('GOOGLE_CLIENT_SECRET', '');
```

Github Authentication settings
------------------------------

```php
// Enable/disable GitHub authentication
define('GITHUB_AUTH', false);

// GitHub client id (Copy it from your settings -> Applications -> Developer applications)
define('GITHUB_CLIENT_ID', '');

// GitHub client secret key (Copy it from your settings -> Applications -> Developer applications)
define('GITHUB_CLIENT_SECRET', '');
```

Reverse-Proxy Authentication settings
-------------------------------------

```php
// Enable/disable the reverse proxy authentication
define('REVERSE_PROXY_AUTH', false);

// Header name to use for the username
define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');

// Username of the admin, by default blank
define('REVERSE_PROXY_DEFAULT_ADMIN', '');

// Default domain to use for setting the email address
define('REVERSE_PROXY_DEFAULT_DOMAIN', '');
```

RememberMe Authentication settings
----------------------------------

```php
// Enable/disable remember me authentication
define('REMEMBER_ME_AUTH', true);
```

Secure HTTP headers settings
----------------------------

```php
// Enable or disable "Strict-Transport-Security" HTTP header
define('ENABLE_HSTS', true);

// Enable or disable "X-Frame-Options: DENY" HTTP header
define('ENABLE_XFRAME', true);
```

Logging
-------

```php
// Enable syslog logging
// Set to false to disable syslog
define('ENABLE_SYSLOG', true);
```

Bruteforce protection
---------------------

```php
// Enable captcha after 3 authentication failure
define('BRUTEFORCE_CAPTCHA', 3);

// Lock the account after 6 authentication failure
define('BRUTEFORCE_LOCKDOWN', 6);

// Lock account duration in minute
define('BRUTEFORCE_LOCKDOWN_DURATION', 15);
```

Session
-------

```php
// Session duration in second (0 = until the browser is closed)
// See http://php.net/manual/en/session.configuration.php#ini.session.cookie-lifetime
define('SESSION_DURATION', 0);
```

HTTP client proxy
-----------------

If external HTTP requests need to be sent through a proxy:

```php
define('HTTP_PROXY_HOSTNAME', '');
define('HTTP_PROXY_PORT', '3128');
define('HTTP_PROXY_USERNAME', '');
define('HTTP_PROXY_PASSWORD', '');
```

Various settings
----------------

```php
// Escape html inside markdown text
define('MARKDOWN_ESCAPE_HTML', true);

// API alternative authentication header, the default is HTTP Basic Authentication defined in RFC2617
define('API_AUTHENTICATION_HEADER', '');

// Hide login form, useful if all your users use Google/Github/ReverseProxy authentication
define('HIDE_LOGIN_FORM', false);

// Override API token stored in the database, useful for automated tests
define('API_AUTHENTICATION_TOKEN', 'My unique API Token');
```
