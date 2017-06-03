Group Member API Procedures
===========================

## getMemberGroups

- Propósito: **Obtener todos los grupos de un usuario determinado**
- Parámetros:
    - **user_id** (integer, required)
- Resultado en caso de éxito: **List of groups**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{
    "jsonrpc": "2.0",
    "method": "getMemberGroups",
    "id": 1987176726,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1987176726,
    "result": [
        {
            "id": "1",
            "name": "My Group A"
        }
    ]
}
```

## getGroupMembers

- Propósito: **Obtener todos los miembros de un grupo**
- Parámetros:
    - **group_id** (integer, required)
- Resultado en caso de éxito: **List of users**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{
    "jsonrpc": "2.0",
    "method": "getGroupMembers",
    "id": 1987176726,
    "params": [
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1987176726,
    "result": [
        {
            "group_id": "1",
            "user_id": "1",
            "id": "1",
            "username": "admin",
            "is_ldap_user": "0",
            "name": null,
            "email": null,
            "notifications_enabled": "0",
            "timezone": null,
            "language": null,
            "disable_login_form": "0",
            "notifications_filter": "4",
            "nb_failed_login": "0",
            "lock_expiration_date": "0",
            "is_project_admin": "0",
            "gitlab_id": null,
            "role": "app-admin"
        }
    ]
}
```

## addGroupMember

- Propósito: **Agregar un usuario a un grupo**
- Parámetros:
    - **group_id** (integer, required)
    - **user_id** (integer, required)
- Resultado en caso de éxito: **true**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{
    "jsonrpc": "2.0",
    "method": "addGroupMember",
    "id": 1589058273,
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
    "id": 1589058273,
    "result": true
}
```

## removeGroupMember

- Propósito: **Quitar un usuario de un grupo**
- Parámetros:
    - **group_id** (integer, required)
    - **user_id** (integer, required)
- Resultado en caso de éxito: **true**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{
    "jsonrpc": "2.0",
    "method": "removeGroupMember",
    "id": 1730416406,
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
    "id": 1730416406,
    "result": true
}
```

## isGroupMember

- Propósito: **Comprobar si un usuario es miembro de un grupo**
- Parámetros:
    - **group_id** (integer, required)
    - **user_id** (integer, required)
- Resultado en caso de éxito: **true**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{
    "jsonrpc": "2.0",
    "method": "isGroupMember",
    "id": 1052800865,
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
    "id": 1052800865,
    "result": false
}
```
