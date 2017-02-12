Autenticación por proxy inverso
============================

Este metodo de autenticación a menudo es usado por [SSO](http://en.wikipedia.org/wiki/Single_sign-on) (Single Sign-On) especialmente para organizaciones mayores.

La autenticación se realiza mediante otro sistema, Kanboard no conoce su contraseña y supongamos que ya está autenticado.

Requerimentos
------------

- Un proxy inverso bien configurado

o

- Apache Auth en el mismo servidor


¿Como funciona esto?
-------------------

1. Su proxy inverso autentica al usuario y envia el nombre de usuario a través de una cabecera HTTP.
2. Kanboard recuperar el nombre de usuario de la solicitud
    - El usuario se crea automáticamente si es necesario
    - Abrir una nueva sesión Kanboard sin ningún símbolo asumiendo que es válida

Instrucciones de instalación
----------------------------

### Configuración de su proxy inverso

Esto esta fuera del alcance de esta documentación.
Debería comprobar la conexión del usuario ya que es enviado por el proxy inverso utilizando una cabecera HTTP.

### Configuración de Kanboard

Crear un archivo `config.php`  copiar el archivo` config.default.php`:

```php
<?php

// Activar / desactivar la autenticación del proxy inverso
define('REVERSE_PROXY_AUTH', true); // Set this value to true

// La cabecera HTTP para recuperar. Si no se especifica, el valor por defecto es REMOTE_USER
define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');

// El Kanboard predeterminado esta el administrador para su organización.
// Ya que todo debe ser filtrada por el proxy inverso,
// Debe tener un usuario administrador para el arranque.
define('REVERSE_PROXY_DEFAULT_ADMIN', 'myadmin');

// El dominio predeterminado para asumir la dirección de correo electrónico.
// En caso de que el nombre de usuario no es una dirección de correo electrónico,
// Se actualizará automáticamente como USER@mydomain.com
define('REVERSE_PROXY_DEFAULT_DOMAIN', 'mydomain.com');
```

Notas:

- Si el proxy esta en el mismo servidor Web que ejecuta Kanboard, según la [CGI protocol](http://www.ietf.org/rfc/rfc3875) el Header será `REMOTE_USER`. Por ejemplo, Apache añadir `REMOTE_USER` por defecto si` Require valid-usuario de la red se establece.

- Si Apache es un proxy inverso a otro Apache corriendo Kanboard, la cabecera `REMOTE_USER` no se establece (mismo comportamiento con IIS y Nginx)

- Si tu tienes un autentico reverse proxy, the [HTTP ICAP draft](http://tools.ietf.org/html/draft-stecher-icap-subid-00#section-3.4) proposes the header to be `X-Authenticated-User`. This de facto standard has been adopted by a number of tools.
