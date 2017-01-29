API Tags Procedures
===================

getAllTags
----------

- Purpose: **Get all tags**
- Parameters: none
- Result on success: **List of tags**
- Result on failure: **false|null**

Request example:

```json
{"jsonrpc":"2.0","method":"getAllTags","id":45253426}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": [
        {
            "id": "1",
            "name": "another tag",
            "project_id": "33"
        }
    ],
    "id": 45253426
}
```

getTagsByProject
----------------

- Purpose: **Get all tags for a given project**
- Parameters:
    - **project_id** (integer)
- Result on success: **List of tags**
- Result on failure: **false|null**

Request example:

```json
{"jsonrpc":"2.0","method":"getTagsByProject","id":1217591720,"params":[33]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": [
        {
            "id": "1",
            "name": "some tag",
            "project_id": "33"
        }
    ],
    "id": 1217591720
}
```

createTag
---------

- Purpose: **Create a new tag**
- Parameters:
    - **project_id** (integer)
    - **tag** (string)
- Result on success: **tag_id**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"createTag","id":1775436017,"params":[33,"some tag"]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": 1,
    "id": 1775436017
}
```

updateTag
---------

- Purpose: **Rename a tag**
- Parameters:
    - **tag_id** (integer)
    - **tag** (string)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"updateTag","id":2037516512,"params":["1","another tag"]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 2037516512
}
```

removeTag
---------

- Purpose: **removeTag**
- Parameters:
    - **tag_id** (integer)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"removeTag","id":907581298,"params":["1"]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 907581298
}
```

setTaskTags
-----------

- Purpose: **Assign/Create/Update tags for a task**
- Parameters:
    - **project_id** (integer)
    - **task_id** (integer)
    - **tags** List of tags ([]string)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"setTaskTags","id":1524522873,"params":[39,17,["tag1","tag2"]]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 1524522873
}
```

getTaskTags
-----------

- Purpose: **Get assigned tags to a task**
- Parameters:
    - **task_id** (integer)
- Result on success: **Dictionary of tags**
- Result on failure: **false|null**

Request example:

```json
{"jsonrpc":"2.0","method":"getTaskTags","id":1667157705,"params":[17]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": {
        "1": "tag1",
        "2": "tag2"
    },
    "id": 1667157705
}
```
