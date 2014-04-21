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

Configuration
-------------

The first step is to create a custom config file named `config.php`.
This file must be stored in the root directory.

To do that, you can create an empty PHP file or copy/rename the sample file `config.default.php`.

### Example for Microsoft Active Directory

Let's say we have a domain `MYDOMAIN` (mydomain.local) and the primary controller is `myserver.mydomain.local`.

```php
<?php

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', true);

// LDAP server hostname
define('LDAP_SERVER', 'myserver.mydomain.local');

// User LDAP DN
define('LDAP_USER_DN', 'MYDOMAIN\\%s');

// Another way to do the same thing
define('LDAP_USER_DN', '%s@mydomain.local');

```

### Example for OpenLDAP

Here, our LDAP server is `myserver.example.com` and all users are stored in the hierarchy `ou=People,dc=example,dc=com`.

```php
<?php

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', true);

// LDAP server hostname
define('LDAP_SERVER', 'myserver.example.com');

// User LDAP DN
define('LDAP_USER_DN', 'uid=%s,ou=People,dc=example,dc=com');

```

The `%s` is replaced by the username for the parameter `LDAP_USER_DN`, so you can define a custom Distinguished Name.
