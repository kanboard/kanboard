LDAP authentication
===================

Requirements
------------

- LDAP extension for PHP
- LDAP server (By example: OpenLDAP or Microsoft Active Directory)

Workflow
--------

When the LDAP authentication is activated, the login process work like that:

1. Try to authenticate the user by using the database
2. If the user is not found inside the database, a LDAP authentication is performed
3. If the LDAP authentication is successful, a local user is created automatically with no password and marked as LDAP user.

Differences between a local user and a LDAP user are the following:

- LDAP users have no local passwords
- LDAP users can't modify their password with the user interface
- By default, all LDAP users have no admin privileges
- To become administrator, a LDAP user must be promoted by another administrator

The full name and the email address are automatically fetched from the LDAP server.

Configuration
-------------

You have to create a custom config file named `config.php` (you can also use the template `config.default.php`).
This file must be stored in the root directory of Kanboard.

### Available configuration parameters

```php
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
// Example: ou=People,dc=example,dc=com
define('LDAP_ACCOUNT_BASE', '');

// LDAP query pattern to use when searching for a user account
// Example for ActiveDirectory: '(&(objectClass=user)(sAMAccountName=%s))'
// Example for OpenLDAP: 'uid=%s'
define('LDAP_USER_PATTERN', '');

// Name of an attribute of the user account object which should be used as the full name of the user.
define('LDAP_ACCOUNT_FULLNAME', 'displayname');

// Name of an attribute of the user account object which should be used as the email of the user.
define('LDAP_ACCOUNT_EMAIL', 'mail');
```

### Example for Microsoft Active Directory

Let's say we have a domain `KANBOARD` (kanboard.local) and the primary controller is `myserver.kanboard.local`.

```php
<?php

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', true);

// Set credentials for be allow to browse the LDAP directory
define('LDAP_USERNAME', 'administrator@kanboard.local');
define('LDAP_PASSWORD', 'my super secret password');

// LDAP server hostname
define('LDAP_SERVER', 'myserver.kanboard.local');

// LDAP properties
define('LDAP_ACCOUNT_BASE', 'CN=Users,DC=kanboard,DC=local');
define('LDAP_USER_PATTERN', '(&(objectClass=user)(sAMAccountName=%s))');
define('LDAP_ACCOUNT_FULLNAME', 'displayname');
define('LDAP_ACCOUNT_EMAIL', 'mail');
```

### Example for OpenLDAP

Here, our LDAP server is `myserver.example.com` and all users are stored in the hierarchy `ou=People,dc=example,dc=com`.

```php
<?php

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', true);

// LDAP server hostname
define('LDAP_SERVER', 'myserver.example.com');

// LDAP properties
define('LDAP_ACCOUNT_BASE', 'ou=People,dc=example,dc=com');
define('LDAP_USER_PATTERN', 'uid=%s');
define('LDAP_ACCOUNT_FULLNAME', 'displayname');
define('LDAP_ACCOUNT_EMAIL', 'mail');
```

The `%s` is replaced by the username for the parameter `LDAP_USER_PATTERN`, so you can define a custom Distinguished Name.
