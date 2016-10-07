API Procedimientos de Usuario
===================

## createUser

- Propósito: **Crea un nuevo usuario**
- Parametros:
    - **username** Must be unique (string, required)
    - **password** Must have at least 6 characters (string, required)
    - **name** (string, optional)
    - **email** (string, optional)
    - **role** (string, optional, example: app-admin, app-manager, app-user)
- Resultado en caso exitoso: **user_id**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createUser",
    "id": 1518863034,
    "params": {
        "username": "biloute",
        "password": "123456"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1518863034,
    "result": 22
}
```

## createLdapUser

- Propósito: **Crea un nuevo usuario autentificado por LDAP**
- Parametros:
    - **username** (string, required)
- Resultado en caso exitoso: **user_id**
- Resultado en caso fallido: **false**

The user will only be created if he is found on the LDAP server.
This method works only with LDAP authentication configured in proxy or anonymous mode.

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createLdapUser",
    "id": 1518863034,
    "params": {
        "username": "my_ldap_user",
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1518863034,
    "result": 22
}
```

## getUser

- Propósito: **Obtiene información de usuario**
- Parametros:
    - **user_id** (integer, required)
- Resultado en caso exitoso: **user properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getUser",
    "id": 1769674781,
    "params": {
        "user_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1769674781,
    "result": {
        "id": "1",
        "username": "biloute",
        "password": "$2y$10$dRs6pPoBu935RpmsrhmbjevJH5MgZ7Kr9QrnVINwwyZ3.MOwqg.0m",
        "role": "app-user",
        "is_ldap_user": "0",
        "name": "",
        "email": "",
        "google_id": null,
        "github_id": null,
        "notifications_enabled": "0"
    }
}
```

## getUserByName

- Propósito: **Obtiene información de usuario**
- Parametros:
    - **username** (string, required)
- Resultado en caso exitoso: **user properties**
- Resultado en caso fallido: **null**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getUserByName",
    "id": 1769674782,
    "params": {
        "username": "biloute"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1769674782,
    "result": {
        "id": "1",
        "username": "biloute",
        "password": "$2y$10$dRs6pPoBu935RpmsrhmbjevJH5MgZ7Kr9QrnVINwwyZ3.MOwqg.0m",
        "role": "app-user",
        "is_ldap_user": "0",
        "name": "",
        "email": "",
        "google_id": null,
        "github_id": null,
        "notifications_enabled": "0"
    }
}
```

## getAllUsers

- Propósito: **Obtiene todos los usuarios disponibles**
- Parametros:
    - **none**
- Resultado en caso exitoso: **List of users**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllUsers",
    "id": 1438712131
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1438712131,
    "result": [
        {
            "id": "1",
            "username": "biloute",
            "name": "",
            "email": "",
            "role": "app-user",
            "is_ldap_user": "0",
            "notifications_enabled": "0",
            "google_id": null,
            "github_id": null
        },
        ...
    ]
}
```

## updateUser

- Propósito: **Actualiza un usuario**
- Parametros:
    - **id** (integer)
    - **username** (string, optional)
    - **name** (string, optional)
    - **email** (string, optional)
    - **role** (string, optional, example: app-admin, app-manager, app-user)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "updateUser",
    "id": 322123657,
    "params": {
        "id": 1,
        "role": "app-manager"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 322123657,
    "result": true
}
```

## removeUser

- Propósito: **Elimina un usuario**
- Parametros:
    - **user_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeUser",
    "id": 2094191872,
    "params": {
        "user_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2094191872,
    "result": true
}
```

## disableUser

- Propósito: **Desactiva un usuario**
- Parametros:
    - **user_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "disableUser",
    "id": 2094191872,
    "params": {
        "user_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2094191872,
    "result": true
}
```

## enableUser

- Propósito: **Activa un usuario**
- Parametros:
    - **user_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "enableUser",
    "id": 2094191872,
    "params": {
        "user_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2094191872,
    "result": true
}
```

## isActiveUser

- Propósito: **Revisa si un usuario esta activo**
- Parametros:
    - **user_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "isActiveUser",
    "id": 2094191872,
    "params": {
        "user_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 2094191872,
    "result": true
}
```
