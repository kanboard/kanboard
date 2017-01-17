Интерфейс командной строки
==========================

Канборд обеспечивает простой интерфейс командной строки, которым можно воспользоваться только из Unix терминала. Эта возможность доступна только с локальной машины.

Интерфейс командной строки полезен для выполнения команд вне процессов веб сервера.

Использование[¶](#usage "Ссылка на этот заголовок")
---------------------------------------------------

-   Откройте терминал и перейдите в директорию Канборд (например: `cd /var/www/kanboard`)
-   Выполните команду `./cli`


```bash
Kanboard version master

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  cronjob                            Execute daily cronjob
  help                               Displays help for a command
  list                               Lists commands
  worker                             Execute queue worker
 db
  db:migrate                         Execute SQL migrations
  db:version                         Show database schema version
 export
  export:daily-project-column-stats  Daily project column stats CSV export (number of tasks per column and per day)
  export:subtasks                    Subtasks CSV export
  export:tasks                       Tasks CSV export
  export:transitions                 Task transitions CSV export
 locale
  locale:compare                     Compare application translations with the fr_FR locale
  locale:sync                        Synchronize all translations based on the fr_FR locale
 notification
  notification:overdue-tasks         Send notifications for overdue tasks
 plugin
  plugin:install                     Install a plugin from a remote Zip archive
  plugin:uninstall                   Remove a plugin
  plugin:upgrade                     Update all installed plugins
 projects
  projects:daily-stats               Calculate daily statistics for all projects
 trigger
  trigger:tasks                      Trigger scheduler event for all tasks
 user
  user:reset-2fa                     Remove two-factor authentication for a user
  user:reset-password                Change user password
```


Доступные команды[¶](#available-commands "Ссылка на этот заголовок")
--------------------------------------------------------------------

### Экспорт задач в формате CSV[¶](#tasks-csv-export "Ссылка на этот заголовок")

Применение:

    ./cli export:tasks <project_id> <start_date> <end_date>

Пример:

    ./cli export:tasks 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv

Данные CSV передаются в `stdout`.

### Экспорт подзадач в формате CSV[¶](#subtasks-csv-export "Ссылка на этот заголовок")

Применение:

    ./cli export:subtasks <project_id> <start_date> <end_date>

Пример:

    ./cli export:subtasks 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv

### Экспорт перемещения задач в формате CSV[¶](#task-transitions-csv-export "Ссылка на этот заголовок")

Применение:

    ./cli export:transitions <project_id> <start_date> <end_date>

Пример:

    ./cli export:transitions 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv

### Экспорт ежедневных сведений в формате CSV[¶](#export-daily-summaries-data-in-csv "Ссылка на этот заголовок")

Экспортированные данные будут выведены в стандартный вывод:

    ./cli export:daily-project-column-stats <project_id> <start_date> <end_date>

Пример:

    ./cli export:daily-project-column-stats 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv

### Отправка уведомлений для просроченных задач[¶](#send-notifications-for-overdue-tasks "Ссылка на этот заголовок")

Email сообщения будут отправлены всем пользователям, у которых включено оповещение.

    ./cli notification:overdue-tasks

Необязательные параметры:

-   `--show`: Показывать отправку уведомлений
-   `--group`: Группировать все просроченные задачи для одного пользователя (со всех проектов) на один email
-   `--manager`: Посылать все просроченные задачи менеджеру (менеджерам) проекта в одном email сообщении

Вы можете просмотреть просроченные задачи с помощью параметра `--show`:

```bash
./cli notification:overdue-tasks --show
+-----+---------+------------+------------+--------------+----------+
| Id  | Title   | Due date   | Project Id | Project name | Assignee |
+-----+---------+------------+------------+--------------+----------+
| 201 | Test    | 2014-10-26 | 1          | Project #0   | admin    |
| 202 | My task | 2014-10-28 | 1          | Project #0   |          |
+-----+---------+------------+------------+--------------+----------+
```

### Запуск ежедневной калькуляции статистики[¶](#run-daily-project-stats-calculation "Ссылка на этот заголовок")

Эта команда считает статистику для каждого проекта:

    ./cli projects:daily-stats
    Run calculation for Project #0
    Run calculation for Project #1
    Run calculation for Project #10

### Триггеры для задач[¶](#trigger-for-tasks)

Эта команда посылает “событие для ежедневных фоновых заданий” для всех открытых задач в каждом проекте.

    ./cli trigger:tasks
    Trigger task event: project_id=2, nb_tasks=1

### Сброс пароля пользователя[¶](#reset-user-password "Ссылка на этот заголовок")

    ./cli user:reset-password my_user

Будет запрошен пароль и подтверждение. Символы не отображаются на экране.

### Удаление двухуровневой аутентификации для пользователя[¶](#remove-two-factor-authentication-for-a-user "Ссылка на этот заголовок")

    ./cli user:reset-2fa my_user

### Установка плагина[¶](#install-a-plugin "Ссылка на этот заголовок")

    ./cli plugin:install https://github.com/kanboard/plugin-github-auth/releases/download/v1.0.1/GithubAuth-1.0.1.zip

Заметка: Установленные файлы будут иметь теже права, что и у текущего пользователя

### Удаление плагина[¶](#remove-a-plugin "Ссылка на этот заголовок")

    ./cli plugin:uninstall Budget

### Обновление всех плагинов[¶](#upgrade-all-plugins "Ссылка на этот заголовок")

    ./cli plugin:upgrade
    * Updating plugin: Budget Planning
    * Plugin up to date: Github Authentication

[Русская документация Kanboard](http://kanboard.ru/doc/)
