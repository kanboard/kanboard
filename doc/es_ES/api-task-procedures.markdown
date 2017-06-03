API Procedimientos de Tarea
===================

## createTask

- Propósito: **Crea una nueva tarea**
- Parametros:
    - **title** (string, required)
    - **project_id** (integer, required)
    - **color_id** (string, optional)
    - **column_id** (integer, optional)
    - **owner_id** (integer, optional)
    - **creator_id** (integer, optional)
    - **date_due**: ISO8601 format (string, optional)
    - **description** Markdown content (string, optional)
    - **category_id** (integer, optional)
    - **score** (integer, optional)
    - **swimlane_id** (integer, optional)
    - **priority** (integer, optional)
    - **recurrence_status**  (integer, optional)
    - **recurrence_trigger**  (integer, optional)
    - **recurrence_factor**  (integer, optional)
    - **recurrence_timeframe**  (integer, optional)
    - **recurrence_basedate**  (integer, optional)
- Resultado en caso exitoso: **task_id**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createTask",
    "id": 1176509098,
    "params": {
        "owner_id": 1,
        "creator_id": 0,
        "date_due": "",
        "description": "",
        "category_id": 0,
        "score": 0,
        "title": "Test",
        "project_id": 1,
        "color_id": "green",
        "column_id": 2,
        "recurrence_status": 0,
        "recurrence_trigger": 0,
        "recurrence_factor": 0,
        "recurrence_timeframe": 0,
        "recurrence_basedate": 0
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1176509098,
    "result": 3
}
```

## getTask

- Propósito: **Obtiene tarea por el unico identificador**
- Parametros:
    - **task_id** (integer, required)
- Resultado en caso exitoso: **task properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getTask",
    "id": 700738119,
    "params": {
        "task_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 700738119,
    "result": {
        "id": "1",
        "title": "Task #1",
        "description": "",
        "date_creation": "1409963206",
        "color_id": "blue",
        "project_id": "1",
        "column_id": "2",
        "owner_id": "1",
        "position": "1",
        "is_active": "1",
        "date_completed": null,
        "score": "0",
        "date_due": "0",
        "category_id": "0",
        "creator_id": "0",
        "date_modification": "1409963206",
        "reference": "",
        "date_started": null,
        "time_spent": "0",
        "time_estimated": "0",
        "swimlane_id": "0",
        "date_moved": "1430875287",
        "recurrence_status": "0",
        "recurrence_trigger": "0",
        "recurrence_factor": "0",
        "recurrence_timeframe": "0",
        "recurrence_basedate": "0",
        "recurrence_parent": null,
        "recurrence_child": null,
        "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=1&project_id=1",
        "color": {
            "name": "Yellow",
            "background": "rgb(245, 247, 196)",
            "border": "rgb(223, 227, 45)"
        }
    }
}
```

## getTaskByReference

- Propósito: **Obtiene tarea por la referencia externa**
- Parametros:
    - **project_id** (integer, required)
    - **reference** (string, required)
