Instalación

En primer lugar, comprobar los [requisitos] ( requirements.markdown)antes de ir más lejos.

Del archivo (versión estable)
---------------------------------


1. Debe tener un servidor web con PHP instalado
2. Descargar el código fuente y copiar el directorio ` kanboard` en la que desea.
3. Comprobar si el directorio ` data` se puede escribir
4. Con el navegador vaya a < http://yourpersonalserver/kanboard >
5. El nombre de usuario y contraseña por defecto es **admin/admin**
6. Comience a utilizar el software
7. No se olvide de cambiar su contraseña!


La carpeta de datos se utiliza para almacenar:

- Base de datos SQLite: ` db.sqlite`
- Archivo de depuración: ` debug.log` (si está activado el modo de depuración)
- Archivos cargados: ` ficheros / *`
- miniaturas de imagen: ` ficheros / miniaturas / *`

Las personas que están utilizando una base de datos remota (MySQL / PostgreSQL) y un almacenamiento de archivos a distancia (AWS S3 o similar) no necesariamente tienen que tener una carpeta de datos local persistente o para cambiar el permiso.

Desde el repositorio (versión de desarrollo)
-----------------------------------------


Debe instalar [compositora] (https://getcomposer.org/)para utilizar este método.

1. ` git clone https: // github.com / kanboard / kanboard.git`
2. ` composer instalar --no- dev`
3. Ir a la tercera etapa justo por encima

Nota: Este método se instalará la versión de **desarrollo actual** , utilice a su propio riesgo .

Instalación exterior de la raíz del documento
---------------------------------------------

Si desea instalar Kanboard fuera de la raíz de documentos del servidor web, es necesario crear al menos estos enlaces simbólicos:

```bash
.
├── assets -> ../kanboard/assets
├── cli -> ../kanboard/cli
├── doc -> ../kanboard/doc
├── favicon.ico -> ../kanboard/favicon.ico
├── index.php -> ../kanboard/index.php
├── jsonrpc.php -> ../kanboard/jsonrpc.php
└── robots.txt -> ../kanboard/robots.txt
```

El ` .htaccess` es opcional, ya que su contenido puede ser incluido directamente en la configuración de Apache.

También puede definir una ubicación personalizada para los plugins y carpetas de archivos cambiando el [archivo de configuración] (config.markdown).

La instalación opcional

---------------------

- Algunas funciones de Kanboard requieren que ejecuta [un trabajo en segundo plano todos los días] (cronjob.markdown) (Informes y análisis)
- [Instalar el trabajador fondo] (worker.markdown) para mejorar las prestaciones

Seguridad
---------


- No se olvide de cambiar el usuario por defecto / contraseña
- No permita a todo el mundo para acceder al directorio ` data` de la URL. 
Ya existe un ` .htaccess` para Apache , pero nada para los demás servidores web.
