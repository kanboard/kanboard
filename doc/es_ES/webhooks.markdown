Webhooks
========

WebHooks son útiles para llevar a cabo acciones con aplicaciones externas.

- WebHooks se puede utilizar para crear una tarea llamando a un simple URL (También se puede hacer eso con la API)
- Una URL externa se puede llamar automáticamente cuando se produce un evento en Kanboard (creación de la tarea, Comentario actualizado, etc)

Cómo escribir un web hook?
---------------------------------

Todos los eventos internos de Kanboard se pueden enviar a una URL externa.

- La URL webhook tiene que ser definido en **Configuración > WebHooks> URL web hook**.
- Cuando se activa un evento Kanboard llama la URL predefinida automáticamente
- Los datos se codifican en formato JSON y se envían con una petición HTTP POST
- El token webhook también se envía como un parámetro de cadena de consulta, para que pueda comprobar si la solicitud proviene realmente de Kanboard.
- **La URL personalizada debe contestar en menos de 1 segundo**, esas peticiones son síncronas (limitación PHP) y que puede alentar la interfaz de usuario si el script es demasiado lento!

### Lista de eventos soportados

- comment.create
- comment.update
- comment.delete
- file.create
- task.move.project
- task.move.column
- task.move.position
- task.move.swimlane
- task.update
- task.create
- task.close
- task.open
- task.assignee_change
- subtask.update
- subtask.create
- subtask.delete
- task_internal_link.create_update
- task_internal_link.delete

### Ejemplo de una petición HTTP

```
POST https://your_webhook_url/?token=WEBHOOK_TOKEN_HERE
User-Agent: Kanboard Webhook
Content-Type: application/json
Connection: close

{
    "event_name": "task.move.column",
    "event_data": {
        "task_id": "4",
        "task": {
            "id": "4",
            "reference": "",
            "title": "My task",
            "description": "",
            "date_creation": "1469314356",
            "date_completed": null,
            "date_modification": "1469315422",
            "date_due": "1469491200",
            "date_started": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "color_id": "green",
            "project_id": "1",
            "column_id": "1",
            "owner_id": "1",
            "creator_id": "1",
            "position": "1",
            "is_active": "1",
            "score": "0",
            "category_id": "0",
            "priority": "0",
            "swimlane_id": "0",
            "date_moved": "1469315422",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "category_name": null,
            "swimlane_name": null,
            "project_name": "Demo Project",
            "default_swimlane": "Default swimlane",
            "column_title": "Backlog",
            "assignee_username": "admin",
            "assignee_name": null,
            "creator_username": "admin",
            "creator_name": null
        },
        "changes": {
            "src_column_id": "2",
            "dst_column_id": "1",
            "date_moved": "1469315398"
        },
        "project_id": "1",
        "position": 1,
        "column_id": "1",
        "swimlane_id": "0",
        "src_column_id": "2",
        "dst_column_id": "1",
        "date_moved": "1469315398",
        "recurrence_status": "0",
        "recurrence_trigger": "0"
    }
}
```

Todos los eventos payloads estan en el siguiente formato:

```json
{
  "event_name": "model.event_name",
  "event_data": {
    "key1": "value1",
    "key2": "value2",
    ...
  }
}
```

Los valores de `event_data` no son necesarios normalizados a través de eventos.

### Ejemplos de eventos payloads

Creación de tareas:

```json
{
    "event_name": "task.create",
    "event_data": {
        "task_id": 5,
        "task": {
            "id": "5",
            "reference": "",
            "title": "My new task",
            "description": "",
            "date_creation": "1469315481",
            "date_completed": null,
            "date_modification": "1469315481",
            "date_due": "0",
            "date_started": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "color_id": "orange",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "creator_id": "1",
            "position": "1",
            "is_active": "1",
            "score": "3",
            "category_id": "0",
            "priority": "2",
            "swimlane_id": "0",
            "date_moved": "1469315481",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "category_name": null,
            "swimlane_name": null,
            "project_name": "Demo Project",
            "default_swimlane": "Default swimlane",
            "column_title": "Ready",
            "assignee_username": "admin",
            "assignee_name": null,
            "creator_username": "admin",
            "creator_name": null
        }
    }
}
```

Modificación de tareas:

```json
{
    "event_name": "task.update",
    "event_data": {
        "task_id": "5",
        "task": {
            "id": "5",
            "reference": "",
            "title": "My new task",
            "description": "New description",
            "date_creation": "1469315481",
            "date_completed": null,
            "date_modification": "1469315531",
            "date_due": "1469836800",
            "date_started": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "color_id": "purple",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "creator_id": "1",
            "position": "1",
            "is_active": "1",
            "score": "3",
            "category_id": "0",
            "priority": "2",
            "swimlane_id": "0",
            "date_moved": "1469315481",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "category_name": null,
            "swimlane_name": null,
            "project_name": "Demo Project",
            "default_swimlane": "Default swimlane",
            "column_title": "Ready",
            "assignee_username": "admin",
            "assignee_name": null,
            "creator_username": "admin",
            "creator_name": null
        },
        "changes": {
            "description": "New description",
            "color_id": "purple",
            "date_due": 1469836800
        }
    }
}
```

