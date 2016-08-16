Project File API Procedures
===========================

## createProjectFile

- Purpose: **Create and upload a new project attachment**
- Parameters:
    - **project_id** (integer, required)
    - **filename** (integer, required)
    - **blob** File content encoded in base64 (string, required)
- Result on success: **file_id**
- Result on failure: **false**
- Note: **The maximum file size depends of your PHP configuration, this method should not be used to upload large files**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createProjectFile",
    "id": 94500810,
    "params": [
        1,
        "My file",
        "cGxhaW4gdGV4dCBmaWxl"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 94500810,
    "result": 1
}
```

## getAllProjectFiles

- Purpose: **Get all files attached to a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **list of files**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllProjectFiles",
    "id": 1880662820,
    "params": {
        "project_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1880662820,
    "result": [
        {
            "id": "1",
            "name": "My file",
            "path": "1\/1\/0db4d0a897a4c852f6e12f0239d4805f7b4ab596",
            "is_image": "0",
            "project_id": "1",
            "date": "1432509941",
            "user_id": "0",
            "size": "15",
            "username": null,
            "user_name": null
        }
    ]
}
```

## getProjectFile

- Purpose: **Get file information**
- Parameters:
    - **project_id** (integer, required)
    - **file_id** (integer, required)
- Result on success: **file properties**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectFile",
    "id": 318676852,
    "params": [
        "42",
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 318676852,
    "result": {
        "id": "1",
        "name": "My file",
        "path": "1\/1\/0db4d0a897a4c852f6e12f0239d4805f7b4ab596",
        "is_image": "0",
        "project_id": "1",
        "date": "1432509941",
        "user_id": "0",
        "size": "15"
    }
}
```

## downloadProjectFile

- Purpose: **Download project file contents (encoded in base64)**
- Parameters:
    - **project_id** (integer, required)
    - **file_id** (integer, required)
- Result on success: **base64 encoded string**
- Result on failure: **empty string**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "downloadProjectFile",
    "id": 235943344,
    "params": [
        "1",
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 235943344,
    "result": "cGxhaW4gdGV4dCBmaWxl"
}
```

## removeProjectFile

- Purpose: **Remove a file associated to a project**
- Parameters:
    - **project_id** (integer, required)
    - **file_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeProjectFile",
    "id": 447036524,
    "params": [
        "1",
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 447036524,
    "result": true
}
```

## removeAllProjectFiles

- Purpose: **Remove all files associated to a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeAllProjectFiles",
    "id": 593312993,
    "params": {
        "project_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 593312993,
    "result": true
}
```
