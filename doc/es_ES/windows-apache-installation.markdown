Instalación en Windows Server y Apache
=========================================

Esta guia ayudara paso por paso para la configuracion de Kanboard en un servidor Windows con Apache y PHP.

Nota: si usted tiene un plataforma de 64 bits elegir "x64" de lo contrario seleccione "x86" para sistemas de 32 bits.

Visual C++ instalación redistribuible
---------------------------------------

PHP y Apache son compilados con Visual Studio por lo que necesita instalar esta biblioteca si no está ya hecho.

1. Descargue la libreria desde [página oficial de Microsoft](http://www.microsoft.com/en-us/download/details.aspx?id=30679)
2. Ejecute el instalador `vcredist_x64.exe` o `vcredist_x86.exe` de acuerdo a su plataforma

instlación de Apache 
-------------------

1. Descargar binario de Apache[Apache Lounge](http://www.apachelounge.com/download/)
2. Descomprimir la carpeta de Apache24 `C:\Apache24`

### Definir el nombre del servidor

Abra el archivo `C:\Apache24\conf\httpd.conf` y añada la directiva:

```
ServerName localhost
```

### Instalar el servicio de Apache

Abrir un simbolo de sistema (`cmd.exe`) e ir al directorio `C:\Apache24\bin`:

```bash---terminal
cd C:\Apache24\bin

# Instalar el servicio de Windows
httpd.exe -k install
```

### Instalar ApacheMonitor

- Doble click en `C:\Apache24\bin\ApacheMonitor.exe`, o ponerlo en la carpeta de inicio.
- Haga clic derecho sobre el icono y comenzar Apache

### Checar la instalacion de Apache

Ir a http://localhost/ debería ver una página en blanco con el texto "It works!".

Instalación de PHP 
----------------

1. descargar la última versión estable de PHP desde la [ Pagina oficial de PHP ](http://windows.php.net/download/), elegir el **Thread Safe** y utilizar la versión exacta del mismo tipo de construcción como Apache: x86 o x64
2. Descomprimir los archivos`C:\php`
3. Vaya a la carpeta PHP cambie el nombre del archivo `php.ini-production` a `php.ini`

Editar el `php.ini`:

directorio de la extensión eliminar comentario:

```ini
extension_dir = "C:/php/ext"
```

Elimine el comentario de estos módulos de PHP:

```ini
extension=php_gd2.dll
extension=php_ldap.dll
extension=php_mbstring.dll
extension=php_openssl.dll
extension=php_pdo_sqlite.dll
```

Establecer la zona horaria:

```ini
date.timezone = America/Montreal
```

La lista de zonas horarias admitidas se puede encontrar en el [la documentacion de PHP ](http://php.net/manual/en/timezones.america.php).

Cargar el módulo de PHP para Apache:

Añadir esta configuración en el archivo `C:\Apache24\conf\httpd.conf`:

```
LoadModule php5_module "c:/php/php5apache2_4.dll"
AddHandler application/x-httpd-php .php

# configurar la ruta de acceso a php.ini
PHPIniDir "C:/php"

# cambiar esta directiva
DirectoryIndex index.php index.html
```

Reiniciar Apache.

Compruebe la instalación de PHP:

Cree un archivo llamado `phpinfo.php` en el folder `C:\Apache24\htdocs`:

```php
<?php

phpinfo();

?>
```
Ir a http://localhost/phpinfo.php y debe ver toda la información acerca de la instalación de PHP.

Instalación de Kanboard 
---------------------

- Descarga el archivo zip
- Descomprimir el archivo en `C:\Apache24\htdocs\kanboard` por ejemplo,
- Abra su navegador para poder usar Kanboard http://localhost/kanboard/
- Las credenciales predeterminadas son **admin/admin**


configuración probada
--------------------

- Windows 2008 R2 / Apache 2.4.12 / PHP 5.6.8

Nota:
-----
- Algunas funciones de Kanboard requieren ejecutar [un trabajo en segundo plano todos los días] (cronjob.markdown).
