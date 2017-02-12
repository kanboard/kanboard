Archivo de configuración
========================

Puede personalizar la configuración predeterminada de Kanboard mediante la adición de un archivo ` config.php` en la raíz del proyecto o en la carpeta ` data` .
También puede cambiar el nombre del archivo `config.default.php` a `config.php` y cambiar los valores deseados .

Habilitar/Deshabilitar el modo debug
--------------------------------------

```php
define('DEBUG', true);
define('LOG_DRIVER', 'file'); // Otros drivers son: syslog, stdout, stderr or file
```

El controlador de registro se debe definir si se habilita el modo de depuración .
El modo de depuración registra todas las consultas SQL y el tiempo necesario para generar páginas .

Plugins
-------

Folder de plugins:

```php
define('PLUGINS_DIR', 'data/plugins');
```

Enable/disable plugin de instalación para la interface de usuario:

```php
define('PLUGIN_INSTALLER', true); // Default es true
```

Folder para subir archivos
-------------------------

```php
define('FILES_DIR', 'data/files');
```

Enable/disable url rewrite
--------------------------

```php
define('ENABLE_URL_REWRITE', false);
```

Configuración de Email
-------------------

```php
// E-mail address para la cabecera "From" (notificaciones)
define('MAIL_FROM', 'notifications@kanboard.local');

// Mail transport para uso: "smtp", "sendmail" or "mail" (Funcion PHP mail)
define('MAIL_TRANSPORT', 'mail');

// Configuración SMTP para usarse para elegir el trasporte de "smtp"
define('MAIL_SMTP_HOSTNAME', '');
define('MAIL_SMTP_PORT', 25);
define('MAIL_SMTP_USERNAME', '');
define('MAIL_SMTP_PASSWORD', '');
define('MAIL_SMTP_ENCRYPTION', null); // Valid values are "null", "ssl" or "tls"

// Comando Sendmail para usarse cuando el trasporte sea "sendmail"
define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');
```

Configuración de base de datos
------------------------------

```php
// Driver base de datos: sqlite, mysql or postgres (sqlite por default)
define('DB_DRIVER', 'sqlite');

// Nombre de usuario Mysql/Postgres 
define('DB_USERNAME', 'root');

// Password Mysql/Postgres 
define('DB_PASSWORD', '');

// Mysql/Postgres hostname
define('DB_HOSTNAME', 'localhost');

// Mysql/Postgres Nombre de la base de datos
define('DB_NAME', 'kanboard');

// Mysql/Postgres Puerto personalizado (null = default port)
define('DB_PORT', null);

// Mysql SSL key
define('DB_SSL_KEY', null);

// Mysql SSL certificate
define('DB_SSL_CERT', null);

// Mysql SSL CA
define('DB_SSL_CA', null);
```

Configuraciones LDAP 
----------------------

```php
// Habilitar la autenticación por  LDAP (false por default)
define('LDAP_AUTH', false);

// LDAP server hostname
define('LDAP_SERVER', '');

// LDAP puerto del servidor (389 por defecto)
define('LDAP_PORT', 389);

// Por default, requiere certificados para verificacion para ldaps:// estilo URL. muesta false para saltarse la verificacion
define('LDAP_SSL_VERIFY', true);

// Enable LDAP START_TLS
define('LDAP_START_TLS', false);

// Por defecto Kanboard tiene el nombre de usuario LDAP en minúscula  para evitar usuarios duplicados ( la base de datos entre mayúsculas y minúsculas )
// Establece en true si desea conservar el caso
define('LDAP_USERNAME_CASE_SENSITIVE', false);

// LDAP tipo de enlace : "anonymous", "user" o "proxy"
define('LDAP_BIND_TYPE', 'anonymous');

// Nombre de usuario LDAP para utilizar con el modo de proxy
// Patrón de nombre de usuario LDAP para utilizar con el modo de usuario
define('LDAP_USERNAME', null);

// password LDAP para usar en modo proxy
define('LDAP_PASSWORD', null);

// LDAP DN para usuarios
// Ejemplo para ActiveDirectory: CN=Users,DC=kanboard,DC=local
// Ejemplo para OpenLDAP: ou=People,dc=example,dc=com
define('LDAP_USER_BASE_DN', '');

// LDAP pattern to use when searching for a user account
// Example for ActiveDirectory: '(&(objectClass=user)(sAMAccountName=%s))'
// Example for OpenLDAP: 'uid=%s'
define('LDAP_USER_FILTER', '');

// LDAP attribute for username
// Example for ActiveDirectory: 'samaccountname'
// Example for OpenLDAP: 'uid'
define('LDAP_USER_ATTRIBUTE_USERNAME', 'uid');

// LDAP attribute for user full name
// Example for ActiveDirectory: 'displayname'
// Example for OpenLDAP: 'cn'
define('LDAP_USER_ATTRIBUTE_FULLNAME', 'cn');

// LDAP attribute for user email
define('LDAP_USER_ATTRIBUTE_EMAIL', 'mail');

// LDAP attribute to find groups in user profile
define('LDAP_USER_ATTRIBUTE_GROUPS', 'memberof');

// LDAP attribute for user avatar image: thumbnailPhoto or jpegPhoto
define('LDAP_USER_ATTRIBUTE_PHOTO', '');

// LDAP attribute for user language, example: 'preferredlanguage'
// Put an empty string to disable language sync
define('LDAP_USER_ATTRIBUTE_LANGUAGE', '');

// Permitir creacion de usuario automatico LDAP
define('LDAP_USER_CREATION', true);

// LDAP DN para administradores
// Example: CN=Kanboard-Admins,CN=Users,DC=kanboard,DC=local
define('LDAP_GROUP_ADMIN_DN', '');

// LDAP DN para managers
// Example: CN=Kanboard Managers,CN=Users,DC=kanboard,DC=local
define('LDAP_GROUP_MANAGER_DN', '');

// Habilitiar proveedor LDAP de grupo para permisos de proyecto
// El usuario final será capaz de navegar por los grupos LDAP desde la interfaz de usuario y permitir el acceso a proyectos específicos
define('LDAP_GROUP_PROVIDER', false);

// LDAP Base DN for groups
define('LDAP_GROUP_BASE_DN', '');

// LDAP filtro de grupo
// Ejemplo para ActiveDirectory: (&(objectClass=group)(sAMAccountName=%s*))
define('LDAP_GROUP_FILTER', '');

// LDAP filtro por grupo de usuario
// Si se configura este filtro , Kanboard buscará grupos de usuarios en LDAP_GROUP_BASE_DN
// Example for OpenLDAP: (&(objectClass=posixGroup)(memberUid=%s))
define('LDAP_GROUP_USER_FILTER', '');

// LDAP atributo para los nombres de grupos
define('LDAP_GROUP_ATTRIBUTE_NAME', 'cn');
```

