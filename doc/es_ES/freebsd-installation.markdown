Instalacion en FreeBSD 
=======================

Instalación de paquetes
---------------------

```bash---terminal
$ pkg update
$ pkg upgrade
$ pkg install apache24 mod_php56 kanboard
```

Habilitar Apache en `/etc/rc.conf`;

```bash---terminal
$ echo apache24_enable="YES" >> /etc/rc.conf
```

Preparar PHP para Apache:

```bash---terminal
$ echo "AddType application/x-httpd-php .php" >> /usr/local/etc/apache24/Includes/php.conf
$ echo "DirectoryIndex index.php index.html" >> /usr/local/etc/apache24/Includes/php.conf
```

Iniciar Apache:

```bash---terminal
$ service apache24 start
```

Añadir enlace a la carpeta Kanboard en su docroot Apache:


```bash---terminal
cd /usr/local/www/apache24/data
ln -s /usr/local/www/kanboard
```

Ir a http://your.server.domain.tld/kanboard and enjoy!

*Notas*:
- Si deseas utilizar funciones adicionales como la integracion con LDAP, etc.
por favor instale el modulo PHP utilizando el paquete adecuado.
- Es posible que tenga que ejecutar los permisos de la carpeta de datos.


Instalacion de puertos
---------------------

Generalmente tres elementos tienen que instalarse:

- Apache
- mod_php for Apache
- Kanboard

Fetch y extraer puertos...

```bash---terminal
$ portsnap fetch
$ portsnap extract
```

o actualizacion ya existente:

```bash---terminal
$ portsnap fetch
$ portsnap update
```

Mas detalles con respecto a portsnap se puede encontrar en [FreeBSD Handbook](https://www.freebsd.org/doc/handbook/ports-using.html).

Instalacion de Apache:

```bash--terminal
$ cd /usr/ports/www/apache24
$ make install clean
```

Habilitar Apache en `/etc/rc.conf`:

```bash---terminal
$ echo apache24_enable="YES" >> /etc/rc.conf
```

Instalacion de  mod_php para Apachec:

```bash---terminal
$ cd /usr/ports/www/mod_php5
$ make install clean
```

Instalacion de los puertos de formulario para Kanboard

```bash---terminal
$ cd /usr/ports/www/kanboard
$ make install clean
```

Configuracion de PHP para Apache


```bash--terminal
$ echo "AddType application/x-httpd-php .php" >> /usr/local/etc/apache24/Includes/php.conf
$ echo "DirectoryIndex index.php index.html" >> /usr/local/etc/apache24/Includes/php.conf
```

Iniciando Apache:

```bash---terminal
$ service apache24 start
```

Ir a http://your.server.domain.tld/kanboard and enjoy!

*Nota*:
Si desea utilizar funciones adicionales como la intregacion con LDAP, etc.
Instale el modulo PHP adecuado de `lang/php5-extensions`.


Manual de instalacion
-------------------

Como en la version 1.0.16 Kanboard se puede encontrar en los puertos de FreeBSD no hay necesidad de instalarlo manualmente.


Tome nota por favor
--------------------

- El puerto esta alojado en [bitbucket](https://bitbucket.org/if0/freebsd-kanboard/). Sientase libre de comentar, y sugerir cambios !
- Algunas funciones de Kanboard requieren ejecutar[un trabajo en segundo plano todos los dias](cronjob.markdown).

