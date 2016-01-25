API Category Procedures
=======================

## createCategory

- Purpose: **Create a new category**
- Parameters:
- **project_id** (integer, required)
    - **name** (string, required, must be unique for the given project)
- Result on success: **category_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createCategory",
    "id": 541909890,
    "params": {
        "name": "Super category",
        "project_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 541909890,
    "result": 4
}
```

## getCategory

- Purpose: **Get category information**
- Parameters:
    - **category_id** (integer, required)
- Result on success: **category properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getCategory",
    "id": 203539163,
    "params": {
        "category_id": 1
    }
}
```

Response example:

```json
{

    "jsonrpc": "2.0",
    "id": 203539163,
    "result": {
        "id": "1",
        "name": "Super category",
        "project_id": "1"
    }
}
```

## getAllCategories

- Purpose: **Get all available categories**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **List of categories**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllCategories",
    "id": 1261777968,
    "params": {
        "project_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1261777968,
    "result": [
        {
            "id": "1",
            "name": "Super category",
            "project_id": "1"
        }
    ]
}
```

## updateCategory

- Purpose: **Update a category**
- Parameters:
    - **id** (integer, required)
    - **name** (string, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateCategory",
    "id": 570195391,
    "params": {
        "id": 1,
        "name": "Renamed category"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 570195391,
    "result": true
}
```

## removeCategory

- Purpose: **Remove a category**
- Parameters:
    - **category_id** (integer)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeCategory",
    "id": 88225706,
    "params": {
        "category_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 88225706,
    "result": true
}
```
