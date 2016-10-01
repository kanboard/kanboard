URL rewriting
=============

Kanboard es capaz de trabajar indistintamente con URL rewriting habilitado o no habilitado.

- Ejemplo URL rewritten: `/board/123`
- De otra manera: `?controller=board&action=show&project_id=123`

Is usas Kanboard con Apache y con el modo rewrite habilitado, las nice URLs podran usarse automaticamente.
En caso que objtentas un "404 Not Found", 
In case you get a "404 Not Found", puede que tenga que configurar al menos la siguiente overrides para tu DocumentRoot para obtener el archivo .htaccess trabajando:

```sh
<Directory /var/www/kanboard/>
	AllowOverride FileInfo Options=All,MultiViews AuthConfig
</Directory>
```

URL Shortcuts
-------------

- Ir a la tarea #123: **/t/123**
- Ir al tablero del proyecto #2: **/b/2**
- Ir al calendario del proyecto #5: **/c/5**
- Ir a la lista de las vistas del proyecto #8: **/l/8**
- Ir a la configuración de un proyecto id #42: **/p/42**

Configuración
-------------

Pode defecto, kanboard debera verificar si apache tiene habilitado el modo rewrite.

Para evitar la detección automática de URL rewriting desde el servidor web, se puede habiltiar la siguiete característica de tu archivo de configuración.

```php
define('ENABLE_URL_REWRITE', true);
```

Cuando esta constante esta en `true`:

- Las URLs generadas a partir de las herramientas de línea de comandos también se convertiran
- Si utiliza otro servidor web de Apache, por ejemplo Nginx o Microsoft IIS, tiene que configurar usted mismo la rewriting URL

Nota: Kanboard siempre vuelve a los URL de la vieja escuela cuando no está configurada, esta configuración es opcional.

Nginx ejemplo de configuración
------------------------------

En la sección `server` tu archivo de configuración de Nginx puedes usar este ejemplo:

```bash
index index.php;

location / {
    try_files $uri $uri/ /index.php$is_args$args;

    # If Kanboard is under a subfolder
    # try_files $uri $uri/ /kanboard/index.php;
}

location ~ \.php$ {
    try_files $uri =404;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_index index.php;
    include fastcgi_params;
}

# Deny access to the directory data
location ~* /data {
    deny all;
    return 404;
}

# Deny access to .htaccess
location ~ /\.ht {
    deny all;
    return 404;
}
```

en tu Kanboard `config.php`:

```php
define('ENABLE_URL_REWRITE', true);
```

Adaptar el ejemplo anterior de acuerdo a su propia configuración.

IIS ejemplo de configuración
-------------------------

1. Descargar e instalar el modulo Rewrite para IIS : [Download link](http://www.iis.net/learn/extensions/url-rewrite-module/using-the-url-rewrite-module)
2. Crear un web.config en tu folder de instalación:

```xml
<?xml version="1.0"?>
<configuration>
    <system.webServer>
        <defaultDocument>
            <files>
                <clear />
                <add value="index.php" />
            </files>
        </defaultDocument>
        <rewrite>
            <rules>
                <rule name="Kanboard URL Rewrite" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAll">
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" appendQueryString="true" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```

en tu Kanboard `config.php`:

```php
define('ENABLE_URL_REWRITE', true);
```

Adaptar el ejemplo anterior de acuerdo a su propia configuración.

