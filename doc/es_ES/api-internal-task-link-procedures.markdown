API Procedimientos de Tareas Internas de Enlace
=================================

## createTaskLink

- Propósito: **Crea un enlace entre dos tareas**
- Parametros:
    - **task_id** (integer, required)
    - **opposite_task_id** (integer, required)
    - **link_id** (integer, required)
- Resultado en caso exitoso: **task_link_id**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createTaskLink",
    "id": 509742912,
    "params": [
        2,
        3,
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 509742912,
    "result": 1
}
```

## updateTaskLink

- Propósito: **Actualiza enlace de tarea**
- Parametros:
    - **task_link_id** (integer, required)
    - **task_id** (integer, required)
    - **opposite_task_id** (integer, required)
    - **link_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "updateTaskLink",
    "id": 669037109,
    "params": [
        1,
        2,
        4,
        2
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 669037109,
    "result": true
}
```

## getTaskLinkById

- Propósito: **Obtiene un enlace de tarea**
- Parametros:
    - **task_link_id** (integer, required)
- Resultado en caso exitoso: **task link properties**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getTaskLinkById",
    "id": 809885202,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 809885202,
    "result": {
        "id": "1",
        "link_id": "1",
        "task_id": "2",
        "opposite_task_id": "3"
    }
}
```

## getAllTaskLinks

- Propósito: **Obtiene todos los enlaces relacionados a una tarea**
- Parametros:
    - **task_id** (integer, required)
- Resultado en caso exitoso: **list of task link**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllTaskLinks",
    "id": 810848359,
    "params": [
        2
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 810848359,
    "result": [
        {
            "id": "1",
            "task_id": "3",
            "label": "relates to",
            "title": "B",
            "is_active": "1",
            "project_id": "1",
            "task_time_spent": "0",
            "task_time_estimated": "0",
            "task_assignee_id": "0",
            "task_assignee_username": null,
            "task_assignee_name": null,
            "column_title": "Backlog"
        }
    ]
}
```

## removeTaskLink

- Propósito: **Elimina un enlace entre dos tareas**
- Parametros:
    - **task_link_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeTaskLink",
    "id": 473028226,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 473028226,
    "result": true
}
```
