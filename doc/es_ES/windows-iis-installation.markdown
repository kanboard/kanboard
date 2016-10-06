La instalación en Windows 2008/2012 con IIS
==========================================

Esta guía le ayudará paso a paso para la configuración de Kanboard en un servidor Windows con IIS y PHP.

PHP Instalacion
----------------

- Instalar IIS en el servidor (Añadir un nuevo papel y no se olvide de permitir CGI / FastCGI)
- Instalar PHP siguiendo la documentación oficial:

    - [Microsoft IIS 5.1 y IIS 6.0](http://php.net/manual/en/install.windows.iis6.php)
    - [Microsoft IIS 7.0 y later](http://php.net/manual/en/install.windows.iis7.php)
    - [PHP para Windows está disponible aquí](http://windows.php.net/download/)


### PHP.ini

Necesita al menos, estas extensiones en su`php.ini`:

```ini
extension=php_gd2.dll
extension=php_ldap.dll
extension=php_mbstring.dll
extension=php_openssl.dll
extension=php_pdo_sqlite.dll
```

La lista completa de las extensiones PHP necesarias están disponibles en el[La página de requisitos](requirements.markdown)

No se olvide de establecer la zona horaria:

```ini
date.timezone = America/Montreal
```

La lista de zonas horarias admitidas se puede encontrar en el[PHP documentacion](http://php.net/manual/en/timezones.america.php).

Notas:


- Si utiliza PHP <5.4, tiene que habilitar las etiquetas cortas en su php.ini
- No se olvide de activar las extensiones php necesarias antes mencionados
- Si tienes un error acerca de "la biblioteca MSVCP110.dll falta", es probable que tenga que descargar el Visual C ++ Redistributable para Visual Studio desde el sitio web de Microsoft.

IIS Modulos
-----------

El archivo contiene un Kanboard `web.config` archivo para activar [la reescritura de URL](nice-urls.markdown). 
Esta configuración requiere la [módulo de reescritura para IIS](http://www.iis.net/learn/extensions/url-rewrite-module/using-the-url-rewrite-module).


Si usted no tiene el módulo de reescritura, obtendrá un error interno del servidor (500) de IIS.
Si no quiere tener Kanboard con buenas direcciones URL, puede eliminar el archivo`web.config`.

Kanboard Instalacion
---------------------

- Descarga el archivo zip
- Descomprimir el archivo en `C:\inetpub\wwwroot\kanboard` por ejemplo,
- Asegúrese de que el directorio `data` es modificable por el usuario de IIS
- Abra su navegador para poder usar Kanboard http://localhost/kanboard/
- Las credenciales predeterminadas son **admin/admin**
- [URL de configuración de reescritura] (nice-urls.markdown)

Nota
-----

- Algunas funciones de Kanboard requieren ejecutar [un trabajo en segundo plano todos los días] (cronjob.markdown).

