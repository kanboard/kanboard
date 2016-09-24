API Procedimientos de Categoría
=======================

## createCategory

- Propósito: **Crea una nueva categoría**
- Parámetros:
- **project_id** (integer, required)
    - **name** (string, required, must be unique for the given project)
- Resultado en caso exitoso: **category_id**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createCategory",
    "id": 541909890,
    "params": {
        "name": "Super category",
        "project_id": 1
    }
}
```

Ejemplo de respuesta::

```json
{
    "jsonrpc": "2.0",
    "id": 541909890,
    "result": 4
}
```

## getCategory

- Propósito: **Obtiene información de la categoría**
- Parámetros:
    - **category_id** (integer, required)
- Resultado en caso exitoso: **category properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getCategory",
    "id": 203539163,
    "params": {
        "category_id": 1
    }
}
```

Ejemplo de respuesta::

```json
{

    "jsonrpc": "2.0",
    "id": 203539163,
    "result": {
        "id": "1",
        "name": "Super category",
        "project_id": "1"
    }
}
```

## getAllCategories

- Propósito: **Obtiene todas las categorías disponibles**
- Parámetros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **List of categories**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllCategories",
    "id": 1261777968,
    "params": {
        "project_id": 1
    }
}
```

Ejemplo de respuesta::

```json
{
    "jsonrpc": "2.0",
    "id": 1261777968,
    "result": [
        {
            "id": "1",
            "name": "Super category",
            "project_id": "1"
        }
    ]
}
```

## updateCategory

- Propósito: **Actualiza una categoría**
- Parámetros:
    - **id** (integer, required)
    - **name** (string, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "updateCategory",
    "id": 570195391,
    "params": {
        "id": 1,
        "name": "Renamed category"
    }
}
```

Ejemplo de respuesta::

```json
{
    "jsonrpc": "2.0",
    "id": 570195391,
    "result": true
}
```

## removeCategory

- Propósito: **Elimina una categoría**
- Parámetros:
    - **category_id** (integer)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeCategory",
    "id": 88225706,
    "params": {
        "category_id": 1
    }
}
```

Ejemplo de respuesta::

```json
{
    "jsonrpc": "2.0",
    "id": 88225706,
    "result": true
}
```
