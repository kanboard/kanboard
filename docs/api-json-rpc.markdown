Json-RPC API
============

Protocol
--------

Kanboard use the protocol Json-RPC to interact with external programs.

JSON-RPC is a remote procedure call protocol encoded in JSON.
Almost the same thing as XML-RPC but with the JSON format.

We use the [version 2 of the protocol](http://www.jsonrpc.org/specification).
You must call the API with a `POST` HTTP request.

Credentials
-----------

The API credentials are available on the settings page.

- API end-point: `http://YOUR_SERVER/jsonrpc.php`
- Username: `jsonrpc`
- Password: Random token (API token on the settings page)

The API use the [HTTP Basic Authentication Scheme described in the RFC2617](http://www.ietf.org/rfc/rfc2617.txt).
If there is an authentication error, you got an HTTP status code `401 Not Authorized`.

Examples
--------

### Example with cURL

From the command line:

```bash
curl \
-u "jsonrpc:19ffd9709d03ce50675c3a43d1c49c1ac207f4bc45f06c5b2701fbdf8929" \
-d '{"jsonrpc": "2.0", "method": "getAllProjects", "id": 1}' \
http://localhost/kanboard/jsonrpc.php
```

Response from the server:

```json
{
    "jsonrpc":"2.0",
    "id":1,
    "result":[
        {
            "id":"1",
            "name":"API test",
            "is_active":"1",
            "token":"6bd0932fe7f4b5e6e4bc3c72800bfdef36a2c5de2f38f756dfb5bd632ebf",
            "last_modified":"1403392631"
        }
    ]
}
```

### Example with Python

Here a basic example written in Python to create a task:

```python
#!/usr/bin/env python

import requests
import json

def main():
    url = "http://demo.kanboard.net/jsonrpc.php"
    api_key = "be4271664ca8169d32af49d8e1ec854edb0290bc3588a2e356275eab9505"
    headers = {"content-type": "application/json"}

    payload = {
        "method": "createTask",
        "params": {
            "title": "Python API test",
            "project_id": 1
        },
        "jsonrpc": "2.0",
        "id": 1,
    }

    response = requests.post(
        url,
        data=json.dumps(payload),
        headers=headers,
        auth=("jsonrpc", api_key)
    )

    if response.status_code == 401:
        print "Authentication failed"
    else:
        result = response.json()

        assert result["result"] == True
        assert result["jsonrpc"]
        assert result["id"] == 1

        print "Task created successfully!"

if __name__ == "__main__":
    main()
```

Run this script from your terminal:

```bash
python jsonrpc.py
Task created successfully!
```

### Example with a PHP client:

I wrote a simple [Json-RPC Client/Server library in PHP](https://github.com/fguillot/JsonRPC), here an example:

```php
<?php

$client = new JsonRPC\Client('http://localhost:8000/jsonrpc.php');
$client->authentication('jsonrpc', '19ffd9709d03ce50675c3a43d1c49c1ac207f4bc45f06c5b2701fbdf8929');

print_r($client->getAllProjects());

```

The response:

```
Array
(
    [0] => Array
        (
            [id] => 1
            [name] => API test
            [is_active] => 1
            [token] => 6bd0932fe7f4b5e6e4bc3c72800bfdef36a2c5de2f38f756dfb5bd632ebf
            [last_modified] => 1403392631
        )

)
```

Procedures
----------

### getVersion

- Purpose: **Get the application version**
- Parameters: none
- Result: **version** (Example: 1.0.12, master)

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getVersion",
    "id": 1661138292
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1661138292,
    "result": "1.0.13"
}
```

### getTimezone

- Purpose: **Get the application timezone**
- Parameters: none
- Result on success: **Timezone** (Example: UTC, Europe/Paris)
- Result on failure: **Default timezone** (UTC)

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getTimezone",
    "id": 1661138292
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1661138292,
    "result": "Europe\/Paris"
}
```

### createProject

- Purpose: **Create a new project**
- Parameters:
    - **name** (string, required)
    - **description** (string, optional)
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

### getProjectById

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
        "last_modified": "1410263246",
        "is_public": "0",
        "description": "A sample project"
    }
}
```

### getProjectByName

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
        "last_modified": "0",
        "is_public": "0",
        "description": "A sample project"
    }
}
```

