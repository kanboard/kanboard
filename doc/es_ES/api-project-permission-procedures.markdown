API Procedimientos de permisos de proyecto
=================================

## getProjectUsers

- Propósito: **Obtiene todos los miembros de un proyecto**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **Dictionary of user_id => user name**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectUsers",
    "id": 1601016721,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1601016721,
    "result": {
        "1": "admin"
    }
}
```

## getAssignableUsers

- Propósito: **Obtiene los usuarios que pueden ser asignados a una tarea para un proyecto** (todos los miembros excepto los visores)
- Parametros:
    - **project_id** (integer, required)
    - **prepend_unassigned** (boolean, optional, default is false)
- Resultado en caso exitoso: **Dictionary of user_id => user name**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAssignableUsers",
    "id": 658294870,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 658294870,
    "result": {
        "1": "admin"
    }
}
```

## addProjectUser

- Propósito: **Permite el acceso a un proyecto para un usuario**
- Parametros:
    - **project_id** (integer, required)
    - **user_id** (integer, required)
    - **role** (string, optional)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "addProjectUser",
    "id": 1294688355,
    "params": [
        "1",
        "1",
        "project-viewer"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1294688355,
    "result": true
}
```

## addProjectGroup

- Propósito: **Permite el acceso a un proyecto para un grupo**
- Parametros:
    - **project_id** (integer, required)
    - **group_id** (integer, required)
    - **role** (string, optional)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "addProjectGroup",
    "id": 1694959089,
    "params": [
        "1",
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1694959089,
    "result": true
}
```

## removeProjectUser

- Propósito: **Revoca el acceso del usuario a un proyecto**
- Parametros:
    - **project_id** (integer, required)
    - **user_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeProjectUser",
    "id": 645233805,
    "params": [
        1,
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 645233805,
    "result": true
}
```

## removeProjectGroup

- Propósito: **Revoca el acceso del grupo a un proyecto**
- Parametros:
    - **project_id** (integer, required)
    - **group_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeProjectGroup",
    "id": 557146966,
    "params": [
        1,
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 557146966,
    "result": true
}
```

## changeProjectUserRole

- Propósito: **Cambia el rol de un usuario para un proyecto**
- Parametros:
    - **project_id** (integer, required)
    - **user_id** (integer, required)
    - **role** (string, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "changeProjectUserRole",
    "id": 193473170,
    "params": [
        "1",
        "1",
        "project-viewer"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 193473170,
    "result": true
}
```

## changeProjectGroupRole

- Propósito: **Cambia el rol de un grupo para un proyecto**
- Parametros:
    - **project_id** (integer, required)
    - **group_id** (integer, required)
    - **role** (string, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "changeProjectGroupRole",
    "id": 2114673298,
    "params": [
        "1",
        "1",
        "project-viewer"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2114673298,
    "result": true
}
```

## getProjectUserRole

- Propósito: **Obtiene el rol de un usuario para un determinado proyecto**
- Parametros:
    - **project_id** (integer, required)
    - **user_id** (integer, required)
- Resultado en caso exitoso: **role name**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectUserRole",
    "id": 2114673298,
    "params": [
        "2",
        "3"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2114673298,
    "result": "project-viewer"
}
```
