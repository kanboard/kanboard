Web Hooks[¶](#web-hooks "Ссылка на этот заголовок")

===================================================



Webhooks служат для взаимодействия с внешними приложениями. Webhook посылает уведомление стороннему приложению о событиях, которые произошли в Канборд.



-   Webhooks могут быть использованы для создания задач вызовом простого URL (Вы можете сделать это и при помощи API)



-   Обращение к внешнему приложению может происходить автоматически, когда наступает какое-либо событие в Канборд (создана задача, обновлен комментарий и т.д.)



Как написать webhook приемник во внешнем приложении?[¶](#how-to-write-a-web-hook-receiver "Ссылка на этот заголовок")

---------------------------------------------------------------------------------------------------------------------



Все внутренние события в Канборде могут быть посланы во внешний URL.



-   Webhook URL (url приемник внешнего приложения) может быть задан в **Настройки** -\> **Webhooks** -\> **Webhook URL**



-   Когда в Канборде происходит событие, Канборд обращается к указанному URL автоматически



-   Данные конвертируются в формат JSON и передаются с помощью POST HTTP запроса



-   Webhook ключ передается в составе запроса в виде строкового параметра. Таким образом, вы можете проверить, что запрос на самом деле пришел из Канборда.



-   **Созданный вами URL должен среагировать в течении 1 секунды**. Это желательно сделать потому, что запросы являются синхронными (ограничения языка PHP) и возможны тормоза в пользовательском интерфейсе, если скрипт будет слишком медленный!



### Список поддерживаемых событий[¶](#list-of-supported-events "Ссылка на этот заголовок")



-   comment.create (комментарий.создать)



-   comment.update (комментарий.обновить)



-   file.create (файл.создать)



-   task.move.project (задача.переместить.проект)



-   task.move.column (задача.переместить.колонка)



-   task.move.position (задача.переместить.место)



-   task.move.swimlane (задача.переместить.дорожка)



-   task.update (задача.обновить)



-   task.create (задача.создать)



-   task.close (задача.закрыть)



-   task.open (задача.открыть)



-   task.assignee\_change (задача.назначить\_изменить)



-   subtask.update (подзадача.обновить)



-   subtask.create (подзадача.создать)



### Пример HTTP запроса[¶](#example-of-http-request "Ссылка на этот заголовок")



    POST https://your_webhook_url/?token=WEBHOOK_TOKEN_HERE

    User-Agent: Kanboard Webhook

    Content-Type: application/json

    Connection: close



    {

      "event_name": "task.move.column",

      "event_data": {

        "task_id": "1",

        "project_id": "1",

        "position": 1,

        "column_id": "1",

        "swimlane_id": "0",

        "src_column_id": "2",

        "dst_column_id": "1",

        "date_moved": "1431991532",

        "recurrence_status": "0",

        "recurrence_trigger": "0"

      }

    }



Функциональная часть всех событий имеет следующий формат:



    {

      "event_name": "model.event_name",

      "event_data": {

        "key1": "value1",

        "key2": "value2",

        ...

      }

    }



Значения `event_data`{.docutils .literal} могут быть неупорядочены в событиях.



### Пример функциональной части события[¶](#examples-of-event-payloads "Ссылка на этот заголовок")



Создание задачи:



    {

      "event_name": "task.create",

      "event_data": {

        "title": "Demo",

        "description": "",

        "project_id": "1",

        "owner_id": "1",

        "category_id": 0,

        "swimlane_id": 0,

        "column_id": "2",

        "color_id": "yellow",

        "score": 0,

        "time_estimated": 0,

        "date_due": 0,

        "creator_id": 1,

        "date_creation": 1431991532,

        "date_modification": 1431991532,

        "date_moved": 1431991532,

        "position": 1,

        "task_id": 1

      }

    }



Изменение задачи:



    {

      "event_name": "task.update",

      "event_data": {

        "id": "1",

        "title": "Demo",

        "description": "",

        "date_creation": "1431991532",

        "color_id": "yellow",

        "project_id": "1",

        "column_id": "1",

        "owner_id": "1",

        "position": "1",

        "is_active": "1",

        "date_completed": null,

        "score": "0",

        "date_due": "0",

        "category_id": "2",

        "creator_id": "1",

        "date_modification": 1431991603,

        "reference": "",

        "date_started": 1431993600,

        "time_spent": 0,

        "time_estimated": 0,

        "swimlane_id": "0",

        "date_moved": "1431991572",

        "recurrence_status": "0",

        "recurrence_trigger": "0",

        "recurrence_factor": "0",

        "recurrence_timeframe": "0",

        "recurrence_basedate": "0",

        "recurrence_parent": null,

        "recurrence_child": null,

        "task_id": "1",

        "changes": {

          "category_id": "2"

        }

      }

    }



События изменеия задачи имеют поле `changes`{.docutils .literal}, которое содержит обновленные значения.



Перемещение задачи в другую колонку:



    {

      "event_name": "task.move.column",

      "event_data": {

        "task_id": "1",

        "project_id": "1",

        "position": 1,

        "column_id": "1",

        "swimlane_id": "0",

        "src_column_id": "2",

        "dst_column_id": "1",

        "date_moved": "1431991532",

        "recurrence_status": "0",

        "recurrence_trigger": "0"

      }

    }



Перемещение задачи в другое место:



    {

      "event_name": "task.move.position",

      "event_data": {

        "task_id": "2",

        "project_id": "1",

        "position": 1,

        "column_id": "1",

        "swimlane_id": "0",

        "src_column_id": "1",

        "dst_column_id": "1",

        "date_moved": "1431996905",

        "recurrence_status": "0",

        "recurrence_trigger": "0"

      }

    }



Создание комментария:



    {

      "event_name": "comment.create",

      "event_data": {

        "id": 1,

        "task_id": "1",

        "user_id": "1",

        "comment": "test",

        "date_creation": 1431991615

      }

    }



Изменение комментария:



    {

      "event_name": "comment.update",

      "event_data": {

        "id": "1",

        "task_id": "1",

        "user_id": "1",

        "comment": "test edit"

      }

    }



Создание подзадачи:



    {

      "event_name": "subtask.create",

      "event_data": {

        "id": 3,

        "task_id": "1",

        "title": "Test",

        "user_id": "1",

        "time_estimated": "2",

        "position": 3

      }

    }



Изменение подзадачи:



    {

      "event_name": "subtask.update",

      "event_data": {

        "id": "1",

        "status": 1,

        "task_id": "1"

      }

    }



Загрузка файла:



    {

      "event_name": "file.create",

      "event_data": {

        "task_id": "1",

        "name": "test.png"

      }

    }



Создан снимок экрана:



    {

      "event_name": "file.create",

      "event_data": {

        "task_id": "2",

        "name": "Screenshot taken May 19, 2015 at 10:56 AM"

      }

    }



### [Оглавление](index.markdown)



-   [Web Hooks](#)

    -   [Как написать webhook приемник во внешнем приложении?](#how-to-write-a-web-hook-receiver)

        -   [Список поддерживаемых событий](#list-of-supported-events)

        -   [Пример HTTP запроса](#example-of-http-request)

        -   [Пример функциональной части события](#examples-of-event-payloads)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

