API User Procedures
===================

## createUser

- Purpose: **Create a new user**
- Parameters:
    - **username** Must be unique (string, required)
    - **password** Must have at least 6 characters (string, required)
    - **name** (string, optional)
    - **email** (string, optional)
    - **role** (string, optional, example: app-admin, app-manager, app-user)
- Result on success: **user_id**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1518863034,
    "result": 22
}
```

## createLdapUser

- Purpose: **Create a new user authentified by LDAP**
- Parameters:
    - **username** (string, required)
- Result on success: **user_id**
- Result on failure: **false**

The user will only be created if he is found on the LDAP server.
This method works only with LDAP authentication configured in proxy or anonymous mode.

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1518863034,
    "result": 22
}
```

## getUser

- Purpose: **Get user information**
- Parameters:
    - **user_id** (integer, required)
- Result on success: **user properties**
- Result on failure: **null**

Request example:

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

Response example:

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

- Purpose: **Get user information**
- Parameters:
    - **username** (string, required)
- Result on success: **user properties**
- Result on failure: **null**

Request example:

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

Response example:

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

- Purpose: **Get all available users**
- Parameters:
    - **none**
- Result on success: **List of users**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllUsers",
    "id": 1438712131
}
```

Response example:

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

- Purpose: **Update a user**
- Parameters:
    - **id** (integer)
    - **username** (string, optional)
    - **name** (string, optional)
    - **email** (string, optional)
    - **role** (string, optional, example: app-admin, app-manager, app-user)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 322123657,
    "result": true
}
```

## removeUser

- Purpose: **Remove a user**
- Parameters:
    - **user_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2094191872,
    "result": true
}
```

## disableUser

- Purpose: **Disable a user**
- Parameters:
    - **user_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2094191872,
    "result": true
}
```

## enableUser

- Purpose: **Enable a user**
- Parameters:
    - **user_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2094191872,
    "result": true
}
```

## isActiveUser

- Purpose: **Check if a user is active**
- Parameters:
    - **user_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2094191872,
    "result": true
}
```