### getAllProjects

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
    "id": 134982303
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 134982303,
    "result": [
        {
            "id": "2",
            "name": "PHP client",
            "is_active": "1",
            "token": "",
            "last_modified": "0",
            "is_public": "0",
            "description": "PHP client project"
        },
        {
            "id": "1",
            "name": "Test",
            "is_active": "1",
            "token": "",
            "last_modified": "0",
            "is_public": "0",
            "description": "Test project"
        }
    ]
}
```

### updateProject

- Purpose: **Update a project**
- Parameters:
    - **id** (integer, required)
    - **name** (string, required)
    - **is_active** (integer, optional)
    - **token** (string, optional)
    - **is_public** (integer, optional)
    - **description** (string, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateProject",
    "id": 1853996288,
    "params": {
        "id": 1,
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

### removeProject

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

### enableProject

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

### disableProject

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

### enableProjectPublicAccess

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

### disableProjectPublicAccess

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

### getProjectActivity

- Purpose: **Get Activityfeed for Project(s)**
- Parameters:
    - **project_ids** (integer array, required)
    - **limit** (integer, optional)
    - **start** (timestamp, optional)
    - **end** (timestamp, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectActivity",
    "id": 942472945,
    "params": [
        "project_ids": [1,2]
    ]
}
```

### getMembers

- Purpose: **Get members of a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: Key/value pair of user_id and username
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getMembers",
    "id": 1944388643,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1944388643,
    "result": {
        "1": "user1",
        "2": "user2",
        "3": "user3"
    }
}
```

### revokeUser

- Purpose: **Revoke user access for a given project**
- Parameters:
    - **project_id** (integer, required)
    - **user_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "revokeUser",
    "id": 251218350,
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
    "id": 251218350,
    "result": true
}
```

### allowUser

- Purpose: **Grant user access for a given project**
- Parameters:
    - **project_id** (integer, required)
    - **user_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "allowUser",
    "id": 2111451404,
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
    "id": 2111451404,
    "result": true
}
```


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

### getSwimlanes

- Purpose: **Get the list of enabled swimlanes of a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **swimlane properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getSwimlanes",
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
    "result": [
        {
            "id": "0",
            "name": "Default"
        },
        {
            "id": "2",
            "name": "Version 7.0"
        },
    ]
}
```

### getAllSwimlanes

- Purpose: **Get the list of all swimlanes of a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **swimlane properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllSwimlanes",
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
    "result": [
        {
            "id": "0",
            "name": "Default"
        },
        {
            "id": "3",
            "name": "Version 1.0",
            "is_active": "0",
            "position": 1,
            "project_id": 2
        },
        {
            "id": "2",
            "name": "Version 7.0",
            "is_active": "1",
            "position": 2,
            "project_id": 2
        }
    ]
}
```

### getSwimlane

- Purpose: **Get the a swimlane**
- Parameters:
    - **project_id** (integer, required)
    - **name** (string, required)
- Result on success: **swimlane properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getSwimlane",
    "id": 1242049935,
    "params": [
        2,
        "Version 1.0"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1242049935,
    "result": {
        "id": "3",
        "name": "Version 1.0",
        "is_active": "0",
        "position": 2,
        "project_id": 2
    }
}
```

### moveSwimlaneUp

- Purpose: **Move up the swimlane position**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "moveSwimlaneUp",
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

### moveSwimlaneDown

