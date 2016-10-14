API Procedimientos de subtareas
======================

## createSubtask

- Propósito: **Crea una nueva subtarea**
- Parametros:
    - **task_id** (integer, required)
    - **title** (integer, required)
    - **user_id** (int, optional)
    - **time_estimated** (int, optional)
    - **time_spent** (int, optional)
    - **status** (int, optional)
- Resultado en caso exitoso: **subtask_id**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createSubtask",
    "id": 2041554661,
    "params": {
        "task_id": 1,
        "title": "Subtask #1"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2041554661,
    "result": 45
}
```

## getSubtask

- Propósito: **Obtiene informacion de subtarea**
- Parametros:
    - **subtask_id** (integer)
- Resultado en caso exitoso: **subtask properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getSubtask",
    "id": 133184525,
    "params": {
        "subtask_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 133184525,
    "result": {
        "id": "1",
        "title": "Subtask #1",
        "status": "0",
        "time_estimated": "0",
        "time_spent": "0",
        "task_id": "1",
        "user_id": "0"
    }
}
```

## getAllSubtasks

- Propósito: **Obtiene todas las subtareas disponibles**
- Parametros:
    - **task_id** (integer, required)
- Resultado en caso exitoso: **List of subtasks**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllSubtasks",
    "id": 2087700490,
    "params": {
        "task_id": 1
    }
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2087700490,
    "result": [
        {
            "id": "1",
            "title": "Subtask #1",
            "status": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "task_id": "1",
            "user_id": "0",
            "username": null,
            "name": null,
            "status_name": "Todo"
        },
        ...
    ]
}
```

## updateSubtask

- Propósito: **Actualiza una subtarea**
- Parametros:
    - **id** (integer, required)
    - **task_id** (integer, required)
    - **title** (integer, optional)
    - **user_id** (integer, optional)
    - **time_estimated** (integer, optional)
    - **time_spent** (integer, optional)
    - **status** (integer, optional)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "updateSubtask",
    "id": 191749979,
    "params": {
        "id": 1,
        "task_id": 1,
        "status": 1,
        "time_spent": 5,
        "user_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 191749979,
    "result": true
}
```

## removeSubtask

- Propósito: **Elimina una subtarea**
- Parametros:
    - **subtask_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeSubtask",
    "id": 1382487306,
    "params": {
        "subtask_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1382487306,
    "result": true
}
```