- Resultado en caso exitoso: **task properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getTaskByReference",
    "id": 1992081213,
    "params": {
        "project_id": 1,
        "reference": "TICKET-1234"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1992081213,
    "result": {
        "id": "5",
        "title": "Task with external ticket number",
        "description": "[Link to my ticket](http:\/\/my-ticketing-system\/1234)",
        "date_creation": "1434227446",
        "color_id": "yellow",
        "project_id": "1",
        "column_id": "1",
        "owner_id": "0",
        "position": "4",
        "is_active": "1",
        "date_completed": null,
        "score": "0",
        "date_due": "0",
        "category_id": "0",
        "creator_id": "0",
        "date_modification": "1434227446",
        "reference": "TICKET-1234",
        "date_started": null,
        "time_spent": "0",
        "time_estimated": "0",
        "swimlane_id": "0",
        "date_moved": "1434227446",
        "recurrence_status": "0",
        "recurrence_trigger": "0",
        "recurrence_factor": "0",
        "recurrence_timeframe": "0",
        "recurrence_basedate": "0",
        "recurrence_parent": null,
        "recurrence_child": null,
        "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=5&project_id=1"
    }
}
```

## getAllTasks

- Propósito: **Obtiene todas las tareas disponibles**
- Parametros:
    - **project_id** (integer, required)
    - **status_id**: The value 1 for active tasks and 0 for inactive (integer, required)
- Resultado en caso exitoso: **List of tasks**
- Resultado en caso fallido: **false**

Ejemplo de petición to fetch all tasks on the board:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllTasks",
    "id": 133280317,
    "params": {
        "project_id": 1,
        "status_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": [
        {
            "id": "1",
            "title": "Task #1",
            "description": "",
            "date_creation": "1409961789",
            "color_id": "blue",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "position": "1",
            "is_active": "1",
            "date_completed": null,
            "score": "0",
            "date_due": "0",
            "category_id": "0",
            "creator_id": "0",
            "date_modification": "1409961789",
            "reference": "",
            "date_started": null,
            "time_spent": "0",
            "time_estimated": "0",
            "swimlane_id": "0",
            "date_moved": "1430783191",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=1&project_id=1"
        },
        {
            "id": "2",
            "title": "Test",
            "description": "",
            "date_creation": "1409962115",
            "color_id": "green",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "position": "2",
            "is_active": "1",
            "date_completed": null,
            "score": "0",
            "date_due": "0",
            "category_id": "0",
            "creator_id": "0",
            "date_modification": "1409962115",
            "reference": "",
            "date_started": null,
            "time_spent": "0",
            "time_estimated": "0",
            "swimlane_id": "0",
            "date_moved": "1430783191",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=2&project_id=1"
        },
        ...
    ]
}
```

## getOverdueTasks

- Propósito: **Obtiene todas las tareas atrasadas**
- Resultado en caso exitoso: **List of tasks**
- Resultado en caso fallido: **false**

Ejemplo de petición to fetch all tasks on the board:

```json
{
    "jsonrpc": "2.0",
    "method": "getOverdueTasks",
    "id": 133280317
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": [
        {
            "id": "1",
            "title": "Task #1",
            "date_due": "1409961789",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        {
            "id": "2",
            "title": "Test",
            "date_due": "1409962115",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        ...
    ]
}
```

## getOverdueTasksByProject

- Propósito: **Obtiene todas las tareas atrasadas para un proyecto especial**
- Resultado en caso exitoso: **List of tasks**
- Resultado en caso fallido: **false**

Ejemplo de petición to fetch all tasks on the board:

```json
{
    "jsonrpc": "2.0",
    "method": "getOverdueTasksByProject",
    "id": 133280317,
    "params": {
        "project_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": [
        {
            "id": "1",
            "title": "Task #1",
            "date_due": "1409961789",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        {
            "id": "2",
            "title": "Test",
            "date_due": "1409962115",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        ...
    ]
}
```

## updateTask

- Propósito: **Actualiza una tarea**
- Parametros:
    - **id** (integer, required)
    - **title** (string, optional)
    - **color_id** (string, optional)
    - **owner_id** (integer, optional)
    - **date_due**: ISO8601 format (string, optional)
    - **description** Markdown content (string, optional)
    - **category_id** (integer, optional)
    - **score** (integer, optional)
    - **priority** (integer, optional)
    - **recurrence_status**  (integer, optional)
    - **recurrence_trigger**  (integer, optional)
    - **recurrence_factor**  (integer, optional)
    - **recurrence_timeframe**  (integer, optional)
    - **recurrence_basedate**  (integer, optional)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición to change the task color:

```json
{
    "jsonrpc": "2.0",
    "method": "updateTask",
    "id": 1406803059,
    "params": {
        "id": 1,
        "color_id": "blue"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1406803059,
    "result": true
}
```

## openTask

- Propósito: **Establece una tarea en estado abierto**
- Parametros:
    - **task_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "openTask",
    "id": 1888531925,
    "params": {
        "task_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1888531925,
    "result": true
}
```

## closeTask

- Propósito: **Establece una tarea en estado cerrado**
- Parametros:
    - **task_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "closeTask",
    "id": 1654396960,
    "params": {
        "task_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1654396960,
    "result": true
}
```

## removeTask

- Propósito: **Elimina una tarea**
- Parametros:
    - **task_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeTask",
    "id": 1423501287,
    "params": {
        "task_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1423501287,
    "result": true
}
```

## moveTaskPosition

- Propósito: **Mueve una tarea a otra columna, posición o diagrama dentro del mismo tablero**
- Parametros:
    - **project_id** (integer, required)
    - **task_id** (integer, required)
    - **column_id** (integer, required)
    - **position** (integer, required)
    - **swimlane_id** (integer, optional, default=0)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "moveTaskPosition",
    "id": 117211800,
    "params": {
        "project_id": 1,
        "task_id": 1,
        "column_id": 2,
        "position": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 117211800,
    "result": true
}
```

