API Task Procedures[¶](#api-task-procedures "Ссылка на этот заголовок")

=======================================================================



createTask[¶](#createtask "Ссылка на этот заголовок")

-----------------------------------------------------



-   Purpose: **Create a new task**

-   Parameters:

    -   **title** (string, required)

    -   **project\_id** (integer, required)

    -   **color\_id** (string, optional)

    -   **column\_id** (integer, optional)

    -   **owner\_id** (integer, optional)

    -   **creator\_id** (integer, optional)

    -   **date\_due**: ISO8601 format (string, optional)

    -   **description** Markdown content (string, optional)

    -   **category\_id** (integer, optional)

    -   **score** (integer, optional)

    -   **swimlane\_id** (integer, optional)

    -   **priority** (integer, optional)

    -   **recurrence\_status** (integer, optional)

    -   **recurrence\_trigger** (integer, optional)

    -   **recurrence\_factor** (integer, optional)

    -   **recurrence\_timeframe** (integer, optional)

    -   **recurrence\_basedate** (integer, optional)

-   Result on success: **task\_id**

-   Result on failure: **false**



Request example:



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



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1176509098,

        "result": 3

    }



getTask[¶](#gettask "Ссылка на этот заголовок")

-----------------------------------------------



-   Purpose: **Get task by the unique id**

-   Parameters:

    -   **task\_id** (integer, required)

-   Result on success: **task properties**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getTask",

        "id": 700738119,

        "params": {

            "task_id": 1

        }

    }



Response example:



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

            "recurrence_child": null,

            "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=1&project_id=1",

            "color": {

                "name": "Yellow",

                "background": "rgb(245, 247, 196)",

                "border": "rgb(223, 227, 45)"

            }

        }

    }



getTaskByReference[¶](#gettaskbyreference "Ссылка на этот заголовок")

---------------------------------------------------------------------



-   Purpose: **Get task by the external reference**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **reference** (string, required)

-   Result on success: **task properties**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getTaskByReference",

        "id": 1992081213,

        "params": {

            "project_id": 1,

            "reference": "TICKET-1234"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1992081213,

        "result": {

            "id": "5",

            "title": "Task with external ticket number",

            "description": "[Link to my ticket](http:\/\/my-ticketing-system\/1234)",

            "date_creation": "1434227446",

            "color_id": "yellow",

            "project_id": "1",

            "column_id": "1",

            "owner_id": "0",

            "position": "4",

            "is_active": "1",

            "date_completed": null,

            "score": "0",

            "date_due": "0",

            "category_id": "0",

            "creator_id": "0",

            "date_modification": "1434227446",

            "reference": "TICKET-1234",

            "date_started": null,

            "time_spent": "0",

            "time_estimated": "0",

            "swimlane_id": "0",

            "date_moved": "1434227446",

            "recurrence_status": "0",

            "recurrence_trigger": "0",

            "recurrence_factor": "0",

            "recurrence_timeframe": "0",

            "recurrence_basedate": "0",

            "recurrence_parent": null,

            "recurrence_child": null,

            "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=5&project_id=1"

        }

    }



getAllTasks[¶](#getalltasks "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Get all available tasks**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **status\_id**: The value 1 for active tasks and 0 for inactive (integer, required)

-   Result on success: **List of tasks**

-   Result on failure: **false**



Request example to fetch all tasks on the board:



    {

        "jsonrpc": "2.0",

        "method": "getAllTasks",

        "id": 133280317,

        "params": {

            "project_id": 1,

            "status_id": 1

        }

    }



Response example:



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

                "recurrence_child": null,

                "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=1&project_id=1"

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

                "recurrence_child": null,

                "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=2&project_id=1"

            },

            ...

        ]

    }



