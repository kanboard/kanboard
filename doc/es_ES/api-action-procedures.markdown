API Procedimiento de acciones automaticas
================================

## getAvailableActions [Obtener acciones disponibles]

- Propósito: **Obtener una lista de acciones automaticas disponibles**
- Parametros: ninguno
- Resultado satisfactorio: **list of actions**
- Resultado fallido: **falso**

Ejemplo de solicitud:

```json
{
    "jsonrpc": "2.0",
    "method": "getAvailableActions",
    "id": 1217735483
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1217735483,
    "result": {
        "\Kanboard\Action\TaskLogMoveAnotherColumn": "Agregar un comentario moviendo las tareas entre columnas",
        "\Kanboard\Action\TaskAssignColorUser": "Asignar un color especifico aun usuario",
        "\Kanboard\Action\TaskAssignColorColumn": "Asignar un color cuando la tarea es movida a una columna especifica",
        "\Kanboard\Action\TaskAssignCategoryColor": "Asignar automaticamente una categoria basado en un color",
        "\Kanboard\Action\TaskAssignColorCategory": "Asignar automaticamente un color basado en una categoria",
        "\Kanboard\Action\TaskAssignSpecificUser": "Asigar tareas a un usuario especifico",
        "\Kanboard\Action\TaskAssignCurrentUser": "Asignar tareas a la persona que hace la acción",
        "\Kanboard\Action\TaskUpdateStartDate": "Automaticamente actualizar la fecha de inicio",
        "\Kanboard\Action\TaskAssignUser": "Cambiar asigando basado en un nombre de usuario [username] externo",
        "\Kanboard\Action\TaskAssignCategoryLabel": "Cambiar la categoria basado en un etiqueta externa",
        "\Kanboard\Action\TaskClose": "Cerrar una tarea",
        "\Kanboard\Action\CommentCreation": "Crear un comentario desde un proveedor externo",
        "\Kanboard\Action\TaskCreation": "Crear una tarea desde un proveedor externo",
        "\Kanboard\Action\TaskDuplicateAnotherProject": "Duplicar la tarea a otro proyecto",
        "\Kanboard\Action\TaskMoveColumnAssigned": "Mover la tarea a otra columna cuando es asiganada a un usuario",
        "\Kanboard\Action\TaskMoveColumnUnAssigned": "Mover la tarea a otra columna cuando la asignación es limpiada",
        "\Kanboard\Action\TaskMoveAnotherProject": "Mover la tarea a otro proyecto",
        "\Kanboard\Action\TaskOpen": "Abrir una Tarea"
    }
}
```

## getAvailableActionEvents [obtener acciones de eventos disponibles]

- Propósito: **Obtener una lista de acciones disponibles para los eventos**
- Parametros: ninguno
- Resultado satisfactorio: **lista de eventos**
- Resultado fallído : **falso**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAvailableActionEvents",
    "id": 2116665643
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2116665643,
    "result": {
        "bitbucket.webhook.commit": "Bitbucket commit recibido",
        "task.close": "Cerrando tarea",
        "github.webhook.commit": "Github commit recibido",
        "github.webhook.issue.assignee": "Github issue asignación cambiada",
        "github.webhook.issue.closed": "Github issue cerrada",
        "github.webhook.issue.commented": "Github issue comentario creado",
        "github.webhook.issue.label": "Github issue etiqueta cambiada",
        "github.webhook.issue.opened": "Github issue abierta",
        "github.webhook.issue.reopened": "Github issue reabierto",
        "gitlab.webhook.commit": "Gitlab commit recibido",
        "gitlab.webhook.issue.closed": "Gitlab issue cerrado",
        "gitlab.webhook.issue.opened": "Gitlab issue abierto",
        "task.move.column": "Mover una tarea a otra columna",
        "task.open": "Abrir una tarea abierta",
        "task.assignee_change": "Tarea cambio de asignación",
        "task.create": "Creación de tarea",
        "task.create_update": "Creación de tarea o modificación",
        "task.update": "Modificación de tarea"
    }
}
```

## getCompatibleActionEvents [Obtener acciones compatibles con eventos]

- Propósito: **Obtener una lista de eventos compatibles con una acción**
- Parametros:
    - **action_name** (string, required)
- Resultado satisfactorio: **lista de eventos**
- Resultado fallido: **falso**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getCompatibleActionEvents",
    "id": 899370297,
    "params": [
        "\Kanboard\Action\TaskClose"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 899370297,
    "result": {
        "bitbucket.webhook.commit": "Bitbucket commit recibido",
        "github.webhook.commit": "Github commit recibido",
        "github.webhook.issue.closed": "Github issue cerrada",
        "gitlab.webhook.commit": "Gitlab commit recibido",
        "gitlab.webhook.issue.closed": "Gitlab issue cerrado",
        "task.move.column": "Mover una tarea a otra columna"
    }
}
```

## getActions [Obtener acciones]

- Propósito: **Obtener una lista de acciones para un proyecto**
- Parametros:
    - **project_id** (integer, required)
- Resultado satisfactorio: **lista de propiedades de las acciones**
- Resultado fallido: **falso**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getActions",
    "id": 1433237746,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": [
        {
            "id" : "13",
            "project_id" : "2",
            "event_name" : "task.move.column",
            "action_name" : "\Kanboard\Action\TaskAssignSpecificUser",
            "params" : {
                "column_id" : "5",
                "user_id" : "1"
            }
        }
    ]
}
```

## createAction [Creación de acciones]

- Proposito: **Crear una acción**
- Parametros:
    - **project_id** (integer, required)
    - **event_name** (string, required)
    - **action_name** (string, required)
    - **params** (key/value parameters, required)
- Resultados satisfactorios: **action_id**
- Resultados fallidos: **falso**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createAction",
    "id": 1433237746,
    "params": {
        "project_id" : "2",
        "event_name" : "task.move.column",
        "action_name" : "\Kanboard\Action\TaskAssignSpecificUser",
        "params" : {
            "column_id" : "3",
            "user_id" : "2"
        }
    }
}
```

Ejemplo de respuestas:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": 14
}
```

## removeAction [Eliminar una acción]

- Proposito: **Eliminar una acción**
- Parametros:
    - **action_id** (integer, required)
- Resultados satisfactorios: **true**
- Resultados fallidos: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeAction",
    "id": 1510741671,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1510741671,
    "result": true
}
```
