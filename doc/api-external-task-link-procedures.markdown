External Task Link API Procedures
=================================

## getExternalTaskLinkTypes

- Purpose: **Get all registered external link providers**
- Parameters: **none**
- Result on success: **dict**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"getExternalTaskLinkTypes","id":477370568}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": {
        "auto": "Auto",
        "attachment": "Attachment",
        "file": "Local File",
        "weblink": "Web Link"
    },
    "id": 477370568
}
```

## getExternalTaskLinkProviderDependencies

- Purpose: **Get available dependencies for a given provider**
- Parameters:
    - **providerName** (string, required)
- Result on success: **dict**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"getExternalTaskLinkProviderDependencies","id":124790226,"params":["weblink"]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": {
        "related": "Related"
    },
    "id": 124790226
}
```

## createExternalTaskLink

- Purpose: **Create a new external link**
- Parameters:
    - **task_id** (integer, required)
    - **url** (string, required)
    - **dependency** (string, required)
    - **type** (string, optional)
    - **title** (string, optional)
- Result on success: **link_id**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"createExternalTaskLink","id":924217495,"params":[9,"http:\/\/localhost\/document.pdf","related","attachment"]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": 1,
    "id": 924217495
}
```

## updateExternalTaskLink

- Purpose: **Update external task link**
- Parameters:
    - **task_id** (integer, required)
    - **link_id** (integer, required)
    - **title** (string, required)
    - **url** (string, required)
    - **dependency** (string, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc":"2.0",
    "method":"updateExternalTaskLink",
    "id":1123562620,
    "params": {
        "task_id":9,
        "link_id":1,
        "title":"New title"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 1123562620
}
```

## getExternalTaskLinkById

- Purpose: **Get an external task link**
- Parameters:
    - **task_id** (integer, required)
    - **link_id** (integer, required)
- Result on success: **dict**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"getExternalTaskLinkById","id":2107066744,"params":[9,1]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": {
        "id": "1",
        "link_type": "attachment",
        "dependency": "related",
        "title": "document.pdf",
        "url": "http:\/\/localhost\/document.pdf",
        "date_creation": "1466965256",
        "date_modification": "1466965256",
        "task_id": "9",
        "creator_id": "0"
    },
    "id": 2107066744
}
```

## getAllExternalTaskLinks

- Purpose: **Get all external links attached to a task**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **list of external links**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"getAllExternalTaskLinks","id":2069307223,"params":[9]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": [
        {
            "id": "1",
            "link_type": "attachment",
            "dependency": "related",
            "title": "New title",
            "url": "http:\/\/localhost\/document.pdf",
            "date_creation": "1466965256",
            "date_modification": "1466965256",
            "task_id": "9",
            "creator_id": "0",
            "creator_name": null,
            "creator_username": null,
            "dependency_label": "Related",
            "type": "Attachment"
        }
    ],
    "id": 2069307223
}
```

## removeExternalTaskLink

- Purpose: **Remove an external link**
- Parameters:
    - **task_id** (integer, required)
    - **link_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"removeExternalTaskLink","id":552055660,"params":[9,1]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 552055660
}
```
