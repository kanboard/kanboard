API Procedimientos de comentarios
======================

## createComment [Crear un comentario]

- Propósito: **Crear un nuevo comentario**
- Parametros:
    - **task_id** (integer, required)
    - **user_id** (integer, required)
    - **content** Reducción de contenido (string, required)
- Resultado satisfactorio: **comment_id**
- Resultado fallido : **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createComment",
    "id": 1580417921,
    "params": {
        "task_id": 1,
        "user_id": 1,
        "content": "Comment #1"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1580417921,
    "result": 11
}
```

## getComment [Obtener un comentario]

- Propósito: **Obtener la información del comentario**
- Parametros:
    - **comment_id** (integer, required)
- Resultado satisfactorio: **propiedades del comentario**
- Resultado fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getComment",
    "id": 867839500,
    "params": {
        "comment_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 867839500,
    "result": {
        "id": "1",
        "task_id": "1",
        "user_id": "1",
        "date_creation": "1410881970",
        "comment": "Comment #1",
        "username": "admin",
        "name": null
    }
}
```

## getAllComments [Obtener todos los comentarios]

- Proposito: **Obtener todos los comentarios disponibles**
- Parametros:
    - **task_id** (integer, required)
- Resultado satisfactorio: **Lista de comentarios**
- Resultado fallido : **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllComments",
    "id": 148484683,
    "params": {
        "task_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 148484683,
    "result": [
        {
            "id": "1",
            "date_creation": "1410882272",
            "task_id": "1",
            "user_id": "1",
            "comment": "Comment #1",
            "username": "admin",
            "name": null
        },
        ...
    ]
}
```

## updateComment [Actualizar un comentario]

- Proposito: **Actualizar un comentario**
- Parametros:
    - **id** (integer, required)
    - **content** Reducción de contenido (string, required)
- Resultado satisfactorio: **true**
- Resultado fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "updateComment",
    "id": 496470023,
    "params": {
        "id": 1,
        "content": "Comment #1 updated"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1493368950,
    "result": true
}
```

## removeComment [Eliminar un comentario]

- Proposito : **Eliminar un comentario**
- Parametros:
    - **comment_id** (integer, required)
- Resultado satisfactorio: **true**
- Resultado fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeComment",
    "id": 328836871,
    "params": {
        "comment_id": 1
    }
}
```
Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 328836871,
    "result": true
}
```
