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

### createProject

- Purpose: **Create a new project**
- Parameters: **name** (string)
- Result on success: **true**
- Result on failure: **false**

### getProjectById

- Purpose: **Get project information**
- Parameters: **project_id** (integer)
- Result on success: **project properties**
- Result on failure: **null**

### getProjectByName

- Purpose: **Get project information**
- Parameters: **name** (string)
- Result on success: **project properties**
- Result on failure: **null**

### getAllProjects

- Purpose: **Get all available projects**
- Parameters: **none**
- Result on success: **List of projects**
- Result on failure: **false**

### updateProject

- Purpose: **Update a project**
- Parameters: Key/value pair composed of the **id** (integer), **name** (string), **is_active** (integer, optional)
- Result on success: **true**
- Result on failure: **false**

### removeProject

- Purpose: **Remove a project**
- Parameters: **project_id** (integer)
- Result on success: **true**
- Result on failure: **false**




### getBoard

- Purpose: **Get all necessary information to display a board**
- Parameters: **project_id** (integer)
- Result on success: **board properties**
- Result on failure: **null**

### getColumns

- Purpose: **Get all columns information for a given project**
- Parameters: **project_id** (integer)
- Result on success: **columns properties**
- Result on failure: **null**

### moveColumnUp

- Purpose: **Move up the column position**
- Parameters: **project_id** (integer), **column_id** (integer)
- Result on success: **true**
- Result on failure: **false**

### moveColumnDown

- Purpose: **Move down the column position**
- Parameters: **project_id** (integer), **column_id** (integer)
- Result on success: **true**
- Result on failure: **false**

### updateColumn

- Purpose: **Update column properties**
- Parameters: **column_id** (integer), **values** (**title** string, **task_limit** integer)
- Result on success: **true**
- Result on failure: **false**

### addColumn

- Purpose: **Add a new column**
- Parameters: **project_id** (integer), **values** (**title** string, **task_limit** integer)
- Result on success: **true**
- Result on failure: **false**

### removeColumn

- Purpose: **Remove a column**
- Parameters: **column_id** (integer)
- Result on success: **true**
- Result on failure: **false**

### getAllowedUsers

- Purpose: **Get allowed users for a given project**
- Parameters: **project_id** (integer)
- Result on success: Key/value pair of user_id and username
- Result on failure: **false**

### revokeUser

- Purpose: **Revoke user access for a given project**
- Parameters: **project_id** (integer), **user_id** (integer)
- Result on success: **true**
- Result on failure: **false**

### allowUser

- Purpose: **Grant user access for a given project**
- Parameters: **project_id** (integer), **user_id** (integer)
- Result on success: **true**
- Result on failure: **false**




### createTask

- Purpose: **Create a new task**
- Parameters: Key/value pair composed of the **title** (string), **description** (string, optional), **color_id** (string), **project_id** (integer), **column_id** (integer), **owner_id** (integer, optional), **score** (integer, optional), **date_due** (integer, optional), **category_id** (integer, optional)
- Result on success: **true**
- Result on failure: **false**

### getTask

- Purpose: **Get task information**
- Parameters: **task_id** (integer)
- Result on success: **task properties**
- Result on failure: **null**

### getAllTasks

- Purpose: **Get all available tasks**
- Parameters: **project_id** (integer)
- Result on success: **List of tasks**
- Result on failure: **false**

### updateTask

- Purpose: **Update a task**
- Parameters: Key/value pair composed of the **id** (integer), **title** (string), **description** (string, optional), **color_id** (string), **project_id** (integer), **column_id** (integer), **owner_id** (integer, optional), **score** (integer, optional), **date_due** (integer, optional), **category_id** (integer, optional)
- Result on success: **true**
- Result on failure: **false**

### openTask

- Purpose: **Set a task to the status open**
- Parameters: **task_id** (integer)
- Result on success: **true**
- Result on failure: **false**

### closeTask

- Purpose: **Set a task to the status close**
- Parameters: **task_id** (integer)
- Result on success: **true**
- Result on failure: **false**