- Purpose: **Move down the swimlane position**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "moveSwimlaneDown",
    "id": 957090649,
    "params": {
        "project_id": 1,
        "swimlane_id": 2
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

### updateSwimlane

- Purpose: **Update swimlane properties**
- Parameters:
    - **swimlane_id** (integer, required)
    - **name** (string, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateSwimlane",
    "id": 480740641,
    "params": [
        2,
        "Version 4.1"
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

### addSwimlane

- Purpose: **Add a new swimlane**
- Parameters:
    - **project_id** (integer, required)
    - **name** (string, required)
- Result on success: **swimlane_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "addSwimlane",
    "id": 638544704,
    "params": [
        1,
        "Version 1.0"
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

### removeSwimlane

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

### disableSwimlane

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

### enableSwimlane

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

### getAvailableActions

- Purpose: **Get list of available actions**
- Parameters: none
- Result on success: **list of actions**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAvailableActions",
    "id": 1433237746,
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": {
        "TaskLogMoveAnotherColumn" : "Add a comment logging moving the task between columns",
        "TaskAssignColorColumn" : "Assign a color when the task is moved to a specific column",
        "TaskAssignColorUser" : "Assign a color to a specific user",
        "TaskAssignCategoryColor" : "Assign automatically a category based on a color",
        "TaskAssignColorCategory" : "Assign automatically a color based on a category",
        "TaskAssignSpecificUser" : "Assign the task to a specific user",
        "TaskAssignCurrentUser" : "Assign the task to the person who does the action",
        "TaskAssignUser" : "Change the assignee based on an external username",
        "TaskAssignCategoryLabel" : "Change the category based on an external label",
        "TaskClose" : "Close a task",
        "CommentCreation" : "Create a comment from an external provider",
        "TaskCreation" : "Create a task from an external provider",
        "TaskDuplicateAnotherProject" : "Duplicate the task to another project",
        "TaskMoveAnotherProject" : "Move the task to another project",
        "TaskOpen" : "Open a task"
    }
}
```

### getAvailableEvents

- Purpose: **Get list of available events**
- Parameters: none
- Result on success: **list of events**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAvailableEvents",
    "id": 1433237746,
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": {
        "bitbucket.webhook.commit" : "Bitbucket commit received",
        "task.close" : "Closing a task",
        "github.webhook.commit" : "Github commit received",
        "github.webhook.issue.assignee" : "Github issue assignee change",
        "github.webhook.issue.closed" : "Github issue closed",
        "github.webhook.issue.commented" : "Github issue comment created",
        "github.webhook.issue.label" : "Github issue label change",
        "github.webhook.issue.opened" : "Github issue opened",
        "github.webhook.issue.reopened" : "Github issue reopened",
        "gitlab.webhook.commit" : "Gitlab commit received",
        "gitlab.webhook.issue.closed" : "Gitlab issue closed",
        "gitlab.webhook.issue.opened" : "Gitlab issue opened",
        "task.move.column" : "Move a task to another column",
        "task.open" : "Open a closed task",
        "task.assignee_change" : "Task assignee change",
        "task.create" : "Task creation",
        "task.create_update" : "Task creation or modification",
        "task.update" : "Task modification"
    }
}
```

### getCompatibleEvents

- Purpose: **Get list of events compatible with an action**
- Parameters:
    - **action_name** (string, required)
- Result on success: **list of events**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getCompatibleEvents",
    "id": 1433237746,
    "params": [
        "TaskAssignSpecificUser"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": {
        "task.move.column" : "Move a task to another column",
        "task.create_update" : "Task creation or modification",
    }
}
```

### getActions

- Purpose: **Get list of actions for a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **list of actions info**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getActions",
    "id": 1433237746,
    "params": [
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": [
        {
            "id" : "13",
            "project_id" : "2",
            "event_name" : "task.move.column",
            "action_name" : "TaskAssignSpecificUser",
            "params" : {
                "column_id" : "5",
                "user_id" : "1"
            }
        }
    ]
}
```

### createAction

- Purpose: **Create an action**
- Parameters:
    - **project_id** (integer, required)
    - **event_name** (string, required)
    - **action_name** (string, required)
    - **params** (list of string pairs, required)
- Result on success: **action_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createAction",
    "id": 1433237746,
    "params": {
        "project_id" : "2",
        "event_name" : "task.move.column",
        "action_name" : "TaskAssignSpecificUser",
        "params" : {
            "column_id" : "3",
            "user_id" : "2"
        }
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": 14
}
```

### removeAction

- Purpose: **Remove an action**
- Parameters:
    - **action_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAvailableEvents",
    "id": 1433237746,
    "params": [
        "2",
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

### createTask

- Purpose: **Create a new task**
- Parameters:
    - **title** (string, required)
    - **project_id** (integer, required)
    - **color_id** (string, optional)
    - **column_id** (integer, optional)
    - **owner_id** (integer, optional)
    - **creator_id** (integer, optional)
    - **date_due**: ISO8601 format (string, optional)
    - **description** Markdown content (string, optional)
    - **category_id** (integer, optional)
    - **score** (integer, optional)
    - **swimlane_id** (integer, optional)
    - **recurrence_status**  (integer, optional)
    - **recurrence_trigger**  (integer, optional)
    - **recurrence_factor**  (integer, optional)
    - **recurrence_timeframe**  (integer, optional)
    - **recurrence_basedate**  (integer, optional)
- Result on success: **task_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createTask",
    "id": 1176509098,
    "params": {
        "owner_id": 1,
        "creator_id": 0,
        "date_due": "",
        "description": "",
        "category_id": 0,
        "score": 0,
        "title": "Test",
        "project_id": 1,
        "color_id": "green",
        "column_id": 2,
        "recurrence_status": 0,
        "recurrence_trigger": 0,
        "recurrence_factor": 0,
        "recurrence_timeframe": 0,
        "recurrence_basedate": 0
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1176509098,
    "result": 3
}
```

### getTask

- Purpose: **Get task information**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **task properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getTask",
    "id": 700738119,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 700738119,
    "result": {
        "id": "1",
        "title": "Task #1",
        "description": "",
        "date_creation": "1409963206",
        "color_id": "blue",
        "project_id": "1",
        "column_id": "2",
        "owner_id": "1",
        "position": "1",
        "is_active": "1",
        "date_completed": null,
        "score": "0",
        "date_due": "0",
        "category_id": "0",
        "creator_id": "0",
        "date_modification": "1409963206",
        "reference": "",
        "date_started": null,
        "time_spent": "0",
        "time_estimated": "0",
        "swimlane_id": "0",
        "date_moved": "1430875287",
        "recurrence_status": "0",
        "recurrence_trigger": "0",
        "recurrence_factor": "0",
        "recurrence_timeframe": "0",
        "recurrence_basedate": "0",
        "recurrence_parent": null,
        "recurrence_child": null
        }
}
```

### getAllTasks

- Purpose: **Get all available tasks**
- Parameters:
    - **project_id** (integer, required)
    - **status_id**: The value 1 for active tasks and 0 for inactive (integer, required)
- Result on success: **List of tasks**
- Result on failure: **false**

Request example to fetch all tasks on the board:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllTasks",
    "id": 133280317,
    "params": {
        "project_id": 1,
        "status_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": [
        {
            "id": "1",
            "title": "Task #1",
            "description": "",
            "date_creation": "1409961789",
            "color_id": "blue",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "position": "1",
            "is_active": "1",
            "date_completed": null,
            "score": "0",
            "date_due": "0",
            "category_id": "0",
            "creator_id": "0",
            "date_modification": "1409961789",
            "reference": "",
            "date_started": null,
            "time_spent": "0",
            "time_estimated": "0",
            "swimlane_id": "0",
            "date_moved": "1430783191",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null
        },
        {
            "id": "2",
            "title": "Test",
            "description": "",
            "date_creation": "1409962115",
            "color_id": "green",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "position": "2",
            "is_active": "1",
            "date_completed": null,
            "score": "0",
            "date_due": "0",
            "category_id": "0",
            "creator_id": "0",
            "date_modification": "1409962115",
            "reference": "",
            "date_started": null,
            "time_spent": "0",
            "time_estimated": "0",
            "swimlane_id": "0",
            "date_moved": "1430783191",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null
        },
        ...
    ]
}
```

