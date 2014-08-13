<?php

// Auto-refresh frequency in seconds for the public board view (60 seconds by default)
define('BOARD_PUBLIC_CHECK_INTERVAL', 60);

// Board refresh frequency in seconds (the value 0 disable this feature, 10 seconds by default)
define('BOARD_CHECK_INTERVAL', 10);

// Period (in second) to consider a task was modified recently (0 to disable, 2 days by default)
define('RECENT_TASK_PERIOD', 48*60*60);

// Database driver: sqlite or mysql (sqlite by default)
define('DB_DRIVER', 'sqlite');

// Mysql username
define('DB_USERNAME', 'root');

// Mysql password
define('DB_PASSWORD', '');

// Mysql hostname
define('DB_HOSTNAME', 'localhost');

// Mysql database name
define('DB_NAME', 'kanboard');

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', false);

// LDAP server hostname
define('LDAP_SERVER', '');

// LDAP server port (389 by default)
define('LDAP_PORT', 389);

// By default, require certificate to be verified for ldaps:// style URL. Set to false to skip the verification.
define('LDAP_SSL_VERIFY', true);

// LDAP username to connect with. NULL for anonymous bind (by default).
define('LDAP_USERNAME', null);

// LDAP password to connect with. NULL for anonymous bind (by default).
define('LDAP_PASSWORD', null);

// LDAP account base, i.e. root of all user account
// Example: ou=people,dc=example,dc=com
define('LDAP_ACCOUNT_BASE', '');

// LDAP query pattern to use when searching for a user account
// Example for ActiveDirectory: '(&(objectClass=user)(sAMAccountName=%s))'
// Example for OpenLDAP: 'uid=%s'
define('LDAP_USER_PATTERN', '');

// Name of an attribute of the user account object which should be used as the full name of the user.
define('LDAP_ACCOUNT_FULLNAME', 'displayname');

// Name of an attribute of the user account object which should be used as the email of the user.
define('LDAP_ACCOUNT_EMAIL', 'mail');

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
