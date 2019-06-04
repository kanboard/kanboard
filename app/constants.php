<?php

// Root directory
define('ROOT_DIR', __DIR__.DIRECTORY_SEPARATOR.'..');

// App directory
define('APP_DIR', __DIR__);

// Data directory location
defined('DATA_DIR') or define('DATA_DIR', ROOT_DIR.DIRECTORY_SEPARATOR.'data');

// Files directory (attachments)
defined('FILES_DIR') or define('FILES_DIR', DATA_DIR.DIRECTORY_SEPARATOR.'files');

// Available cache drivers are "file" and "memory"
defined('CACHE_DRIVER') or define('CACHE_DRIVER', 'memory');

// Cache folder (file driver)
defined('CACHE_DIR') or define('CACHE_DIR', DATA_DIR.DIRECTORY_SEPARATOR.'cache');

// Plugins settings
defined('PLUGINS_DIR') or define('PLUGINS_DIR', ROOT_DIR.DIRECTORY_SEPARATOR.'plugins');
defined('PLUGIN_API_URL') or define('PLUGIN_API_URL', 'https://kanboard.org/plugins.json');
defined('PLUGIN_INSTALLER') or define('PLUGIN_INSTALLER', false); // Disabled by default for security reasons

// Enable/disable debug
defined('DEBUG') or define('DEBUG', strtolower(getenv('DEBUG')) === 'true');

// Logging drivers: syslog, stdout, stderr, system or file
defined('LOG_DRIVER') or define('LOG_DRIVER', 'system');

// Logging file
defined('LOG_FILE') or define('LOG_FILE', DATA_DIR.DIRECTORY_SEPARATOR.'debug.log');

// Application version
defined('APP_VERSION') or define('APP_VERSION', build_app_version('$Format:%d$', '$Format:%H$'));

// Run automatically database migrations
defined('DB_RUN_MIGRATIONS') or define('DB_RUN_MIGRATIONS', true);

// Database driver: sqlite, mysql or postgres
defined('DB_DRIVER') or define('DB_DRIVER', 'sqlite');

// Sqlite configuration
defined('DB_FILENAME') or define('DB_FILENAME', DATA_DIR.DIRECTORY_SEPARATOR.'db.sqlite');

// Mysql/Postgres configuration
defined('DB_USERNAME') or define('DB_USERNAME', 'root');
defined('DB_PASSWORD') or define('DB_PASSWORD', '');
defined('DB_HOSTNAME') or define('DB_HOSTNAME', 'localhost');
defined('DB_NAME') or define('DB_NAME', 'kanboard');
defined('DB_PORT') or define('DB_PORT', null);
defined('DB_SSL_KEY') or define('DB_SSL_KEY', null);
defined('DB_SSL_CERT') or define('DB_SSL_CERT', null);
defined('DB_SSL_CA') or define('DB_SSL_CA', null);
defined('DB_VERIFY_SERVER_CERT') or define('DB_VERIFY_SERVER_CERT', null);
defined('DB_TIMEOUT') or define('DB_TIMEOUT', null);

// Database backend group provider
defined('DB_GROUP_PROVIDER') or define('DB_GROUP_PROVIDER', true);
defined('DB_USER_PROVIDER') or define('DB_USER_PROVIDER', true);

// LDAP configuration
defined('LDAP_AUTH') or define('LDAP_AUTH', false);
defined('LDAP_SERVER') or define('LDAP_SERVER', '');
defined('LDAP_PORT') or define('LDAP_PORT', 389);
defined('LDAP_SSL_VERIFY') or define('LDAP_SSL_VERIFY', true);
defined('LDAP_START_TLS') or define('LDAP_START_TLS', false);
defined('LDAP_USERNAME_CASE_SENSITIVE') or define('LDAP_USERNAME_CASE_SENSITIVE', false);

defined('LDAP_BIND_TYPE') or define('LDAP_BIND_TYPE', 'anonymous');
defined('LDAP_USERNAME') or define('LDAP_USERNAME', null);
defined('LDAP_PASSWORD') or define('LDAP_PASSWORD', null);

defined('LDAP_USER_BASE_DN') or define('LDAP_USER_BASE_DN', '');
defined('LDAP_USER_FILTER') or define('LDAP_USER_FILTER', '');
defined('LDAP_USER_ATTRIBUTE_USERNAME') or define('LDAP_USER_ATTRIBUTE_USERNAME', 'uid');
defined('LDAP_USER_ATTRIBUTE_FULLNAME') or define('LDAP_USER_ATTRIBUTE_FULLNAME', 'cn');
defined('LDAP_USER_ATTRIBUTE_EMAIL') or define('LDAP_USER_ATTRIBUTE_EMAIL', 'mail');
defined('LDAP_USER_ATTRIBUTE_GROUPS') or define('LDAP_USER_ATTRIBUTE_GROUPS', 'memberof');
defined('LDAP_USER_ATTRIBUTE_PHOTO') or define('LDAP_USER_ATTRIBUTE_PHOTO', '');
defined('LDAP_USER_ATTRIBUTE_LANGUAGE') or define('LDAP_USER_ATTRIBUTE_LANGUAGE', '');
defined('LDAP_USER_CREATION') or define('LDAP_USER_CREATION', true);