### removeTask

- Purpose: **Remove a task**
- Parameters: **task_id** (integer)
- Result on success: **true**
- Result on failure: **false**




### createUser

- Purpose: **Create a new user**
- Parameters: Key/value pair composed of the **username** (string), **password** (string), **confirmation** (string), **name** (string, optional), **email** (string, optional), is_admin (integer, optional), **default_project_id** (integer, optional)
- Result on success: **true**
- Result on failure: **false**

### getUser

- Purpose: **Get user information**
- Parameters: **user_id** (integer)
- Result on success: **user properties**
- Result on failure: **null**

### getAllUsers

- Purpose: **Get all available users**
- Parameters: **none**
- Result on success: **List of users**
- Result on failure: **false**

### updateUser

- Purpose: **Update a user**
- Parameters: Key/value pair composed of the **id** (integer), **username** (string), **password** (string), **confirmation** (string), **name** (string, optional), **email** (string, optional), is_admin (integer, optional), **default_project_id** (integer, optional)
- Result on success: **true**
- Result on failure: **false**

### removeUser

- Purpose: **Remove a user**
- Parameters: **user_id** (integer)
- Result on success: **true**
- Result on failure: **false**




### createCategory

- Purpose: **Create a new category**
- Parameters: Key/value pair composed of the **name** (string), **project_id** (integer)
- Result on success: **true**
- Result on failure: **false**

### getCategory

- Purpose: **Get category information**
- Parameters: **category_id** (integer)
- Result on success: **category properties**
- Result on failure: **null**

### getAllCategories

- Purpose: **Get all available categories**
- Parameters: **project_id** (integer)
- Result on success: **List of categories**
- Result on failure: **false**

### updateCategory

- Purpose: **Update a category**
- Parameters: Key/value pair composed of the **id** (integer), **name** (string), **project_id** (integer)
- Result on success: **true**
- Result on failure: **false**

### removeCategory

- Purpose: **Remove a category**
- Parameters: **category_id** (integer)
- Result on success: **true**
- Result on failure: **false**




### createComment

- Purpose: **Create a new comment**
- Parameters: Key/value pair composed of the **task_id** (integer), **user_id** (integer), **comment** (string)
- Result on success: **true**
- Result on failure: **false**

### getComment

- Purpose: **Get comment information**
- Parameters: **comment_id** (integer)
- Result on success: **comment properties**
- Result on failure: **null**

### getAllComments

- Purpose: **Get all available comments**
- Parameters: **none**
- Result on success: **List of comments**
- Result on failure: **false**

### updateComment

- Purpose: **Update a comment**
- Parameters: Key/value pair composed of the **id** (integer), **task_id** (integer), **user_id** (integer), **comment** (string)
- Result on success: **true**
- Result on failure: **false**

### removeComment

- Purpose: **Remove a comment**
- Parameters: **comment_id** (integer)
- Result on success: **true**
- Result on failure: **false**




### createSubtask

- Purpose: **Create a new subtask**
- Parameters: Key/value pair composed of the **title** (integer), time_estimated (int, optional), task_id (int), user_id (int, optional)
- Result on success: **true**
- Result on failure: **false**

### getSubtask

- Purpose: **Get subtask information**
- Parameters: **subtask_id** (integer)
- Result on success: **subtask properties**
- Result on failure: **null**

### getAllSubtasks

- Purpose: **Get all available subtasks**
- Parameters: **none**
- Result on success: **List of subtasks**
- Result on failure: **false**

### updateSubtask

- Purpose: **Update a subtask**
- Parameters: Key/value pair composed of the **id** (integer), **title** (integer), status (integer, optional) time_estimated (int, optional), time_spent (int, optional), task_id (int), user_id (int, optional)
- Result on success: **true**
- Result on failure: **false**

### removeSubtask

- Purpose: **Remove a subtask**
- Parameters: **subtask_id** (integer)
- Result on success: **true**
- Result on failure: **false**
