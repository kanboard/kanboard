Autenticación  LDAP 
===================

Requirimientos
------------

- PHP LDAP extensión activa
- Servidor LDAP:
    - OpenLDAP
    - Microsoft Active Directory
    - Novell eDirectory

Workflow [flujo de trabajo]
----------------------------

Cuando se activa la autenticación LDAP, el proceso de inicio de sesión funciona de la siguiente manera:

1. Primero trata el usuario de autenticarse usando la base de datos
2. Si no se encuentra el usuario dentro de la base de datos, se realiza una autenticación LDAP
3. Si la autenticación LDAP es exitosa, de forma predeterminada, se crea automáticamente un usuario local sin contraseña y se marca como usuarios de LDAP.

El nombre completo y la dirección email son automaticamente obtenida desde el servidor LDAP.

Authentication Types
--------------------

| Type       | Description                                                     |
|------------|-----------------------------------------------------------------|
| Proxy User | Un usuario específico se utiliza para navegar por el directorio LDAP               |
| User       | Las credenciales de los usuarios finales se utilizan para la navegación de directorio LDAP  |
| Anonymous  | No se realiza la autenticación LDAP para la navegación               |

**La autenticación recomendada es el metodo "Proxy"**.

#### Modo Anonimo

```php
define('LDAP_BIND_TYPE', 'anonymous');
define('LDAP_USERNAME', null);
define('LDAP_PASSWORD', null);
```

Este es un valor por default pero algunos servidores LDAP no permiten la navegación anonima por razones de seguridad.

#### Modo Proxie

Un usuario específico se utiliza para navegar por el directorio LDAP:

```php
define('LDAP_BIND_TYPE', 'proxy');
define('LDAP_USERNAME', 'my proxy user');
define('LDAP_PASSWORD', 'my proxy password');
```

#### Modo usuario

Este método utiliza las credenciales proporcionadas por el usuario final.

Por ejemplo, Microsoft Active Directory no permite la navegación anónima por defecto y si no desea utilizar un usuario proxy puede utilizar este método.

```php
define('LDAP_BIND_TYPE', 'user');
define('LDAP_USERNAME', '%s@kanboard.local');
define('LDAP_PASSWORD', null);
```

En esta caso, la constante `LDAP_USERNAME` es usada 
In this case, the constant ` se utiliza como patrón para los LDAP nombre de usuario, ejemplos:

- `%s@kanboard.local` will be replaced by `my_user@kanboard.local`
- `KANBOARD\\%s` will be replaced by `KANBOARD\my_user`

Filtro de usuarios LDAP
-----------------------

Los parametros de configuración `LDAP_USER_FILTER` son usados para buscar usuarios en el directorio LDAP.

Ejmplos:

- `(&(objectClass=user)(sAMAccountName=%s))` is replaced by `(&(objectClass=user)(sAMAccountName=my_username))`
- `uid=%s` is replaced by `uid=my_username`

Otros ejemplos [filtros por Active Directory](http://social.technet.microsoft.com/wiki/contents/articles/5392.active-directory-ldap-syntax-filters.aspx)

Ejemplo para filtrar el acceso a Kanboard:

`(&(objectClass=user)(sAMAccountName=%s)(memberOf=CN=Kanboard Users,CN=Users,DC=kanboard,DC=local))`

Este ejemplo permite que sólo las personas miembros del grupo "Usuarios Kanboard" se conecten a Kanboard

Ejemplo para  Microsoft Active Directory
--------------------------------------

Digamos que tenemos un dominio `KANBOARD` (kanboard.local) y el controlador principal es `myserver.kanboard.local`.

Primer ejemplo en modo proxie:

```php
<?php

// Habilitar autenticación LDAP  (false por default)
define('LDAP_AUTH', true);

define('LDAP_BIND_TYPE', 'proxy');
define('LDAP_USERNAME', 'administrator@kanboard.local');
define('LDAP_PASSWORD', 'my super secret password');

// Nombre del servidor LDAP
define('LDAP_SERVER', 'myserver.kanboard.local');

// LDAP propiedades
define('LDAP_USER_BASE_DN', 'CN=Users,DC=kanboard,DC=local');
define('LDAP_USER_FILTER', '(&(objectClass=user)(sAMAccountName=%s))');
```

Segundo ejemplo con el modo usuario:

```php
<?php

// Habilitar autenticación LDAP  (false por default)
define('LDAP_AUTH', true);

define('LDAP_BIND_TYPE', 'user');
define('LDAP_USERNAME', '%s@kanboard.local');
define('LDAP_PASSWORD', null);

// Nombre del servidor LDAP
define('LDAP_SERVER', 'myserver.kanboard.local');

// LDAP propiedades
define('LDAP_USER_BASE_DN', 'CN=Users,DC=kanboard,DC=local');
define('LDAP_USER_FILTER', '(&(objectClass=user)(sAMAccountName=%s))');
```

Ejemplo para OpenLDAP
--------------------

Nuestro servidor LDAP es `myserver.example.com` y todos los usuarios son almacenados bajo  `ou=People,dc=example,dc=com`.

Para este ejemplo utilizamos un enlace anónimo.


```php
<?php

// Habilitar autenticación LDAP  (false por default)
define('LDAP_AUTH', true);

//  Nombre del servidor LDAP
define('LDAP_SERVER', 'myserver.example.com');

// LDAP propiedades
define('LDAP_USER_BASE_DN', 'ou=People,dc=example,dc=com');
define('LDAP_USER_FILTER', 'uid=%s');
```

Habilitar creación de cuentas automáticas
-----------------------------------------


Por defecto, Kanboard creará una cuenta de usuario de forma automática si no se encuentra nada.

Solo cambiar el valor de `LDAP_ACCOUNT_CREATION` a `false`:

```php
// creación de cuenta automática
define('LDAP_ACCOUNT_CREATION', false);
```

Solución de problemas
-----------------------

### Restricciones de SELinux

Si SELinux está activado, tienes que permitir que Apache llegue a su servidor LDAP.

- Tu puedes cambiar SELinux a modo permisivo o deshabilitarlo (no recomendado)
- Tu puedes permitir todas las conexiones de red, por ejemplo `setsebool -P httpd_can_network_connect=1` o tener mas de una regla restricitiva

En cualquier caso tome como referencia la documentación official Redhat/Centos

### Habilitar el modo debug

Si no es capaz de configurar correctamente la autenticación LDAP, se puede [habilitar el modo debug ](config.markdown) y observar los logs.