### getOverdueTasks

- Purpose: **Get all overdue tasks**
- Result on success: **List of tasks**
- Result on failure: **false**

Request example to fetch all tasks on the board:

```json
{
    "jsonrpc": "2.0",
    "method": "getOverdueTasks",
    "id": 133280317
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": [
        {
            "id": "1",
            "title": "Task #1",
            "date_due": "1409961789",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        {
            "id": "2",
            "title": "Test",
            "date_due": "1409962115",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        ...
    ]
}
```

### updateTask

- Purpose: **Update a task**
- Parameters:
    - **id** (integer, required)
    - **title** (string, optional)
    - **project_id** (integer, optional)
    - **color_id** (string, optional)
    - **column_id** (integer, optional)
    - **owner_id** (integer, optional)
    - **creator_id** (integer, optional)
    - **date_due**: ISO8601 format (string, optional)
    - **description** Markdown content (string, optional)
    - **category_id** (integer, optional)
    - **score** (integer, optional)
    - **swimlane_id** (integer, optional)
    - **recurrence_status**  (integer, optional)
    - **recurrence_trigger**  (integer, optional)
    - **recurrence_factor**  (integer, optional)
    - **recurrence_timeframe**  (integer, optional)
    - **recurrence_basedate**  (integer, optional)
- Result on success: **true**
- Result on failure: **false**

