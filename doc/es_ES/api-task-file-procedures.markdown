API Procedimientos de archivo de tarea
========================

## createTaskFile

- Propósito: **Crea y sube una nueva tarea adjunta **
- Parametros:
    - **project_id** (integer, required)
    - **task_id** (integer, required)
    - **filename** (integer, required)
    - **blob** File content encoded in base64 (string, required)
- Resultado en caso exitoso: **file_id**
- Resultado en caso fallido: **false**
- Note: **El tamaño maximo del archivo depende de tu configuración PHP, este metodo no debe usarse para subir archivos grandes**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createTaskFile",
    "id": 94500810,
    "params": [
        1,
        1,
        "My file",
        "cGxhaW4gdGV4dCBmaWxl"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 94500810,
    "result": 1
}
```

## getAllTaskFiles

- Propósito: **Obtiene todos los archivos adjuntos a una tarea**
- Parametros:
    - **task_id** (integer, required)
- Resultado en caso exitoso: **list of files**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllTaskFiles",
    "id": 1880662820,
    "params": {
        "task_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1880662820,
    "result": [
        {
            "id": "1",
            "name": "My file",
            "path": "1\/1\/0db4d0a897a4c852f6e12f0239d4805f7b4ab596",
            "is_image": "0",
            "task_id": "1",
            "date": "1432509941",
            "user_id": "0",
            "size": "15",
            "username": null,
            "user_name": null
        }
    ]
}
```

## getTaskFile

- Propósito: **Obtiene información de archivo**
- Parametros:
    - **file_id** (integer, required)
- Resultado en caso exitoso: **file properties**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getTaskFile",
    "id": 318676852,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 318676852,
    "result": {
        "id": "1",
        "name": "My file",
        "path": "1\/1\/0db4d0a897a4c852f6e12f0239d4805f7b4ab596",
        "is_image": "0",
        "task_id": "1",
        "date": "1432509941",
        "user_id": "0",
        "size": "15"
    }
}
```

## downloadTaskFile

- Propósito: **Descarga el contenido del archivo (Codificado en base64)**
- Parametros:
    - **file_id** (integer, required)
- Resultado en caso exitoso: **base64 encoded string**
- Resultado en caso fallido: **empty string**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "downloadTaskFile",
    "id": 235943344,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 235943344,
    "result": "cGxhaW4gdGV4dCBmaWxl"
}
```

## removeTaskFile

- Propósito: **Elimina archivo**
- Parametros:
    - **file_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeTaskFile",
    "id": 447036524,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 447036524,
    "result": true
}
```

## removeAllTaskFiles

- Propósito: **Elimina todos los archivos asociados a la tarea**
- Parametros:
    - **task_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeAllTaskFiles",
    "id": 593312993,
    "params": {
        "task_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 593312993,
    "result": true
}
```
