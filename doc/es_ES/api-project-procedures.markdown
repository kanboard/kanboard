API Procedimientos de proyecto
======================

## createProject

- Propósito: **Crea un nuevo proyecto**
- Parametros:
    - **name** (string, required)
    - **description** (string, optional)
    - **owner_id** (integer, optional)
    - **identifier** (string, optional)
- Resultado en caso exitoso: **project_id**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createProject",
    "id": 1797076613,
    "params": {
        "name": "PHP client"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1797076613,
    "result": 2
}
```

## getProjectById

- Propósito: **Obtiene información del proyecto**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **project properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectById",
    "id": 226760253,
    "params": {
        "project_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 226760253,
    "result": {
        "id": "1",
        "name": "API test",
        "is_active": "1",
        "token": "",
        "last_modified": "1436119135",
        "is_public": "0",
        "is_private": "0",
        "is_everybody_allowed": "0",
        "default_swimlane": "Default swimlane",
        "show_default_swimlane": "1",
        "description": "test",
        "identifier": "",
        "url": {
            "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",
            "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",
            "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"
        }
    }
}
```

## getProjectByName

- Propósito: **Obtiene información del proyecto**
- Parametros:
    - **name** (string, required)
- Resultado en caso exitoso: **project properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectByName",
    "id": 1620253806,
    "params": {
        "name": "Test"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1620253806,
    "result": {
        "id": "1",
        "name": "Test",
        "is_active": "1",
        "token": "",
        "last_modified": "1436119135",
        "is_public": "0",
        "is_private": "0",
        "is_everybody_allowed": "0",
        "default_swimlane": "Default swimlane",
        "show_default_swimlane": "1",
        "description": "test",
        "identifier": "",
        "url": {
            "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",
            "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",
            "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"
        }
    }
}
```

## getProjectByIdentifier

- Propósito: **Obtiene información del proyecto**
- Parametros:
    - **identifier** (string, required)
- Resultado en caso exitoso: **project properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectByIdentifier",
    "id": 1620253806,
    "params": {
        "identifier": "TEST"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1620253806,
    "result": {
        "id": "1",
        "name": "Test",
        "is_active": "1",
        "token": "",
        "last_modified": "1436119135",
        "is_public": "0",
        "is_private": "0",
        "is_everybody_allowed": "0",
        "default_swimlane": "Default swimlane",
        "show_default_swimlane": "1",
        "description": "test",
        "identifier": "TEST",
        "url": {
            "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",
            "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",
            "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"
        }
    }
}
```

## getAllProjects

- Propósito: **Obtiene todos los proyectos disponibles**
- Parametros:
    - **none**
- Resultado en caso exitoso: **List of projects**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllProjects",
    "id": 2134420212
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2134420212,
    "result": [
        {
            "id": "1",
            "name": "API test",
            "is_active": "1",
            "token": "",
            "last_modified": "1436119570",
            "is_public": "0",
            "is_private": "0",
            "is_everybody_allowed": "0",
            "default_swimlane": "Default swimlane",
            "show_default_swimlane": "1",
            "description": null,
            "identifier": "",
            "url": {
                "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",
                "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",
                "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"
            }
        }
    ]
}
```

## updateProject

- Propósito: **Actualiza un proyecto**
- Parametros:
    - **project_id** (integer, required)
    - **name** (string, optional)
    - **description** (string, optional)
    - **owner_id** (integer, optional)
    - **identifier** (string, optional)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "updateProject",
    "id": 1853996288,
    "params": {
        "project_id": 1,
        "name": "PHP client update"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1853996288,
    "result": true
}
```

## removeProject

- Propósito: **Elimina un proyecto**
- Parametros:
    **project_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeProject",
    "id": 46285125,
    "params": {
        "project_id": "2"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 46285125,
    "result": true
}
```

## enableProject

- Propósito: **Habilita un proyecto**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "enableProject",
    "id": 1775494839,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1775494839,
    "result": true
}
```

## disableProject

- Propósito: **Desactiva un proyecto**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "disableProject",
    "id": 1734202312,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1734202312,
    "result": true
}
```

## enableProjectPublicAccess

- Propósito: **Habilita el acceso publico para un proyecto determinado**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "enableProjectPublicAccess",
    "id": 103792571,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 103792571,
    "result": true
}
```

## disableProjectPublicAccess

- Propósito: **Desactiva el acceso publico para un proyecto determinado**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "disableProjectPublicAccess",
    "id": 942472945,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 942472945,
    "result": true
}
```

## getProjectActivity

- Propósito: **Obtiene el trafico de actividad para un proyecto**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **List of events**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectActivity",
    "id": 942472945,
    "params": [
        "project_id": 1
    ]
}
```

## getProjectActivities

- Propósito: **Obtiene la actividad de consumo para proyecto(s)**
- Parametros:
    - **project_ids** (integer array, required)
- Resultado en caso exitoso: **List of events**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectActivities",
    "id": 942472945,
    "params": [
        "project_ids": [1,2]
    ]
}
```
