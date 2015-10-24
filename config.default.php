<?php

/*******************************************************************/
/* Rename this file to config.php if you want to change the values */
/*******************************************************************/

// Enable/Disable debug
define('DEBUG', false);

// Debug file path
define('DEBUG_FILE', __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'debug.log');

// Plugins directory
define('PLUGINS_DIR', 'data'.DIRECTORY_SEPARATOR.'plugins');

// Folder for uploaded files
define('FILES_DIR', 'data'.DIRECTORY_SEPARATOR.'files');

// E-mail address for the "From" header (notifications)
define('MAIL_FROM', 'notifications@kanboard.local');

// Mail transport available: "smtp", "sendmail", "mail" (PHP mail function), "postmark", "mailgun", "sendgrid"
define('MAIL_TRANSPORT', 'mail');

// SMTP configuration to use when the "smtp" transport is chosen
define('MAIL_SMTP_HOSTNAME', '');
define('MAIL_SMTP_PORT', 25);
define('MAIL_SMTP_USERNAME', '');
define('MAIL_SMTP_PASSWORD', '');
define('MAIL_SMTP_ENCRYPTION', null); // Valid values are "null", "ssl" or "tls"

// Sendmail command to use when the transport is "sendmail"
define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');

// Postmark API token (used to send emails through their API)
define('POSTMARK_API_TOKEN', '');

// Mailgun API key (used to send emails through their API)
define('MAILGUN_API_TOKEN', '');

// Mailgun domain name
define('MAILGUN_DOMAIN', '');

// Sendgrid API configuration
define('SENDGRID_API_USER', '');
define('SENDGRID_API_KEY', '');

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

// LDAP bind type: "anonymous", "user" or "proxy"
define('LDAP_BIND_TYPE', 'anonymous');

// LDAP username to use with proxy mode
// LDAP username pattern to use with user mode
define('LDAP_USERNAME', null);

// LDAP password to use for proxy mode
define('LDAP_PASSWORD', null);

// LDAP account base, i.e. root of all user account
// Example: ou=People,dc=example,dc=com
define('LDAP_ACCOUNT_BASE', '');

// LDAP query pattern to use when searching for a user account
// Example for ActiveDirectory: '(&(objectClass=user)(sAMAccountName=%s))'
// Example for OpenLDAP: 'uid=%s'
define('LDAP_USER_PATTERN', '');

// Name of an attribute of the user account object which should be used as the full name of the user
define('LDAP_ACCOUNT_FULLNAME', 'displayname');

// Name of an attribute of the user account object which should be used as the email of the user
define('LDAP_ACCOUNT_EMAIL', 'mail');

// Name of an attribute of the user account object which should be used as the id of the user. (optional)
// Example for ActiveDirectory: 'samaccountname'
// Example for OpenLDAP: 'uid'
define('LDAP_ACCOUNT_ID', '');

// LDAP Attribute for group membership
define('LDAP_ACCOUNT_MEMBEROF', 'memberof');

// DN for administrators
// Example: CN=Kanboard Admins,CN=Users,DC=kanboard,DC=local
define('LDAP_GROUP_ADMIN_DN', '');

// DN for project administrators
// Example: CN=Kanboard Project Admins,CN=Users,DC=kanboard,DC=local
define('LDAP_GROUP_PROJECT_ADMIN_DN', '');

// By default Kanboard lowercase the ldap username to avoid duplicate users (the database is case sensitive)
// Set to true if you want to preserve the case
define('LDAP_USERNAME_CASE_SENSITIVE', false);

// Automatically create user account
define('LDAP_ACCOUNT_CREATION', true);

// Enable/disable Google authentication
define('GOOGLE_AUTH', false);

// Google client id (Get this value from the Google developer console)
define('GOOGLE_CLIENT_ID', '');

// Google client secret key (Get this value from the Google developer console)
define('GOOGLE_CLIENT_SECRET', '');

// Enable/disable GitHub authentication
define('GITHUB_AUTH', false);

// GitHub client id (Copy it from your settings -> Applications -> Developer applications)
define('GITHUB_CLIENT_ID', '');

// GitHub client secret key (Copy it from your settings -> Applications -> Developer applications)
define('GITHUB_CLIENT_SECRET', '');

// Github oauth2 authorize url
define('GITHUB_OAUTH_AUTHORIZE_URL', 'https://github.com/login/oauth/authorize');

// Github oauth2 token url
define('GITHUB_OAUTH_TOKEN_URL', 'https://github.com/login/oauth/access_token');

// Github API url (don't forget the trailing slash)
define('GITHUB_API_URL', 'https://api.github.com/');

// Enable/disable Gitlab authentication
define('GITLAB_AUTH', false);

// Gitlab application id
define('GITLAB_CLIENT_ID', '');

// Gitlab application secret
define('GITLAB_CLIENT_SECRET', '');

// Gitlab oauth2 authorize url
define('GITLAB_OAUTH_AUTHORIZE_URL', 'https://gitlab.com/oauth/authorize');

// Gitlab oauth2 token url
define('GITLAB_OAUTH_TOKEN_URL', 'https://gitlab.com/oauth/token');

// Gitlab API url endpoint (don't forget the trailing slash)
define('GITLAB_API_URL', 'https://gitlab.com/api/v3/');

// Enable/disable the reverse proxy authentication
define('REVERSE_PROXY_AUTH', false);

// Header name to use for the username
define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');

// Username of the admin, by default blank
define('REVERSE_PROXY_DEFAULT_ADMIN', '');

// Default domain to use for setting the email address
define('REVERSE_PROXY_DEFAULT_DOMAIN', '');

// Enable/disable remember me authentication
define('REMEMBER_ME_AUTH', true);

// Enable or disable "Strict-Transport-Security" HTTP header
define('ENABLE_HSTS', true);

// Enable or disable "X-Frame-Options: DENY" HTTP header
define('ENABLE_XFRAME', true);

// Enable syslog logging
define('ENABLE_SYSLOG', true);

// Escape html inside markdown text
define('MARKDOWN_ESCAPE_HTML', true);

// API alternative authentication header, the default is HTTP Basic Authentication defined in RFC2617
define('API_AUTHENTICATION_HEADER', '');

// Enable/disable url rewrite
define('ENABLE_URL_REWRITE', false);

// Hide login form, useful if all your users use Google/Github/ReverseProxy authentication
define('HIDE_LOGIN_FORM', false);

// Enable captcha after 3 authentication failure
define('BRUTEFORCE_CAPTCHA', 3);

// Lock the account after 6 authentication failure
define('BRUTEFORCE_LOCKDOWN', 6);

// Lock account duration in minute
define('BRUTEFORCE_LOCKDOWN_DURATION', 15);

// Session duration in second (0 = until the browser is closed)
// See http://php.net/manual/en/session.configuration.php#ini.session.cookie-lifetime
define('SESSION_DURATION', 0);

// HTTP client proxy
define('HTTP_PROXY_HOSTNAME', '');
define('HTTP_PROXY_PORT', '3128');
define('HTTP_PROXY_USERNAME', '');
define('HTTP_PROXY_PASSWORD', '');