getOverdueTasks[¶](#getoverduetasks "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Get all overdue tasks**

-   Result on success: **List of tasks**

-   Result on failure: **false**



Request example to fetch all tasks on the board:



    {

        "jsonrpc": "2.0",

        "method": "getOverdueTasks",

        "id": 133280317

    }



Response example:



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



getOverdueTasksByProject[¶](#getoverduetasksbyproject "Ссылка на этот заголовок")

---------------------------------------------------------------------------------



-   Purpose: **Get all overdue tasks for a special project**

-   Result on success: **List of tasks**

-   Result on failure: **false**



Request example to fetch all tasks on the board:



    {

        "jsonrpc": "2.0",

        "method": "getOverdueTasksByProject",

        "id": 133280317,

        "params": {

            "project_id": 1

        }

    }



Response example:



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



updateTask[¶](#updatetask "Ссылка на этот заголовок")

-----------------------------------------------------



-   Purpose: **Update a task**

-   Parameters:

    -   **id** (integer, required)

    -   **title** (string, optional)

    -   **color\_id** (string, optional)

    -   **owner\_id** (integer, optional)

    -   **date\_due**: ISO8601 format (string, optional)

    -   **description** Markdown content (string, optional)

    -   **category\_id** (integer, optional)

    -   **score** (integer, optional)

    -   **priority** (integer, optional)

    -   **recurrence\_status** (integer, optional)

    -   **recurrence\_trigger** (integer, optional)

    -   **recurrence\_factor** (integer, optional)

    -   **recurrence\_timeframe** (integer, optional)

    -   **recurrence\_basedate** (integer, optional)

-   Result on success: **true**

-   Result on failure: **false**



Request example to change the task color:



    {

        "jsonrpc": "2.0",

        "method": "updateTask",

        "id": 1406803059,

        "params": {

            "id": 1,

            "color_id": "blue"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1406803059,

        "result": true

    }



openTask[¶](#opentask "Ссылка на этот заголовок")

-------------------------------------------------



-   Purpose: **Set a task to the status open**

-   Parameters:

    -   **task\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "openTask",

        "id": 1888531925,

        "params": {

            "task_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1888531925,

        "result": true

    }



closeTask[¶](#closetask "Ссылка на этот заголовок")

---------------------------------------------------



-   Purpose: **Set a task to the status close**

-   Parameters:

    -   **task\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "closeTask",

        "id": 1654396960,

        "params": {

            "task_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1654396960,

        "result": true

    }



removeTask[¶](#removetask "Ссылка на этот заголовок")

-----------------------------------------------------



-   Purpose: **Remove a task**

-   Parameters:

    -   **task\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeTask",

        "id": 1423501287,

        "params": {

            "task_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1423501287,

        "result": true

    }



moveTaskPosition[¶](#movetaskposition "Ссылка на этот заголовок")

-----------------------------------------------------------------



-   Purpose: **Move a task to another column, position or swimlane inside the same board**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **task\_id** (integer, required)

    -   **column\_id** (integer, required)

    -   **position** (integer, required)

    -   **swimlane\_id** (integer, optional, default=0)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



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



Response example:



    {

        "jsonrpc": "2.0",

        "id": 117211800,

        "result": true

    }



moveTaskToProject[¶](#movetasktoproject "Ссылка на этот заголовок")

-------------------------------------------------------------------



-   Purpose: **Move a task to another project**

-   Parameters:

    -   **task\_id** (integer, required)

    -   **project\_id** (integer, required)

    -   **swimlane\_id** (integer, optional)

    -   **column\_id** (integer, optional)

    -   **category\_id** (integer, optional)

    -   **owner\_id** (integer, optional)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "moveTaskToProject",

        "id": 15775829,

        "params": [

            4,

            5

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 15775829,

        "result": true

    }



duplicateTaskToProject[¶](#duplicatetasktoproject "Ссылка на этот заголовок")

-----------------------------------------------------------------------------



-   Purpose: **Move a task to another column or another position**

-   Parameters:

    -   **task\_id** (integer, required)

    -   **project\_id** (integer, required)

    -   **swimlane\_id** (integer, optional)

    -   **column\_id** (integer, optional)

    -   **category\_id** (integer, optional)

    -   **owner\_id** (integer, optional)

-   Result on success: **task\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "duplicateTaskToProject",

        "id": 1662458687,

        "params": [

            5,

            7

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1662458687,

        "result": 6

    }



searchTasks[¶](#searchtasks "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Find tasks by using the search engine**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **query** (string, required)

-   Result on success: **list of tasks**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "searchTasks",

        "id": 1468511716,

        "params": {

            "project_id": 2,

            "query": "assignee:nobody"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1468511716,

        "result": [

            {

                "nb_comments": "0",

                "nb_files": "0",

                "nb_subtasks": "0",

                "nb_completed_subtasks": "0",

                "nb_links": "0",

                "nb_external_links": "0",

                "is_milestone": null,

                "id": "3",

                "reference": "",

                "title": "T3",

                "description": "",

                "date_creation": "1461365164",

                "date_modification": "1461365164",

                "date_completed": null,

                "date_started": null,

                "date_due": "0",

                "color_id": "yellow",

                "project_id": "2",

                "column_id": "5",

                "swimlane_id": "0",

                "owner_id": "0",

                "creator_id": "0"

                // ...

             }

        ]

    }



### [Оглавление](index.markdown)



-   [API Task Procedures](#)

    -   [createTask](#createtask)

    -   [getTask](#gettask)

    -   [getTaskByReference](#gettaskbyreference)

    -   [getAllTasks](#getalltasks)

    -   [getOverdueTasks](#getoverduetasks)

    -   [getOverdueTasksByProject](#getoverduetasksbyproject)

    -   [updateTask](#updatetask)

    -   [openTask](#opentask)

    -   [closeTask](#closetask)

    -   [removeTask](#removetask)

    -   [moveTaskPosition](#movetaskposition)

    -   [moveTaskToProject](#movetasktoproject)

    -   [duplicateTaskToProject](#duplicatetasktoproject)

    -   [searchTasks](#searchtasks)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/api-task-procedures.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/api-task-procedures.txt)

