API Procedimientos de enlace
===================

## getAllLinks

- Propósito: **Obtiene la lista de posibles relaciones entre tareas**
- Parametros: ninguno
- Resultado en caso exitoso: **List of links**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllLinks",
    "id": 113057196
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 113057196,
    "result": [
        {
            "id": "1",
            "label": "relates to",
            "opposite_id": "0"
        },
        {
            "id": "2",
            "label": "blocks",
            "opposite_id": "3"
        },
        {
            "id": "3",
            "label": "is blocked by",
            "opposite_id": "2"
        },
        {
            "id": "4",
            "label": "duplicates",
            "opposite_id": "5"
        },
        {
            "id": "5",
            "label": "is duplicated by",
            "opposite_id": "4"
        },
        {
            "id": "6",
            "label": "is a child of",
            "opposite_id": "7"
        },
        {
            "id": "7",
            "label": "is a parent of",
            "opposite_id": "6"
        },
        {
            "id": "8",
            "label": "targets milestone",
            "opposite_id": "9"
        },
        {
            "id": "9",
            "label": "is a milestone of",
            "opposite_id": "8"
        },
        {
            "id": "10",
            "label": "fixes",
            "opposite_id": "11"
        },
        {
            "id": "11",
            "label": "is fixed by",
            "opposite_id": "10"
        }
    ]
}
```

## getOppositeLinkId

- Propósito: **Obtiene el identificador de enlace opuesto de un enlace de tarea**
- Parametros:
    - **link_id** (integer, required)
- Resultado en caso exitoso: **link_id**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getOppositeLinkId",
    "id": 407062448,
    "params": [
        2
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 407062448,
    "result": "3"
}
```

## getLinkByLabel

- Propósito: **Obtiene un enlace por etiqueta**
- Parametros:
    - **label** (integer, required)
- Resultado en caso exitoso: **link properties**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getLinkByLabel",
    "id": 1796123316,
    "params": [
        "blocks"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1796123316,
    "result": {
        "id": "2",
        "label": "blocks",
        "opposite_id": "3"
    }
}
```

## getLinkById

- Propósito: **Obtiene un enlace por identificador**
- Parametros:
    - **link_id** (integer, required)
- Resultado en caso exitoso: **link properties**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getLinkById",
    "id": 1190238402,
    "params": [
        4
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1190238402,
    "result": {
        "id": "4",
        "label": "duplicates",
        "opposite_id": "5"
    }
}
```

## createLink

- Propósito: **Crea una nueva relación de tarea**
- Parametros:
    - **label** (integer, required)
    - **opposite_label** (integer, optional)
- Resultado en caso exitoso: **link_id**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createLink",
    "id": 1040237496,
    "params": [
        "foo",
        "bar"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1040237496,
    "result": 13
}
```

## updateLink

- Propósito: **Actualiza un enlace**
- Parametros:
    - **link_id** (integer, required)
    - **opposite_link_id** (integer, required)
    - **label** (string, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "updateLink",
    "id": 2110446926,
    "params": [
        "14",
        "12",
        "boo"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2110446926,
    "result": true
}
```

## removeLink

- Propósito: **Elimina un enlace**
- Parametros:
    - **link_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeLink",
    "id": 2136522739,
    "params": [
        "14"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2136522739,
    "result": true
}
```
