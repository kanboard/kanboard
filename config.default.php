<?php

// Auto-refresh frequency in seconds for the public board view (60 seconds by default)
define('BOARD_PUBLIC_CHECK_INTERVAL', 60);

// Board refresh frequency in seconds (the value 0 disable this feature, 10 seconds by default)
define('BOARD_CHECK_INTERVAL', 10);

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

// User LDAP DN
// Example for ActiveDirectory: 'MYDOMAIN\\%s' or '%s@mydomain.local'
// Example for OpenLDAP: 'uid=%s,ou=People,dc=example,dc=com'
define('LDAP_USER_DN', '%s');

// Enable/disable Google authentication
define('GOOGLE_AUTH', false);

// Google client id (Get this value from the Google developer console)
define('GOOGLE_CLIENT_ID', '');

// Google client secret key (Get this value from the Google developer console)
define('GOOGLE_CLIENT_SECRET', '');