Request example to change the task color:

```json
{
    "jsonrpc": "2.0",
    "method": "updateTask",
    "id": 1406803059,
    "params": {
        "id": 1,
        "color_id": "blue"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1406803059,
    "result": true
}
```

### openTask

- Purpose: **Set a task to the status open**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "openTask",
    "id": 1888531925,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1888531925,
    "result": true
}
```

### closeTask

- Purpose: **Set a task to the status close**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "closeTask",
    "id": 1654396960,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1654396960,
    "result": true
}
```

### removeTask

- Purpose: **Remove a task**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeTask",
    "id": 1423501287,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1423501287,
    "result": true
}
```

### moveTaskPosition

- Purpose: **Move a task to another column or another position**
- Parameters:
    - **project_id** (integer, required)
    - **task_id** (integer, required)
    - **column_id** (integer, required)
    - **position** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "moveTaskPosition",
    "id": 117211800,
    "params": {
        "project_id": 1,
        "task_id": 1,
        "column_id": 2,
        "position": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 117211800,
    "result": true
}
```

### createUser

- Purpose: **Create a new user**
- Parameters:
    - **username** Must be unique (string, required)
    - **password** Must have at least 6 characters (string, required)
    - **name** (string, optional)
    - **email** (string, optional)
    - **is_admin** Set the value 1 for admins or 0 for regular users (integer, optional)
    - **default_project_id** (integer, optional)
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

### createLdapUser

- Purpose: **Create a new user authentified by LDAP**
- Parameters:
    - **username** (string, optional if email is set)
    - **email** (string, optional if username is set)
    - **is_admin** Set the value 1 for admins or 0 for regular users (integer, optional)
    - **default_project_id** (integer, optional)
- Result on success: **user_id**
- Result on failure: **false**

The user will only be created if a matching is found on the LDAP server.
Username or email (or both) must be provided.

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createLdapUser",
    "id": 1518863034,
    "params": {
        "username": "biloute",
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

### getUser

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
        "is_admin": "0",
        "default_project_id": "0",
        "is_ldap_user": "0",
        "name": "",
        "email": "",
        "google_id": null,
        "github_id": null,
        "notifications_enabled": "0"
    }
}
```

### getAllUsers

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
            "is_admin": "0",
            "default_project_id": "0",
            "is_ldap_user": "0",
            "notifications_enabled": "0",
            "google_id": null,
            "github_id": null
        },
        ...
    ]
}
```

### updateUser

- Purpose: **Update a user**
- Parameters:
    - **id** (integer)
    - **username** (string, optional)
    - **name** (string, optional)
    - **email** (string, optional)
    - **is_admin** (integer, optional)
    - **default_project_id** (integer, optional)
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
        "is_admin": 1
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

### removeUser

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


### createCategory

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

### getCategory

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

### getAllCategories

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

### updateCategory

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

### removeCategory

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


### createComment

