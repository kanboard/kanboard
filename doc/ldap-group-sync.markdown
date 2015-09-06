LDAP Group Synchronization
==========================

Requirements
------------

- Have LDAP authentication properly configured
- Use a LDAP server that supports `memberOf`

Automatically define Kanboard groups based on LDAP groups
---------------------------------------------------------

In your config file, define the constants `LDAP_GROUP_ADMIN_DN` and `LDAP_GROUP_PROJECT_ADMIN_DN`. Here an example, replace the values according to your own LDAP configuration:

```php
define('LDAP_GROUP_ADMIN_DN', 'CN=Kanboard Admins,CN=Users,DC=kanboard,DC=local');
define('LDAP_GROUP_PROJECT_ADMIN_DN', 'CN=Kanboard Project Admins,CN=Users,DC=kanboard,DC=local');
```

- People member of "Kanboard Admins" will be "Kanboard Administrators"
- People member of "Kanboard Project Admins" will be "Kanboard Project Administrators"
- Everybody else will be Kanboard Standard Users

Note: At the moment, that works only at account creation.

Filter Kanboard access based on the LDAP group
----------------------------------------------

To allow only some users to use Kanboard, use the existing `LDAP_USER_PATTERN` constant:

```php
define('LDAP_USER_PATTERN', '(&(objectClass=user)(sAMAccountName=%s)(memberOf=CN=Kanboard Users,CN=Users,DC=kanboard,DC=local))');
```

This example allow only people member of the group "Kanboard Users" to connect to Kanboard.

