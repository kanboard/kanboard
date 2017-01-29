LDAP Configuration Parameters
=============================

Here is the list of available LDAP parameters:

| Parameter                       | Default value  | Description                                    |
|---------------------------------|----------------|------------------------------------------------|
| `LDAP_AUTH`                     | false          | Enable LDAP authentication                     |
| `LDAP_SERVER`                   | Empty          | LDAP server hostname                           |
| `LDAP_PORT`                     | 389            | LDAP server port                               |
| `LDAP_SSL_VERIFY`               | true           | Validate certificate for `ldaps://` style URL  |
| `LDAP_START_TLS`                | false          | Enable LDAP start TLS                          |
| `LDAP_USERNAME_CASE_SENSITIVE`  | false          | Kanboard lowercase the ldap username to avoid duplicate users (the database is case sensitive)  |
| `LDAP_BIND_TYPE`                | anonymous      | Bind type: "anonymous", "user" or "proxy"      |
| `LDAP_USERNAME`                 | null           | LDAP username to use with proxy mode or username pattern to use with user mode  |
| `LDAP_PASSWORD`                 | null           | LDAP password to use for proxy mode            |
| `LDAP_USER_BASE_DN`             | Empty          | LDAP DN for users (Example: "CN=Users,DC=kanboard,DC=local")           |
| `LDAP_USER_FILTER`              | Empty          | LDAP pattern to use when searching for a user account (Example: "(&(objectClass=user)(sAMAccountName=%s))")            |
| `LDAP_USER_ATTRIBUTE_USERNAME`  | uid            | LDAP attribute for username (Example: "samaccountname")            |
| `LDAP_USER_ATTRIBUTE_FULLNAME`  | cn             | LDAP attribute for user full name (Example: "displayname")         |
| `LDAP_USER_ATTRIBUTE_EMAIL`     | mail           | LDAP attribute for user email                                      |
| `LDAP_USER_ATTRIBUTE_GROUPS`    | memberof       | LDAP attribute to find groups in user profile                      |
| `LDAP_USER_ATTRIBUTE_PHOTO`     | Empty          | LDAP attribute to find user photo (jpegPhoto or thumbnailPhoto                      |
| `LDAP_USER_ATTRIBUTE_LANGUAGE`  | Empty          | LDAP attribute for user language (preferredlanguage), the accepted language format is "fr-FR" |
| `LDAP_USER_CREATION`            | true           | Enable automatic LDAP user creation                                |
| `LDAP_GROUP_ADMIN_DN`           | Empty          | LDAP DN for administrators (Example: "CN=Kanboard-Admins,CN=Users,DC=kanboard,DC=local")   |
| `LDAP_GROUP_MANAGER_DN`         | Empty          | LDAP DN for managers (Example: "CN=Kanboard Managers,CN=Users,DC=kanboard,DC=local")   |
| `LDAP_GROUP_PROVIDER`           | false          | Enable LDAP group provider for project permissions  |
| `LDAP_GROUP_BASE_DN`            | Empty          | LDAP Base DN for groups   |
| `LDAP_GROUP_FILTER`             | Empty          | LDAP group filter (Example: "(&(objectClass=group)(sAMAccountName=%s*))")   |
| `LDAP_GROUP_USER_FILTER`        | Empty          | If defined, Kanboard will search user groups in LDAP_GROUP_BASE_DN with this filter, it's useful only for posixGroups (Example: `(&(objectClass=posixGroup)(memberUid=%s))`) |
| `LDAP_GROUP_ATTRIBUTE_NAME`     | cn             | LDAP attribute for the group name  |

Notes:

- LDAP attributes must be in lowercase
