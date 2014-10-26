LDAP authentication
===================

Requirements
------------

- LDAP extension for PHP
- LDAP server:
    - OpenLDAP
    - Microsoft Active Directory
    - Novell eDirectory

Workflow
--------

When the LDAP authentication is activated, the login process work like that:

1. Try first to authenticate the user by using the database
2. If the user is not found inside the database, a LDAP authentication is performed
3. If the LDAP authentication is successful, a local user is created automatically with no password and marked as LDAP user.

### Differences between a local user and a LDAP user are the following:

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

// LDAP bind type: "anonymous", "user" (use the given user/password from the form) and "proxy" (a specific user to browse the LDAP directory)
define('LDAP_BIND_TYPE', 'anonymous');

// LDAP username to connect with. null for anonymous bind (by default).
// Or for user bind type, you can use a pattern like that %s@kanboard.local
define('LDAP_USERNAME', null);

// LDAP password to connect with. null for anonymous bind (by default).
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

### LDAP bind type

There is 3 possible ways to browse the LDAP directory:

#### Anonymous browsing

```php
define('LDAP_BIND_TYPE', 'anonymous');
define('LDAP_USERNAME', null);
define('LDAP_PASSWORD', null);
```

This is the default value but some LDAP servers don't allow that.

#### Proxy user

A specific user is used to browse the LDAP directory.
By example, Novell eDirectory use that method.

```php
define('LDAP_BIND_TYPE', 'proxy');
define('LDAP_USERNAME', 'my proxy user');
define('LDAP_PASSWORD', 'my proxy password');
```

#### User credentials

This method use the credentials provided by the end-user.
By example, Microsoft Active Directory doesn't allow anonymous browsing by default and if you don't want to use a proxy user you can use this method.

```php
define('LDAP_BIND_TYPE', 'user');
define('LDAP_USERNAME', '%s@mydomain.local');
define('LDAP_PASSWORD', null);
```

Here, the `LDAP_USERNAME` is use to define a replacement pattern:

```php
define('LDAP_USERNAME', '%s@mydomain.local');

// Another way to do the same:

define('LDAP_USERNAME', 'MYDOMAIN\\%s');
```

### Example for Microsoft Active Directory

Let's say we have a domain `KANBOARD` (kanboard.local) and the primary controller is `myserver.kanboard.local`.
Microsoft Active Directory doesn't allow anonymous binding by default.

First example with a proxy user:

```php
<?php

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', true);

// Credentials to be allowed to browse the LDAP directory
define('LDAP_BIND_TYPE', 'proxy');
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

Another way with no proxy user:

```php
<?php

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', true);

// Credentials to be allowed to browse the LDAP directory
define('LDAP_BIND_TYPE', 'user');
define('LDAP_USERNAME', '%s@kanboard.local'); // or 'KANBOARD\\%s'
define('LDAP_PASSWORD', null);

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

For this example with use the anonymous binding.

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
