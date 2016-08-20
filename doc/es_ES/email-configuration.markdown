Configuración del Email 
=======================

Configuración de usuarios 
-------------------------

Para recibir notificaciones por email los usuarios de Kanboard deben tener

- Activar las notificaciones de su perfil
- Tener una dirección valida de email en su perfil
- Ser miembro del proyecto y que este tenga activo la opción de notificaciones

Nota: El usuario que genera una sesión y que realiza alguna acción no recibe ninguna notificación, sólo otros miembros del proyecto.

Comunicación con correos electronicos
-------------------------------------

There are several email transports available:

- SMTP
- Sendmail
- PHP mail funcion nativa
- Otros métodos que pueden ser proporcionados por  externos : Postmark, Sendgrid and Mailgun

Configuración del servidor
--------------------------

Por default, Kanboard usa el  bundled PHP mail function para el envio de emails.
Porque usualmente el servidor no requiere una configuración y así tu servidor puede estar listo para enviar emails.

Sin embargo, es posible usar otros metodos, como el protocolo SMTP  y Sendmail

###  Configuración SMTP

Renombrar el archivo `config.default.php` a `config.php` y modificar estos valores:

```php
// We choose "smtp" as mail transport
define('MAIL_TRANSPORT', 'smtp');

// We define our server settings
define('MAIL_SMTP_HOSTNAME', 'mail.example.com');
define('MAIL_SMTP_PORT', 25);

// Credentials for authentication on the SMTP server (not mandatory)
define('MAIL_SMTP_USERNAME', 'username');
define('MAIL_SMTP_PASSWORD', 'super password');
```

También es posible utilizar una conexión segura, TLS or SSL:

```php
define('MAIL_SMTP_ENCRYPTION', 'ssl'); // Valid values are "null", "ssl" or "tls"
```

### Configuración Sendmail 

Por default el comando para el sendmail esta `/usr/sbin/sendmail -bs` Pero usted puede personalizarlo en su archivo de configuración.

Ejemplo:

```php
// We choose "sendmail" as mail transport
define('MAIL_TRANSPORT', 'sendmail');

// If you need to change the sendmail command, replace the value
define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');
```

### PHP funcion nativa de email

Esta es la configuración por default

```php
define('MAIL_TRANSPORT', 'mail');
```

### La dirección de correo electrónico del remitente

Por default, los correos electrónicos utilizarán la dirección del remitente `notifications@kanboard.local`.
con este correo no es posible responderle

Tu puedes personalizar esta direccion cambiando el valor de la constante `MAIL_FROM` en tu archivo de configuración

```php
define('MAIL_FROM', 'kanboard@mydomain.tld');
```

Esto puede ser útil si su configuracion del servidor SMTP no acepta una dirección por default.

### Cómo mostrar un enlace a la tarea en las notificaciones ?

Para hacer eso, tu tienes que especificar la URL de tu instalación de tu kanboard [Application Settings](https://kanboard.net/documentation/application-configuration).

De manera predeterminada, no se define nada, por lo que no se mostrará los enlaces.

Ejemplos :

- http://demo.kanboard.net/
- http://myserver/kanboard/
- http://kanboard.mydomain.com/

No se olvide de la barra final `/`.

Es necesario definir de forma manual debido a que Kanboard no puede adivinar la dirección URL de una secuencia de comandos de línea de comandos y algunas personas tienen una configuración muy específica.

Solución de problemas
---------------------

Si no hay mensajes de correo electrónico se envían y que está seguro de que todo está configurado correctamente entonces:

- Verificar el correo de spam
- Habilita el modo debug y verifique el archivo `data/debug.log`, Debería ver el error exacto
- Asegúrese de que el servidor o el proveedor de alojamiento le permite enviar mensajes de correo electrónico
- Si usa Selinux Permitir a PHP enviar emails
