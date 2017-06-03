Gestión de bases de datos Sqlite
==========================


Kanboard utiliza Sqlite de forma predeterminada para almacenar sus datos.
Todas las tareas, proyectos y usuarios se almacenan dentro de esta base de datos.

Técnicamente, la base de datos es sólo un único archivo que se encuentra dentro del directorio ` data` y nombrado db.sqlite`.

Exportación / Copia de seguridad
-------------

### Linea de comando

Hacer una copia de seguridad es muy fácil, sólo tienes que copiar el archivo `data / db.sqlite` en otro lugar cuando nadie utilizar el software.

### Interfaz de usuario
También se puede descargar en cualquier momento la base de datos directamente desde la configuración **settings** menú.
La base de datos descargada está comprimida con Gzip, el nombre del archivo se convierte en `db.sqlite.gz`.


Importar/Restauracion
------------------

En realidad no hay manera de restaurar la base de datos de la interfaz de usuario.
La restauración debe hacerse manualmente cuando no se este utilizando el software.

- Para restaurar una copia de seguridad, solo reemplace el archivo actual `data/db.sqlite`.
- Para descomprimir una base de datos gzipped, ejecutar este comando desde un terminal `gunzip db.sqlite.gz`.

Optimización
------------

De vez en cuando, es posible optimizar el archivo de base de datos ejecutando el comando `VACUUM`.
Este comando reconstruir la base de datos y se puede utilizar por varias razones:

- Reducir el tamaño del archivo, la eliminación de datos que producen un espacio vacío, pero no cambia el tamaño del archivo.
- La base de datos está fragmentada debido a inserciones o actualizaciones frecuentes.

### Desde la linea de comando

```
sqlite3 data/db.sqlite 'VACUUM'
```

### Desde la interfaz de usuario

Ir al menu **settings** y dar click sobre el link **Optimize the database**.
Para mas informacion, leer [Sqlite documentation](https://sqlite.org/lang_vacuum.html).