- Purpose: **Create a new comment**
- Parameters:
    - **task_id** (integer, required)
    - **user_id** (integer, required)
    - **content** Markdown content (string, required)
- Result on success: **comment_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createComment",
    "id": 1580417921,
    "params": {
        "task_id": 1,
        "user_id": 1,
        "content": "Comment #1"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1580417921,
    "result": 11
}
```

### getComment

- Purpose: **Get comment information**
- Parameters:
    - **comment_id** (integer, required)
- Result on success: **comment properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getComment",
    "id": 867839500,
    "params": {
        "comment_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 867839500,
    "result": {
        "id": "1",
        "task_id": "1",
        "user_id": "1",
        "date": "1410881970",
        "comment": "Comment #1",
        "username": "admin",
        "name": null
    }
}
```

### getAllComments

- Purpose: **Get all available comments**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **List of comments**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllComments",
    "id": 148484683,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 148484683,
    "result": [
        {
            "id": "1",
            "date": "1410882272",
            "task_id": "1",
            "user_id": "1",
            "comment": "Comment #1",
            "username": "admin",
            "name": null
        },
        ...
    ]
}
```

### updateComment

- Purpose: **Update a comment**
- Parameters:
    - **id** (integer, required)
    - **content** Markdown content (string, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateComment",
    "id": 496470023,
    "params": {
        "id": 1,
        "content": "Comment #1 updated"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1493368950,
    "result": true
}
```

### removeComment

- Purpose: **Remove a comment**
- Parameters:
    - **comment_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeComment",
    "id": 328836871,
    "params": {
        "comment_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 328836871,
    "result": true
}
```


### createSubtask

- Purpose: **Create a new subtask**
- Parameters:
    - **task_id** (integer, required)
    - **title** (integer, required)
    - **user_id** (int, optional)
    - **time_estimated** (int, optional)
    - **time_spent** (int, optional)
    - **status** (int, optional)
- Result on success: **subtask_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createSubtask",
    "id": 2041554661,
    "params": {
        "task_id": 1,
        "title": "Subtask #1"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2041554661,
    "result": 45
}
```

### getSubtask

- Purpose: **Get subtask information**
- Parameters:
    - **subtask_id** (integer)
- Result on success: **subtask properties**
- Result on failure: **null**

```json
{
    "jsonrpc": "2.0",
    "method": "getSubtask",
    "id": 133184525,
    "params": {
        "subtask_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 133184525,
    "result": {
        "id": "1",
        "title": "Subtask #1",
        "status": "0",
        "time_estimated": "0",
        "time_spent": "0",
        "task_id": "1",
        "user_id": "0"
    }
}
```

### getAllSubtasks

- Purpose: **Get all available subtasks**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **List of subtasks**
- Result on failure: **false**

```json
{
    "jsonrpc": "2.0",
    "method": "getAllSubtasks",
    "id": 2087700490,
    "params": {
        "task_id": 1
    }
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2087700490,
    "result": [
        {
            "id": "1",
            "title": "Subtask #1",
            "status": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "task_id": "1",
            "user_id": "0",
            "username": null,
            "name": null,
            "status_name": "Todo"
        },
        ...
    ]
}
```

### updateSubtask

- Purpose: **Update a subtask**
- Parameters:
    - **id** (integer, required)
    - **task_id** (integer, required)
    - **title** (integer, optional)
    - **user_id** (integer, optional)
    - **time_estimated** (integer, optional)
    - **time_spent** (integer, optional)
    - **status** (integer, optional)
- Result on success: **true**
- Result on failure: **false**

Request examples:

```json
{
    "jsonrpc": "2.0",
    "method": "updateSubtask",
    "id": 191749979,
    "params": {
        "id": 1,
        "task_id": 1,
        "status": 1,
        "time_spent": 5,
        "user_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 191749979,
    "result": true
}
```

### removeSubtask

- Purpose: **Remove a subtask**
- Parameters:
    - **subtask_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

```json
{
    "jsonrpc": "2.0",
    "method": "removeSubtask",
    "id": 1382487306,
    "params": {
        "subtask_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1382487306,
    "result": true
}
```