## moveTaskToProject

- Propósito: **Mueve una tarea a otro proyecto**
- Parametros:
    - **task_id** (integer, required)
    - **project_id** (integer, required)
    - **swimlane_id** (integer, optional)
    - **column_id** (integer, optional)
    - **category_id** (integer, optional)
    - **owner_id** (integer, optional)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "moveTaskToProject",
    "id": 15775829,
    "params": [
        4,
        5
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 15775829,
    "result": true
}
```

## duplicateTaskToProject

- Propósito: **Mueve una tarea a otra columna u otra posición**
- Parametros:
    - **task_id** (integer, required)
    - **project_id** (integer, required)
    - **swimlane_id** (integer, optional)
    - **column_id** (integer, optional)
    - **category_id** (integer, optional)
    - **owner_id** (integer, optional)
- Resultado en caso exitoso: **task_id**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "duplicateTaskToProject",
    "id": 1662458687,
    "params": [
        5,
        7
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1662458687,
    "result": 6
}
```

## searchTasks

- Propósito: **Encuentra tareas utilizando el motor de busqueda**
- Parametros:
    - **project_id** (integer, required)
    - **query** (string, required)
- Resultado en caso exitoso: **list of tasks**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "searchTasks",
    "id": 1468511716,
    "params": {
        "project_id": 2,
        "query": "assignee:nobody"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1468511716,
    "result": [
        {
            "nb_comments": "0",
            "nb_files": "0",
            "nb_subtasks": "0",
            "nb_completed_subtasks": "0",
            "nb_links": "0",
            "nb_external_links": "0",
            "is_milestone": null,
            "id": "3",
            "reference": "",
            "title": "T3",
            "description": "",
            "date_creation": "1461365164",
            "date_modification": "1461365164",
            "date_completed": null,
            "date_started": null,
            "date_due": "0",
            "color_id": "yellow",
            "project_id": "2",
            "column_id": "5",
            "swimlane_id": "0",
            "owner_id": "0",
            "creator_id": "0"
            // ...
         }
    ]
}
```

## getTaskMetadata

- Propósito: **Obtiene todos los metadatos relacionados a una tarea por el identificador unico de la tarea**
- Parametros:
    - **task_id** (integer, required)
- Resultado en caso exitoso: **list of metadata**
- Resultado en caso fallido: **empty array**

Ejemplo de petición to fetch all the metada of a task:

```json
{
    "jsonrpc": "2.0",
    "method": "getTaskMetadata",
    "id": 133280317,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": [
        {
            "metaKey1": "metaValue1",
            "metaKey2": "metaValue2",
            ...
        }
    ]
}
```

## getTaskMetadataByName

- Propósito: **Obtiene los metadatos relacionados a una tarea por el identificador unico de la tarea y la metaclave (nombre)**
- Parametros:
    - **task_id** (integer, required)
    - **name** (string, required)
- Resultado en caso exitoso: **metadata value**
- Resultado en caso fallido: **empty string**

Ejemplo de petición to fetch metada of a task by name:

```json
{
    "jsonrpc": "2.0",
    "method": "getTaskMetadataByName",
    "id": 133280317,
    "params": [
        1,
        "metaKey1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": "metaValue1"
}
```

## saveTaskMetadata

- Propósito: **Guarda/actualiza los metadatos de la tarea**
- Parametros:
    - **task_id** (integer, required)
    - **array("name" => "value")** (array, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición to add/update metada of a task:

```json
{
    "jsonrpc": "2.0",
    "method": "saveTaskMetadata",
    "id": 133280317,
    "params": [
        1,
        {
            "metaName" : "metaValue"
        }
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": true
}
```

## removeTaskMetadata

- Propósito: **Elimina los metadatos de la tarea por nombre**
- Parametros:
    - **task_id** (integer, required)
    - **name** (string, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición to remove metada of a task by name:

```json
{
    "jsonrpc": "2.0",
    "method": "removeTaskMetadata",
    "id": 133280317,
    "params": [
        1,
        "metaKey1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": true
}
```
