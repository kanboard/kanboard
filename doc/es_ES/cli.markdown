Interface de linea de comandos
=============================

Kanboard provides a simple command line interface that can be used from any Unix terminal.
This tool can be used only on the local machine.

This feature is useful to run commands outside of the web server processes.

Uso
-----

- Abre una terminal y ve a tu directorio de kanboard (ejemplo : `cd /var/www/kanboard`)
- Corre el comando `./cli` / `php cli`

```bash
Kanboard version master

Uso:
  comando [opciones] [argumentos]

Options:
  -h, --help            Visualizar mensaje de ayuda
  -q, --quiet           Sin mensajes de salida
  -V, --version         Visualiza la verssation version
      --ansi            Forzar salida ANSI
      --no-ansi         Deshabilitar salida ANSI
  -n, --no-interaction  No hacer cualquier pregunta interactiva
  -v|vv|vvv, --verbose  Incrementar la verbosidad de los mensajes: 1 para salida normal, 2 para mas salida verbosa y 3 para debug

Available commands:
  cronjob                            Ejecutar diariamente cronjob
  help                               Visualizar ayuda para los comandos
  list                               Lista de comandos
  worker                             Execute queue worker
 export
  export:daily-project-column-stats  Diariamente estad�sticas de exportaci�n de CSV (n�mero de tareas por columna y por d�a )
  export:subtasks                    Exportar a CSV las subtareas
  export:tasks                       Exportar a CSV las tareas
  export:transitions                 Exportar a CSV tareas de transici�n
 locale
  locale:compare                     Comparar aplicacion de traducciones con el locale fr_FR
  locale:sync                        Sincronizar todas las traducciones basadas en el locale fr_FR
 notification
  notification:overdue-tasks         Enviar notifiaciones de las tareas atrasadas
 plugin
  plugin:install                     Instalar un plugin desde un archivo ZIP remoto
  plugin:uninstall                   Eliminar plugin
  plugin:upgrade                     Actualizar todos los plugins instalados
 projects
  projects:daily-stats               Calcular diariamente las estadisticas para todos los proyectos
 trigger
  trigger:tasks                      Disparadores de eventos calendarizados para todas las tareas
 user
  user:reset-2fa                     Eliminar la autenticaci�n two-factor para un usuario
  user:reset-password                Cambiar el passwor del usuario
```

Comandos disponibles
------------------

### Exportar las tareas en CSV

Uso:

```bash
./cli export:tasks <project_id> <start_date> <end_date>
```

Ejemplo:

```bash
./cli export:tasks 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

CSV los datos son enviados a `stdout`.

### Subtasks CSV export

Uso:

```bash
./cli export:subtasks <project_id> <start_date> <end_date>
```

Ejemplo:

```bash
./cli export:subtasks 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

### Exportaci�n a CSV de tareas de transici�n

Uso:

```bash
./cli export:transitions <project_id> <start_date> <end_date>
```

Ejemplo:

```bash
./cli export:transitions 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

### Exportar diariamente resumenes de datos en CSV

Los datos exportados se pueden imprimir en la salida est�ndar:

```bash
./cli export:daily-project-column-stats <project_id> <start_date> <end_date>
```

Ejemplo:

```bash
./cli export:daily-project-column-stats 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

###  Envio de notificaciones para tareas atrasadas

Los Emails se enviaran a todos los usuarios con las notificaciones habilitadas.

```bash
./cli notification:overdue-tasks
```

Parametros opcionales:

- `--show`: Visualizar las notificaciones enviadas
- `--group`: Grupo tareas atrasadas todo para un usuario (desde todos los proyectos) en un email
- `--manager`: Enviar todas las tareas atrasadas a un project manager(s) en un email

Tambi�n puede mostrar las tareas atrasadas con la bandera `--show`:

```bash
./cli notification:overdue-tasks --show
+-----+---------+------------+------------+--------------+----------+
| Id  | Title   | Due date   | Project Id | Project name | Assignee |
+-----+---------+------------+------------+--------------+----------+
| 201 | Test    | 2014-10-26 | 1          | Project #0   | admin    |
| 202 | My task | 2014-10-28 | 1          | Project #0   |          |
+-----+---------+------------+------------+--------------+----------+
```

### Correr diariamente los calculos de estadisticas de los proyectos

Este comando calcula las estadisticas por cada proyecto:

```bash
./cli projects:daily-stats
Run calculation for Project #0
Run calculation for Project #1
Run calculation for Project #10
```

### Trigger [Disparadores] para tareas

Este comando envia a "daily cronjob event" a todas las tareas abiertas de cada proyecto.

```bash
./cli trigger:tasks
Trigger task event: project_id=2, nb_tasks=1
```

### Resetear el password del usuario

```bash
./cli user:reset-password my_user
```

Se le pedir� una contrase�a y la confirmaci�n. Los caracteres no se imprimen en la pantalla.

### Eliminar la autenticaci�n two-factor para un usuario

```bash
./cli user:reset-2fa my_user
```

### Instalar un plugin

```bash
./cli plugin:install https://github.com/kanboard/plugin-github-auth/releases/download/v1.0.1/GithubAuth-1.0.1.zip
```

Nota: Los archivos instalados tendr�n los mismos permisos que el usuario actual

### Eliminar un usuario

```bash
./cli plugin:uninstall Budget
```

### Upgrade todos los plugins

```bash
./cli plugin:upgrade
* Updating plugin: Budget Planning
* Plugin up to date: Github Authentication
```

### Run Background worker

```bash
./cli worker
```
