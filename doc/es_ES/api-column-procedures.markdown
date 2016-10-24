API Procedimientos de columna
=====================

## getColumns

- Propósito: **Obtiene toda la información de columnas para un proyecto dado**
- Parametros:
    - **project_id** (integer, required)
- Resultados en caso exitoso: **columns properties**
- Resultados en caso fallido: **empty list**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getColumns",
    "id": 887036325,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 887036325,
    "result": [
        {
            "id": "1",
            "title": "Backlog",
            "position": "1",
            "project_id": "1",
            "task_limit": "0"
        },
        {
            "id": "2",
            "title": "Ready",
            "position": "2",
            "project_id": "1",
            "task_limit": "0"
        },
        {
            "id": "3",
            "title": "Work in progress",
            "position": "3",
            "project_id": "1",
            "task_limit": "0"
        }
    ]
}
```

## getColumn

- Propósito: **Obtiene una columna individual**
- Parametros:
    - **column_id** (integer, required)
- Resultados en caso exitoso: **column properties**
- Resultados en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getColumn",
    "id": 1242049935,
    "params": [
        2
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1242049935,
    "result": {
        "id": "2",
        "title": "Youpi",
        "position": "2",
        "project_id": "1",
        "task_limit": "5"
    }
}
```

## changeColumnPosition

- Propósito: **Cambia la posición de columna**
- Parametros:
    - **project_id** (integer, required)
    - **column_id** (integer, required)
    - **position** (integer, required, must be >= 1)
- Resultados en caso exitoso: **true**
- Resultados en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "changeColumnPosition",
    "id": 99275573,
    "params": [
        1,
        2,
        3
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 99275573,
    "result": true
}
```

## updateColumn

- Propósito: **Actualiza las propiedades de la columna**
- Parametros:
    - **column_id** (integer, required)
    - **title** (string, required)
    - **task_limit** (integer, optional)
    - **description** (string, optional)
- Resultados en caso exitoso: **true**
- Resultados en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "updateColumn",
    "id": 480740641,
    "params": [
        2,
        "Boo",
        5
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 480740641,
    "result": true
}
```

## addColumn

- Propósito: **Agrega una nueva columna**
- Parametros:
    - **project_id** (integer, required)
    - **title** (string, required)
    - **task_limit** (integer, optional)
    - **description** (string, optional)
- Resultados en caso exitoso: **column_id**
- Resultados en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "addColumn",
    "id": 638544704,
    "params": [
        1,
        "Boo"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 638544704,
    "result": 5
}
```

## removeColumn

- Propósito: **Elimina una columna**
- Parametros:
    - **column_id** (integer, required)
- Resultados en caso exitoso: **true**
- Resultados en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeColumn",
    "id": 1433237746,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": true
}
```
