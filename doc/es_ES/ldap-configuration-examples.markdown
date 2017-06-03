LDAP Ejemplos de configuración
==============================

Microsoft Active Directory
--------------------------

- Autenticacion de usuario
- Descargar la imagen de perfil de usuario de Active Directory
- Establecer idioma del usuario del atributo LDAP
- Kanboard funciones se asignan a grupos de Active Directory
- Proveedores de grupo LDAP está habilitado

```php
define('LDAP_AUTH', true);

define('LDAP_SERVER', 'my-ldap-server');
define('LDAP_PORT', 389);

define('LDAP_BIND_TYPE', 'proxy');
define('LDAP_USERNAME', 'administrator@kanboard.local');
define('LDAP_PASSWORD', 'secret');

define('LDAP_USER_BASE_DN', 'CN=Users,DC=kanboard,DC=local');
define('LDAP_USER_FILTER', '(&(objectClass=user)(sAMAccountName=%s))');

define('LDAP_USER_ATTRIBUTE_USERNAME', 'samaccountname');
define('LDAP_USER_ATTRIBUTE_FULLNAME', 'displayname');
define('LDAP_USER_ATTRIBUTE_PHOTO', 'jpegPhoto');
define('LDAP_USER_ATTRIBUTE_LANGUAGE', 'preferredLanguage');

define('LDAP_GROUP_ADMIN_DN', 'CN=Kanboard Admins,CN=Users,DC=kanboard,DC=local');
define('LDAP_GROUP_MANAGER_DN', 'CN=Kanboard Managers,CN=Users,DC=kanboard,DC=local');

define('LDAP_GROUP_PROVIDER', true);
define('LDAP_GROUP_BASE_DN', 'CN=Users,DC=kanboard,DC=local');
define('LDAP_GROUP_FILTER', '(&(objectClass=group)(sAMAccountName=%s*))');
define('LDAP_GROUP_ATTRIBUTE_NAME', 'cn');
```

OpenLDAP con memberOf overlay
------------------------------

Usuario LDIF ejemplo:

```
dn: uid=manager,ou=Users,dc=kanboard,dc=local
objectClass: top
objectClass: person
objectClass: organizationalPerson
objectClass: inetOrgPerson
uid: manager
sn: Lastname
givenName: Firstname
cn: Kanboard Manager
displayName: Kanboard Manager
mail: manager@kanboard.local
userPassword: password
memberOf: cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local
```

Grupo LDIF ejemplo:

```
dn: cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local
objectClass: top
objectClass: groupOfNames
cn: Kanboard Managers
member: uid=manager,ou=Users,dc=kanboard,dc=local
```

Configuración del Kanboard :

- Autenticación de usuario
- Los roles de Kanboard restan mapeados por los grupos LDAP 
- LDAP los grupos de proveedores estan habilitados

```php
define('LDAP_AUTH', true);

define('LDAP_SERVER', 'my-ldap-server');
define('LDAP_PORT', 389);

define('LDAP_BIND_TYPE', 'proxy');
define('LDAP_USERNAME', 'cn=admin,DC=kanboard,DC=local');
define('LDAP_PASSWORD', 'password');

define('LDAP_USER_BASE_DN', 'OU=Users,DC=kanboard,DC=local');
define('LDAP_USER_FILTER', 'uid=%s');

define('LDAP_GROUP_ADMIN_DN', 'cn=Kanboard Admins,ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_MANAGER_DN', 'cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local');

define('LDAP_GROUP_PROVIDER', true);
define('LDAP_GROUP_BASE_DN', 'ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_FILTER', '(&(objectClass=groupOfNames)(cn=%s*))');
define('LDAP_GROUP_ATTRIBUTE_NAME', 'cn');
```

OpenLDAP con grupos Posix (memberUid)
--------------------------------------

Usario LDIF ejemplo:

```
dn: uid=manager,ou=Users,dc=kanboard,dc=local
objectClass: inetOrgPerson
objectClass: posixAccount
objectClass: shadowAccount
uid: manager
sn: Lastname
givenName: Firstname
cn: Kanboard Manager
displayName: Kanboard Manager
uidNumber: 10001
gidNumber: 8000
userPassword: password
homeDirectory: /home/manager
mail: manager@kanboard.local
```

Grupo LDIF ejemplo:

```
dn: cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local
objectClass: posixGroup
cn: Kanboard Managers
gidNumber: 5001
memberUid: manager
```

Configuración del Kanboard :

- Autenticación de usuario
- Los roles de Kanboard restan mapeados por los grupos LDAP 
- LDAP los grupos de proveedores estan habilitados

```php
define('LDAP_AUTH', true);

define('LDAP_SERVER', 'my-ldap-server');
define('LDAP_PORT', 389);

define('LDAP_BIND_TYPE', 'proxy');
define('LDAP_USERNAME', 'cn=admin,DC=kanboard,DC=local');
define('LDAP_PASSWORD', 'password');

define('LDAP_USER_BASE_DN', 'OU=Users,DC=kanboard,DC=local');
define('LDAP_USER_FILTER', 'uid=%s');

define('LDAP_GROUP_ADMIN_DN', 'cn=Kanboard Admins,ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_MANAGER_DN', 'cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local');

// Este filtro se utiliza para encontrar los grupos de nuestro usuario
define('LDAP_GROUP_USER_FILTER', '(&(objectClass=posixGroup)(memberUid=%s))');

define('LDAP_GROUP_PROVIDER', true);
define('LDAP_GROUP_BASE_DN', 'ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_FILTER', '(&(objectClass=posixGroup)(cn=%s*))');
define('LDAP_GROUP_ATTRIBUTE_NAME', 'cn');
```

OpenLDAP con groupOfNames
--------------------------

Usuarios LDIF ejemplo:

```
dn: uid=manager,ou=Users,dc=kanboard,dc=local
objectClass: top
objectClass: person
objectClass: organizationalPerson
objectClass: inetOrgPerson
uid: manager
sn: Lastname
givenName: Firstname
cn: Kanboard Manager
displayName: Kanboard Manager
mail: manager@kanboard.local
userPassword: password
```

Grupo LDIF ejemplo:

```
dn: cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local
objectClass: top
objectClass: groupOfNames
cn: Kanboard Managers
member: uid=manager,ou=Users,dc=kanboard,dc=local
```

Configuración del Kanboard :

- Autenticación de usuario
- Los roles de Kanboard restan mapeados por los grupos LDAP 
- LDAP los grupos de proveedores estan habilitados

```php
define('LDAP_AUTH', true);

define('LDAP_SERVER', 'my-ldap-server');
define('LDAP_PORT', 389);

define('LDAP_BIND_TYPE', 'proxy');
define('LDAP_USERNAME', 'cn=admin,DC=kanboard,DC=local');
define('LDAP_PASSWORD', 'password');

define('LDAP_USER_BASE_DN', 'OU=Users,DC=kanboard,DC=local');
define('LDAP_USER_FILTER', 'uid=%s');

define('LDAP_GROUP_ADMIN_DN', 'cn=Kanboard Admins,ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_MANAGER_DN', 'cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local');

// Este filtro se utiliza para encontrar los grupos de nuestro usuario
define('LDAP_GROUP_USER_FILTER', '(&(objectClass=groupOfNames)(member=uid=%s,ou=Users,dc=kanboard,dc=local))');

define('LDAP_GROUP_PROVIDER', true);
define('LDAP_GROUP_BASE_DN', 'ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_FILTER', '(&(objectClass=groupOfNames)(cn=%s*))');
define('LDAP_GROUP_ATTRIBUTE_NAME', 'cn');
```
