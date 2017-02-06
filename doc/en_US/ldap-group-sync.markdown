LDAP Group Synchronization
==========================

Requirements
------------

- Have LDAP authentication properly configured
- Use a LDAP server that supports `memberOf` or `memberUid` (PosixGroups)

Define automatically user roles based on LDAP groups
----------------------------------------------------

Use these constants in your config file:

- `LDAP_GROUP_ADMIN_DN`: Distinguished names for application administrators
- `LDAP_GROUP_MANAGER_DN`: Distinguished names for application managers

### Example for Active Directory:

```php
define('LDAP_GROUP_ADMIN_DN', 'CN=Kanboard Admins,CN=Users,DC=kanboard,DC=local');
define('LDAP_GROUP_MANAGER_DN', 'CN=Kanboard Managers,CN=Users,DC=kanboard,DC=local');
```

- People member of "Kanboard Admins" will have the role "Administrator"
- People member of "Kanboard Managers" will have the role "Managers"
- Everybody else will have the role "User"

### Example for OpenLDAP with Posix Groups:

```php
define('LDAP_GROUP_BASE_DN', 'ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_USER_FILTER', '(&(objectClass=posixGroup)(memberUid=%s))');
define('LDAP_GROUP_ADMIN_DN', 'cn=Kanboard Admins,ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_MANAGER_DN', 'cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local');
```

You **must define the parameter** `LDAP_GROUP_USER_FILTER` if your LDAP server use `memberUid` instead of `memberOf`.
All parameters of this example are mandatory.

Automatically load LDAP groups for project permissions
------------------------------------------------------

This feature allows you to sync automatically LDAP groups with Kanboard groups.
Each group can have a different project role assigned.

On the project permissions page, people can enter groups in the auto-complete field and Kanboard can search for groups with any provider enabled.

If the group doesn't exist in the local database, it will be automatically synced.

- `LDAP_GROUP_PROVIDER`: Enable the LDAP group provider
- `LDAP_GROUP_BASE_DN`: Distinguished names to find groups in LDAP directory
- `LDAP_GROUP_FILTER`: LDAP filter used to perform the query
- `LDAP_GROUP_ATTRIBUTE_NAME`: LDAP attribute used to fetch the group name

### Example for Active Directory:

```php
define('LDAP_GROUP_PROVIDER', true);
define('LDAP_GROUP_BASE_DN', 'CN=Groups,DC=kanboard,DC=local');
define('LDAP_GROUP_FILTER', '(&(objectClass=group)(sAMAccountName=%s*))');
```

With the filter given as example above, Kanboard will search for groups that match the query.
If the end-user enter the text "My group" in the auto-complete box, Kanboard will return all groups that match the pattern: `(&(objectClass=group)(sAMAccountName=My group*))`.

- Note 1: The special characters `*` is important here, **otherwise an exact match will be done**.
- Note 2: This feature is only compatible with LDAP authentication configured in "proxy" or "anonymous" mode

[More examples of LDAP filters for Active Directory](http://social.technet.microsoft.com/wiki/contents/articles/5392.active-directory-ldap-syntax-filters.aspx)

### Example for OpenLDAP with Posix Groups:

```php
define('LDAP_GROUP_PROVIDER', true);
define('LDAP_GROUP_BASE_DN', 'ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_FILTER', '(&(objectClass=posixGroup)(cn=%s*))');
```
