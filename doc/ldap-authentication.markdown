LDAP Authentication
===================

Requirements
------------

- PHP LDAP extension enabled
- LDAP server:
    - OpenLDAP
    - Microsoft Active Directory
    - Novell eDirectory

Workflow
--------

When the LDAP authentication is activated, the login process works like that:

1. Try first to authenticate the user by using the database
2. If the user is not found inside the database, a LDAP authentication is performed
3. If the LDAP authentication is successful, by default a local user is created automatically with no password and marked as LDAP users.

The full name and the email address are automatically fetched from the LDAP server.

Authentication Types
--------------------

| Type       | Description                                                     |
|------------|-----------------------------------------------------------------|
| Proxy User | A specific user is used to browse LDAP directory                |
| User       | The end-user credentials are used for browsing LDAP directory   |
| Anonymous  | No authentication is performed for LDAP browsing                |

**The recommended authentication method is "Proxy"**.

#### Anonymous mode

```php
define('LDAP_BIND_TYPE', 'anonymous');
define('LDAP_USERNAME', null);
define('LDAP_PASSWORD', null);
```

This is the default value but some LDAP servers don't allow anonymous browsing for security reasons.

#### Proxy mode

A specific user is used to browse the LDAP directory:

```php
define('LDAP_BIND_TYPE', 'proxy');
define('LDAP_USERNAME', 'my proxy user');
define('LDAP_PASSWORD', 'my proxy password');
```

#### User mode

This method uses the credentials provided by the end-user.

For example, Microsoft Active Directory doesn't allow anonymous browsing by default and if you don't want to use a proxy user you can use this method.

```php
define('LDAP_BIND_TYPE', 'user');
define('LDAP_USERNAME', '%s@kanboard.local');
define('LDAP_PASSWORD', null);
```

In this case, the constant `LDAP_USERNAME` is used as a pattern to the ldap username, examples:

- `%s@kanboard.local` will be replaced by `my_user@kanboard.local`
- `KANBOARD\\%s` will be replaced by `KANBOARD\my_user`

User LDAP filter
----------------

The configuration parameter `LDAP_USER_FILTER` is used to find users in LDAP directory.

Examples:

- `(&(objectClass=user)(sAMAccountName=%s))` is replaced by `(&(objectClass=user)(sAMAccountName=my_username))`
- `uid=%s` is replaced by `uid=my_username`

Other examples of [filters for Active Directory](http://social.technet.microsoft.com/wiki/contents/articles/5392.active-directory-ldap-syntax-filters.aspx)

Example to filter access to Kanboard:

`(&(objectClass=user)(sAMAccountName=%s)(memberOf=CN=Kanboard Users,CN=Users,DC=kanboard,DC=local))`

This example allows only people members of the group "Kanboard Users" to connect to Kanboard.

Example for Microsoft Active Directory
--------------------------------------

Let's say we have a domain `KANBOARD` (kanboard.local) and the primary controller is `myserver.kanboard.local`.

First example with proxy mode:

```php
<?php

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', true);

define('LDAP_BIND_TYPE', 'proxy');
define('LDAP_USERNAME', 'administrator@kanboard.local');
define('LDAP_PASSWORD', 'my super secret password');

// LDAP server hostname
define('LDAP_SERVER', 'myserver.kanboard.local');

// LDAP properties
define('LDAP_USER_BASE_DN', 'CN=Users,DC=kanboard,DC=local');
define('LDAP_USER_FILTER', '(&(objectClass=user)(sAMAccountName=%s))');
```

Second example with user mode:

```php
<?php

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', true);

define('LDAP_BIND_TYPE', 'user');
define('LDAP_USERNAME', '%s@kanboard.local');
define('LDAP_PASSWORD', null);

// LDAP server hostname
define('LDAP_SERVER', 'myserver.kanboard.local');

// LDAP properties
define('LDAP_USER_BASE_DN', 'CN=Users,DC=kanboard,DC=local');
define('LDAP_USER_FILTER', '(&(objectClass=user)(sAMAccountName=%s))');
```

Example for OpenLDAP
--------------------

Our LDAP server is `myserver.example.com` and all users are stored under `ou=People,dc=example,dc=com`.

For this example we use the anonymous binding.

```php
<?php

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', true);

// LDAP server hostname
define('LDAP_SERVER', 'myserver.example.com');

// LDAP properties
define('LDAP_USER_BASE_DN', 'ou=People,dc=example,dc=com');
define('LDAP_USER_FILTER', 'uid=%s');
```

Disable automatic account creation
-----------------------------------

By default, Kanboard will create a user account automatically if nothing is found.

You can disable this behavior if you prefer to create user accounts manually to restrict Kanboard to only some people.

Just change the value of `LDAP_ACCOUNT_CREATION` to `false`:

```php
// Automatically create user account
define('LDAP_ACCOUNT_CREATION', false);
```

Troubleshootings
----------------

### SELinux restrictions

If SELinux is enabled, you have to allow Apache to reach out your LDAP server.

- You can switch SELinux to the permissive mode or disable it (not recommended)
- You can allow all network connections, by example `setsebool -P httpd_can_network_connect=1` or have a more restrictive rule

In any case, refer to the official Redhat/Centos documentation.

### Enable debug mode

If you are not able to setup correctly the LDAP authentication, you can [enable the debug mode](config.markdown) and watch log files.