defined('LDAP_GROUP_ADMIN_DN') or define('LDAP_GROUP_ADMIN_DN', '');
defined('LDAP_GROUP_MANAGER_DN') or define('LDAP_GROUP_MANAGER_DN', '');

defined('LDAP_GROUP_PROVIDER') or define('LDAP_GROUP_PROVIDER', false);
defined('LDAP_GROUP_BASE_DN') or define('LDAP_GROUP_BASE_DN', '');
defined('LDAP_GROUP_FILTER') or define('LDAP_GROUP_FILTER', '');
defined('LDAP_GROUP_USER_FILTER') or define('LDAP_GROUP_USER_FILTER', '');
defined('LDAP_GROUP_ATTRIBUTE_NAME') or define('LDAP_GROUP_ATTRIBUTE_NAME', 'cn');

// Proxy authentication
defined('REVERSE_PROXY_AUTH') or define('REVERSE_PROXY_AUTH', false);
defined('REVERSE_PROXY_USER_HEADER') or define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');
defined('REVERSE_PROXY_DEFAULT_ADMIN') or define('REVERSE_PROXY_DEFAULT_ADMIN', '');
defined('REVERSE_PROXY_DEFAULT_DOMAIN') or define('REVERSE_PROXY_DEFAULT_DOMAIN', '');

// Remember me authentication
defined('REMEMBER_ME_AUTH') or define('REMEMBER_ME_AUTH', true);

// Mail configuration
defined('MAIL_CONFIGURATION') or define('MAIL_CONFIGURATION', true);
defined('MAIL_FROM') or define('MAIL_FROM', 'notifications@kanboard.local');
defined('MAIL_TRANSPORT') or define('MAIL_TRANSPORT', 'mail');
defined('MAIL_SMTP_HOSTNAME') or define('MAIL_SMTP_HOSTNAME', '');
defined('MAIL_SMTP_PORT') or define('MAIL_SMTP_PORT', 25);
defined('MAIL_SMTP_USERNAME') or define('MAIL_SMTP_USERNAME', '');
defined('MAIL_SMTP_PASSWORD') or define('MAIL_SMTP_PASSWORD', '');
defined('MAIL_SMTP_ENCRYPTION') or define('MAIL_SMTP_ENCRYPTION', null);
defined('MAIL_SENDMAIL_COMMAND') or define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');

// Enable or disable "Strict-Transport-Security" HTTP header
defined('ENABLE_HSTS') or define('ENABLE_HSTS', true);

// Enable or disable "X-Frame-Options: DENY" HTTP header
defined('ENABLE_XFRAME') or define('ENABLE_XFRAME', true);

// Escape html inside markdown text
defined('MARKDOWN_ESCAPE_HTML') or define('MARKDOWN_ESCAPE_HTML', true);

// API alternative authentication header, the default is HTTP Basic Authentication defined in RFC2617
defined('API_AUTHENTICATION_HEADER') or define('API_AUTHENTICATION_HEADER', '');

// Enable/disable url rewrite
defined('ENABLE_URL_REWRITE') or define('ENABLE_URL_REWRITE', isset($_SERVER['HTTP_MOD_REWRITE']));

// Hide login form
defined('HIDE_LOGIN_FORM') or define('HIDE_LOGIN_FORM', false);

// Disabling logout (for external SSO authentication)
defined('DISABLE_LOGOUT') or define('DISABLE_LOGOUT', false);

// Bruteforce protection
defined('BRUTEFORCE_CAPTCHA') or define('BRUTEFORCE_CAPTCHA', 3);
defined('BRUTEFORCE_LOCKDOWN') or define('BRUTEFORCE_LOCKDOWN', 6);
defined('BRUTEFORCE_LOCKDOWN_DURATION') or define('BRUTEFORCE_LOCKDOWN_DURATION', 15);

// Session duration in second (0 = until the browser is closed)
// See http://php.net/manual/en/session.configuration.php#ini.session.cookie-lifetime
defined('SESSION_DURATION') or define('SESSION_DURATION', 0);

// HTTP Client
defined('HTTP_TIMEOUT') or define('HTTP_TIMEOUT', 10);
defined('HTTP_MAX_REDIRECTS') or define('HTTP_MAX_REDIRECTS', 3);
defined('HTTP_PROXY_HOSTNAME') or define('HTTP_PROXY_HOSTNAME', '');
defined('HTTP_PROXY_PORT') or define('HTTP_PROXY_PORT', '3128');
defined('HTTP_PROXY_USERNAME') or define('HTTP_PROXY_USERNAME', '');
defined('HTTP_PROXY_PASSWORD') or define('HTTP_PROXY_PASSWORD', '');
defined('HTTP_PROXY_EXCLUDE') or define('HTTP_PROXY_EXCLUDE', 'localhost');
defined('HTTP_VERIFY_SSL_CERTIFICATE') or define('HTTP_VERIFY_SSL_CERTIFICATE', true);

defined('TOTP_ISSUER') or define('TOTP_ISSUER', 'Kanboard');

// Comma separated list of fields to not synchronize when using external authentication providers
defined('EXTERNAL_AUTH_EXCLUDE_FIELDS') or define('EXTERNAL_AUTH_EXCLUDE_FIELDS', 'username');

// Documentation URL
defined('DOCUMENTATION_URL_PATTERN') or define('DOCUMENTATION_URL_PATTERN', 'https://docs.kanboard.org/en/%s/user_guide/%s.html');
