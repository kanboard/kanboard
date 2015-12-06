API Board Procedures
====================

### getBoard

- Purpose: **Get all necessary information to display a board**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **board properties**
- Result on failure: **empty list**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getBoard",
    "id": 827046470,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 827046470,
    "result": [
        {
            "id": 0,
            "name": "Default swimlane",
            "columns": [
                {
                    "id": "1",
                    "title": "Backlog",
                    "position": "1",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [],
                    "nb_tasks": 0,
                    "score": 0
                },
                {
                    "id": "2",
                    "title": "Ready",
                    "position": "2",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [
                        {
                            "nb_comments":"0",
                            "nb_files":"0",
                            "nb_subtasks":"0",
                            "nb_completed_subtasks":"0",
                            "nb_links":"0",
                            "id":"2",
                            "reference":"",
                            "title":"Test",
                            "description":"",
                            "date_creation":"1430870507",
                            "date_modification":"1430870507",
                            "date_completed":null,
                            "date_due":"0",
                            "color_id":"yellow",
                            "project_id":"1",
                            "column_id":"2",
                            "swimlane_id":"0",
                            "owner_id":"0",
                            "creator_id":"1",
                            "position":"1",
                            "is_active":"1",
                            "score":"0",
                            "category_id":"0",
                            "date_moved":"1430870507",
                            "recurrence_status":"0",
                            "recurrence_trigger":"0",
                            "recurrence_factor":"0",
                            "recurrence_timeframe":"0",
                            "recurrence_basedate":"0",
                            "recurrence_parent":null,
                            "recurrence_child":null,
                            "assignee_username":null,
                            "assignee_name":null
                        }
                    ],
                    "nb_tasks": 1,
                    "score": 0
                },
                {
                    "id": "3",
                    "title": "Work in progress",
                    "position": "3",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [
                        {
                            "nb_comments":"0",
                            "nb_files":"0",
                            "nb_subtasks":"1",
                            "nb_completed_subtasks":"0",
                            "nb_links":"0",
                            "id":"1",
                            "reference":"",
                            "title":"Task with comment",
                            "description":"",
                            "date_creation":"1430783188",
                            "date_modification":"1430783188",
                            "date_completed":null,
                            "date_due":"0",
                            "color_id":"red",
                            "project_id":"1",
                            "column_id":"3",
                            "swimlane_id":"0",
                            "owner_id":"1",
                            "creator_id":"0",
                            "position":"1",
                            "is_active":"1",
                            "score":"0",
                            "category_id":"0",
                            "date_moved":"1430783191",
                            "recurrence_status":"0",
                            "recurrence_trigger":"0",
                            "recurrence_factor":"0",
                            "recurrence_timeframe":"0",
                            "recurrence_basedate":"0",
                            "recurrence_parent":null,
                            "recurrence_child":null,
                            "assignee_username":"admin",
                            "assignee_name":null
                        }
                    ],
                    "nb_tasks": 1,
                    "score": 0
                },
                {
                    "id": "4",
                    "title": "Done",
                    "position": "4",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [],
                    "nb_tasks": 0,
                    "score": 0
                }
            ],
            "nb_columns": 4,
            "nb_tasks": 2
        }
    ]
}
```

### getColumns

- Purpose: **Get all columns information for a given project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **columns properties**
- Result on failure: **empty list**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getColumns",
    "id": 887036325,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 887036325,
    "result": [
        {
            "id": "1",
            "title": "Backlog",
            "position": "1",
            "project_id": "1",
            "task_limit": "0"
        },
        {
            "id": "2",
            "title": "Ready",
            "position": "2",
            "project_id": "1",
            "task_limit": "0"
        },
        {
            "id": "3",
            "title": "Work in progress",
            "position": "3",
            "project_id": "1",
            "task_limit": "0"
        }
    ]
}
```

### getColumn

- Purpose: **Get a single column**
- Parameters:
    - **column_id** (integer, required)
- Result on success: **column properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getColumn",
    "id": 1242049935,
    "params": [
        2
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1242049935,
    "result": {
        "id": "2",
        "title": "Youpi",
        "position": "2",
        "project_id": "1",
        "task_limit": "5"
    }
}
```

### moveColumnUp

- Purpose: **Move up the column position**
- Parameters:
    - **project_id** (integer, required)
    - **column_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "moveColumnUp",
    "id": 99275573,
    "params": [
        1,
        2
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

### moveColumnDown

- Purpose: **Move down the column position**
- Parameters:
    - **project_id** (integer, required)
    - **column_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "moveColumnDown",
    "id": 957090649,
    "params": {
        "project_id": 1,
        "column_id": 2
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 957090649,
    "result": true
}
```

### updateColumn

- Purpose: **Update column properties**
- Parameters:
    - **column_id** (integer, required)
    - **title** (string, required)
    - **task_limit** (integer, optional)
    - **description** (string, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateColumn",
    "id": 480740641,
    "params": [
        2,
        "Boo",
        5
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 480740641,
    "result": true
}
```

### addColumn

- Purpose: **Add a new column**
- Parameters:
    - **project_id** (integer, required)
    - **title** (string, required)
    - **task_limit** (integer, optional)
    - **description** (string, optional)
- Result on success: **column_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "addColumn",
    "id": 638544704,
    "params": [
        1,
        "Boo"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 638544704,
    "result": 5
}
```

### removeColumn

- Purpose: **Remove a column**
- Parameters:
    - **column_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeColumn",
    "id": 1433237746,
    "params": [
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
