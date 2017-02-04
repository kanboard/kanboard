API Project Procedures
======================

## createProject

- Purpose: **Create a new project**
- Parameters:
    - **name** (string, required)
    - **description** (string, optional)
    - **owner_id** (integer, optional)
    - **identifier** (string, optional)
- Result on success: **project_id**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1797076613,
    "result": 2
}
```

## getProjectById

- Purpose: **Get project information**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **project properties**
- Result on failure: **null**

Request example:

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

Response example:

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

- Purpose: **Get project information**
- Parameters:
    - **name** (string, required)
- Result on success: **project properties**
- Result on failure: **null**

Request example:

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

Response example:

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

- Purpose: **Get project information**
- Parameters:
    - **identifier** (string, required)
- Result on success: **project properties**
- Result on failure: **null**

Request example:

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

Response example:

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

## getProjectByEmail

- Purpose: **Get project information**
- Parameters:
    - **email** (string, required)
- Result on success: **project properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectByEmail",
    "id": 1620253806,
    "params": {
        "email": "my_project@my_domain.tld"
    }
}
```

Response example:

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
        "email": "my_project@my_domain.tld",
        "url": {
            "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",
            "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",
            "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"
        }
    }
}
```

## getAllProjects

- Purpose: **Get all available projects**
- Parameters:
    - **none**
- Result on success: **List of projects**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllProjects",
    "id": 2134420212
}
```

Response example:

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

- Purpose: **Update a project**
- Parameters:
    - **project_id** (integer, required)
    - **name** (string, optional)
    - **description** (string, optional)
    - **owner_id** (integer, optional)
    - **identifier** (string, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1853996288,
    "result": true
}
```

## removeProject

- Purpose: **Remove a project**
- Parameters:
    **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 46285125,
    "result": true
}
```

## enableProject

- Purpose: **Enable a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1775494839,
    "result": true
}
```

## disableProject

- Purpose: **Disable a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1734202312,
    "result": true
}
```

## enableProjectPublicAccess

- Purpose: **Enable public access for a given project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 103792571,
    "result": true
}
```

## disableProjectPublicAccess

- Purpose: **Disable public access for a given project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 942472945,
    "result": true
}
```

## getProjectActivity

- Purpose: **Get activity stream for a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **List of events**
- Result on failure: **false**

Request example:

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

- Purpose: **Get Activityfeed for Project(s)**
- Parameters:
    - **project_ids** (integer array, required)
- Result on success: **List of events**
- Result on failure: **false**

Request example:

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