eventos de actualización de tareas tienen un campo llamado `Cambios` que contiene los valores actualizados.

Creación de comentarios:

```json
{
    "event_name": "comment.create",
    "event_data": {
        "comment": {
            "id": "1",
            "task_id": "5",
            "user_id": "1",
            "date_creation": "1469315727",
            "comment": "My comment.",
            "reference": null,
            "username": "admin",
            "name": null,
            "email": null,
            "avatar_path": null
        },
        "task": {
            "id": "5",
            "reference": "",
            "title": "My new task",
            "description": "New description",
            "date_creation": "1469315481",
            "date_completed": null,
            "date_modification": "1469315531",
            "date_due": "1469836800",
            "date_started": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "color_id": "purple",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "creator_id": "1",
            "position": "1",
            "is_active": "1",
            "score": "3",
            "category_id": "0",
            "priority": "2",
            "swimlane_id": "0",
            "date_moved": "1469315481",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "category_name": null,
            "swimlane_name": null,
            "project_name": "Demo Project",
            "default_swimlane": "Default swimlane",
            "column_title": "Ready",
            "assignee_username": "admin",
            "assignee_name": null,
            "creator_username": "admin",
            "creator_name": null
        }
    }
}
```

Creación de subtareas:

```json
{
    "event_name": "subtask.create",
    "event_data": {
        "subtask": {
            "id": "1",
            "title": "My subtask",
            "status": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "task_id": "5",
            "user_id": "1",
            "position": "1",
            "username": "admin",
            "name": null,
            "timer_start_date": 0,
            "status_name": "Todo",
            "is_timer_started": false
        },
        "task": {
            "id": "5",
            "reference": "",
            "title": "My new task",
            "description": "New description",
            "date_creation": "1469315481",
            "date_completed": null,
            "date_modification": "1469315531",
            "date_due": "1469836800",
            "date_started": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "color_id": "purple",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "creator_id": "1",
            "position": "1",
            "is_active": "1",
            "score": "3",
            "category_id": "0",
            "priority": "2",
            "swimlane_id": "0",
            "date_moved": "1469315481",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "category_name": null,
            "swimlane_name": null,
            "project_name": "Demo Project",
            "default_swimlane": "Default swimlane",
            "column_title": "Ready",
            "assignee_username": "admin",
            "assignee_name": null,
            "creator_username": "admin",
            "creator_name": null
        }
    }
}
```

Subir archivos:

```json
{
    "event_name": "task.file.create",
    "event_data": {
        "file": {
            "id": "1",
            "name": "kanboard-latest.zip",
            "path": "tasks/5/6f32893e467e76671965b1ec58c06a2440823752",
            "is_image": "0",
            "task_id": "5",
            "date": "1469315613",
            "user_id": "1",
            "size": "4907308"
        },
        "task": {
            "id": "5",
            "reference": "",
            "title": "My new task",
            "description": "New description",
            "date_creation": "1469315481",
            "date_completed": null,
            "date_modification": "1469315531",
            "date_due": "1469836800",
            "date_started": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "color_id": "purple",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "creator_id": "1",
            "position": "1",
            "is_active": "1",
            "score": "3",
            "category_id": "0",
            "priority": "2",
            "swimlane_id": "0",
            "date_moved": "1469315481",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "category_name": null,
            "swimlane_name": null,
            "project_name": "Demo Project",
            "default_swimlane": "Default swimlane",
            "column_title": "Ready",
            "assignee_username": "admin",
            "assignee_name": null,
            "creator_username": "admin",
            "creator_name": null
        }
    }
}
```

Creación de links de tareas:

```json
{
    "event_name": "task_internal_link.create_update",
    "event_data": {
        "task_link": {
            "id": "2",
            "opposite_task_id": "5",
            "task_id": "4",
            "link_id": "3",
            "label": "is blocked by",
            "opposite_link_id": "2"
        },
        "task": {
            "id": "4",
            "reference": "",
            "title": "My task",
            "description": "",
            "date_creation": "1469314356",
            "date_completed": null,
            "date_modification": "1469315422",
            "date_due": "1469491200",
            "date_started": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "color_id": "green",
            "project_id": "1",
            "column_id": "1",
            "owner_id": "1",
            "creator_id": "1",
            "position": "1",
            "is_active": "1",
            "score": "0",
            "category_id": "0",
            "priority": "0",
            "swimlane_id": "0",
            "date_moved": "1469315422",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "category_name": null,
            "swimlane_name": null,
            "project_name": "Demo Project",
            "default_swimlane": "Default swimlane",
            "column_title": "Backlog",
            "assignee_username": "admin",
            "assignee_name": null,
            "creator_username": "admin",
            "creator_name": null
        }
    }
}
```