Reverse-Proxy configuración de autenticación
-------------------------------------

```php
// Enable/disable la autenticación reverse proxy
define('REVERSE_PROXY_AUTH', false);

// Nombre del header a utilizar para el nombre de usuario
define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');

// Nombre de usuario del administrador , por defecto en blanco
define('REVERSE_PROXY_DEFAULT_ADMIN', '');

// Dominio por defecto a utilizar para configurar la dirección de correo electrónico
define('REVERSE_PROXY_DEFAULT_DOMAIN', '');
```

Configuración para la autenticacion RememberMe 
----------------------------------------------

```php
// Enable/disable recuerdame autenticación
define('REMEMBER_ME_AUTH', true);
```

Secure HTTP configuracion de headers
-------------------------------------

```php
// Enable o disable "Strict-Transport-Security" HTTP header
define('ENABLE_HSTS', true);

// Enable o disable "X-Frame-Options: DENY" HTTP header
define('ENABLE_XFRAME', true);
```

Logging
-------

De forma predeterminada , Kanboard no ingrese nada .
Si desea habilitar el registro , usted tiene que fijar un controlador de registro.

```php
// log de drivers disponibles: syslog, stderr, stdout or file
define('LOG_DRIVER', '');

// Ingrese el nombre de archivo de registro si el driver "file"
define('LOG_FILE', __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'debug.log');
```

Protección de Brute-force 
---------------------

```php
// Habilitar captcha despues de 3 autenticaciones fallidas
define('BRUTEFORCE_CAPTCHA', 3);

// Bloquear la cuenta después de 6 autenticaciones fallidas
define('BRUTEFORCE_LOCKDOWN', 6);

// Bloquear la cuenta durante un minute
define('BRUTEFORCE_LOCKDOWN_DURATION', 15);
```

Session
-------

```php
// Session duration in second (0 = until the browser is closed)
// See http://php.net/manual/en/session.configuration.php#ini.session.cookie-lifetime
define('SESSION_DURATION', 0);
```

Clientes HTTP
--------------

Si las peticiones HTTP externas debe ser enviada a través de un proxy :

```php
define('HTTP_PROXY_HOSTNAME', '');
define('HTTP_PROXY_PORT', '3128');
define('HTTP_PROXY_USERNAME', '');
define('HTTP_PROXY_PASSWORD', '');
```

Para permitir que los certificados de firma propia :

```php
// Establece en false para permitir certficados self-signed
define('HTTP_VERIFY_SSL_CERTIFICATE', true);
```

Varias configuraciones
----------------------

```php
// Escapar de HTML dentro del texto de markdown
define('MARKDOWN_ESCAPE_HTML', true);

// Cabecera de autenticación alternativo API , el valor predeterminado es la autenticación básica HTTP definido en RFC2617
define('API_AUTHENTICATION_HEADER', '');

// Oculatar el formulario de login, usarlo si todos tus usuarios usan Google/Github/ReverseProxy authentication
define('HIDE_LOGIN_FORM', false);

// Desactivación de cierre de sesión ( SSO para la autenticación externa )
define('DISABLE_LOGOUT', false);

// Invalidar token de la API almacenada en la base de datos , útil para pruebas automatizadas
define('API_AUTHENTICATION_TOKEN', 'My unique API Token');
```
