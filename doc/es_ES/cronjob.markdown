Programaci�n de Jobs en background
==================================

Para trabajar correctamente,Kanboard requiere que se corra un job en background diariamente.
Usualmente en plataformas Unix, este proceso se hace con `cron`.

Este job es necesario para estas caracteristicas:

- Reportes y analisis (calcular diariamente las estadisticas de cada proyecto)
- Enviar notificaciones de tareas atrasadas.
- Ejecutar automaticamente acciones conectadas a el evento "Daily background job for tasks"

Configuraci�n de plataformas Unix y Linux
-----------------------------------------

Hay varias maneras de definir una tarea programada en sistemas operativos Unix/Linux , este ejemplo es para Ubuntu 14.04 .
El procedimiento es similar a otros sistemas.

Editar el crontab del usuario de tu servidor web

```bash
sudo crontab -u www-data -e
```

Ejemplo para ejecutar diariamente cronjob a las 8am:

```bash
0 8 * * * cd /path/to/kanboard && ./cli cronjob >/dev/null 2>&1
```

Nota : El proceso del cronjob debe tener acceso a escritura de la base de datos en caso de usar Sqlite.
Usualmente, el cronjob corre bajo el usuario del servidor web. ** www-data , Ejemplo **
