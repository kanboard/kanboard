API Subtask procedures[¶](#api-subtask-procedures "Ссылка на этот заголовок")

=============================================================================



createSubtask[¶](#createsubtask "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Create a new subtask**

-   Parameters:

    -   **task\_id** (integer, required)

    -   **title** (integer, required)

    -   **user\_id** (int, optional)

    -   **time\_estimated** (int, optional)

    -   **time\_spent** (int, optional)

    -   **status** (int, optional)

-   Result on success: **subtask\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "createSubtask",

        "id": 2041554661,

        "params": {

            "task_id": 1,

            "title": "Subtask #1"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 2041554661,

        "result": 45

    }



getSubtask[¶](#getsubtask "Ссылка на этот заголовок")

-----------------------------------------------------



-   Purpose: **Get subtask information**

-   Parameters:

    -   **subtask\_id** (integer)

-   Result on success: **subtask properties**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getSubtask",

        "id": 133184525,

        "params": {

            "subtask_id": 1

        }

    }



Response example:



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



getAllSubtasks[¶](#getallsubtasks "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Get all available subtasks**

-   Parameters:

    -   **task\_id** (integer, required)

-   Result on success: **List of subtasks**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getAllSubtasks",

        "id": 2087700490,

        "params": {

            "task_id": 1

        }



Response example:



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



updateSubtask[¶](#updatesubtask "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Update a subtask**

-   Parameters:

    -   **id** (integer, required)

    -   **task\_id** (integer, required)

    -   **title** (integer, optional)

    -   **user\_id** (integer, optional)

    -   **time\_estimated** (integer, optional)

    -   **time\_spent** (integer, optional)

    -   **status** (integer, optional)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



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



Response example:



    {

        "jsonrpc": "2.0",

        "id": 191749979,

        "result": true

    }



removeSubtask[¶](#removesubtask "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Remove a subtask**

-   Parameters:

    -   **subtask\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeSubtask",

        "id": 1382487306,

        "params": {

            "subtask_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1382487306,

        "result": true

    }



### [Оглавление](index.markdown)



-   [API Subtask procedures](#)

    -   [createSubtask](#createsubtask)

    -   [getSubtask](#getsubtask)

    -   [getAllSubtasks](#getallsubtasks)

    -   [updateSubtask](#updatesubtask)

    -   [removeSubtask](#removesubtask)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

