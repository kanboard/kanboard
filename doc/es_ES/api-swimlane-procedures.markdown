API Procedimientos de diagrama
=======================

## getDefaultSwimlane

- Propósito: **Obtiene los diagramas pre determinados para un proyecto**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getDefaultSwimlane",
    "id": 898774713,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 898774713,
    "result": {
        "id": "1",
        "default_swimlane": "Default swimlane",
        "show_default_swimlane": "1"
    }
}
```

## getActiveSwimlanes

- Propósito: **Obtiene la lista de diagramas activos de un proyecto (Incluye el diagrama predeterminado si esta activo)**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **List of swimlanes**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getActiveSwimlanes",
    "id": 934789422,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 934789422,
    "result": [
        {
            "id": 0,
            "name": "Default swimlane"
        },
        {
            "id": "2",
            "name": "Swimlane A"
        }
    ]
}
```

## getAllSwimlanes

- Propósito: **Obtiene la lista de todos los diagramas de un proyecto (Activo o inactivo) y los ordena por posición**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **List of swimlanes**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllSwimlanes",
    "id": 509791576,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 509791576,
    "result": [
        {
            "id": "1",
            "name": "Another swimlane",
            "position": "1",
            "is_active": "1",
            "project_id": "1"
        },
        {
            "id": "2",
            "name": "Swimlane A",
            "position": "2",
            "is_active": "1",
            "project_id": "1"
        }
    ]
}
```

## getSwimlane

- Propósito: **Obtiene el diagrama por identificador**
- Parametros:
    - **swimlane_id** (integer, required)
- Resultado en caso exitoso: **swimlane properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getSwimlane",
    "id": 131071870,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 131071870,
    "result": {
        "id": "1",
        "name": "Swimlane 1",
        "position": "1",
        "is_active": "1",
        "project_id": "1"
    }
}
```

## getSwimlaneById

- Propósito: **Obtiene el diagrama por identificador**
- Parametros:
    - **swimlane_id** (integer, required)
- Resultado en caso exitoso: **swimlane properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getSwimlaneById",
    "id": 131071870,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 131071870,
    "result": {
        "id": "1",
        "name": "Swimlane 1",
        "position": "1",
        "is_active": "1",
        "project_id": "1"
    }
}
```

## getSwimlaneByName

- Propósito: **Obtiene el diagrama por nombre**
- Parametros:
    - **project_id** (integer, required)
    - **name** (string, required)
- Resultado en caso exitoso: **swimlane properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getSwimlaneByName",
    "id": 824623567,
    "params": [
        1,
        "Swimlane 1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 824623567,
    "result": {
        "id": "1",
        "name": "Swimlane 1",
        "position": "1",
        "is_active": "1",
        "project_id": "1"
    }
}
```

## changeSwimlanePosition

- Propósito: **Asciende la posición del diagrama** (Solo para diagramas activos)
- Parametros:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
    - **position** (integer, required, must be >= 1)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "changeSwimlanePosition",
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

## updateSwimlane

- Propósito: **Actualiza las propiedades del diagrama**
- Parametros:
    - **swimlane_id** (integer, required)
    - **name** (string, required)
    - **description** (string, optional)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "updateSwimlane",
    "id": 87102426,
    "params": [
        "1",
        "Another swimlane"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 87102426,
    "result": true
}
```

## addSwimlane

- Propósito: **Agrega un nuevo diagrama**
- Parametros:
    - **project_id** (integer, required)
    - **name** (string, required)
    - **description** (string, optional)
- Resultado en caso exitoso: **swimlane_id**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "addSwimlane",
    "id": 849940086,
    "params": [
        1,
        "Swimlane 1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 849940086,
    "result": 1
}
```

## removeSwimlane

- Propósito: **Elimina un diagrama**
- Parametros:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeSwimlane",
    "id": 1433237746,
    "params": [
        2,
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

## disableSwimlane

- Propósito: **Desactiva un diagrama**
- Parametros:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "disableSwimlane",
    "id": 1433237746,
    "params": [
        2,
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

## enableSwimlane

- Propósito: **Activa un diagrama**
- Parametros:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "enableSwimlane",
    "id": 1433237746,
    "params": [
        2,
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
