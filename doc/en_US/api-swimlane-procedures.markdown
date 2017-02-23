API Swimlane Procedures
=======================

## getActiveSwimlanes

- Purpose: **Get the list of enabled swimlanes of a project (include default swimlane if enabled)**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **List of swimlanes**
- Result on failure: **null**

Request example:

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

Response example:

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

- Purpose: **Get the list of all swimlanes of a project (enabled or disabled) and sorted by position**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **List of swimlanes**
- Result on failure: **null**

Request example:

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

Response example:

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

- Purpose: **Get the a swimlane by id**
- Parameters:
    - **swimlane_id** (integer, required)
- Result on success: **swimlane properties**
- Result on failure: **null**

Request example:

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

Response example:

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

- Purpose: **Get the a swimlane by id**
- Parameters:
    - **swimlane_id** (integer, required)
- Result on success: **swimlane properties**
- Result on failure: **null**

Request example:

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

Response example:

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

- Purpose: **Get the a swimlane by name**
- Parameters:
    - **project_id** (integer, required)
    - **name** (string, required)
- Result on success: **swimlane properties**
- Result on failure: **null**

Request example:

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

Response example:

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

- Purpose: **Move up the swimlane position** (only for active swimlanes)
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
    - **position** (integer, required, must be >= 1)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 99275573,
    "result": true
}
```

## updateSwimlane

- Purpose: **Update swimlane properties**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
    - **name** (string, required)
    - **description** (string, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateSwimlane",
    "id": 87102426,
    "params": [
        "1",
        "1",
        "Another swimlane"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 87102426,
    "result": true
}
```

## addSwimlane

- Purpose: **Add a new swimlane**
- Parameters:
    - **project_id** (integer, required)
    - **name** (string, required)
    - **description** (string, optional)
- Result on success: **swimlane_id**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 849940086,
    "result": 1
}
```

## removeSwimlane

- Purpose: **Remove a swimlane**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": true
}
```

## disableSwimlane

- Purpose: **Disable a swimlane**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": true
}
```

## enableSwimlane

- Purpose: **Enable a swimlane**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

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

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": true
}
```
