User API Specific Procedures[¶](#user-api-specific-procedures "Ссылка на этот заголовок")

=========================================================================================



getMe[¶](#getme "Ссылка на этот заголовок")

-------------------------------------------



-   Purpose: **Get logged user session**

-   Parameters: None

-   Result on success: **user session data**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getMe",

        "id": 1718627783

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1718627783,

        "result": {

            "id": 2,

            "username": "user",

            "role": "app-user",

            "is_ldap_user": false,

            "name": "",

            "email": "",

            "google_id": null,

            "github_id": null,

            "notifications_enabled": "0",

            "timezone": null,

            "language": null,

            "disable_login_form": "0",

            "twofactor_activated": false,

            "twofactor_secret": null,

            "token": "",

            "notifications_filter": "4"

        }

    }



getMyDashboard[¶](#getmydashboard "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Get the dashboard of the logged user without pagination**

-   Parameters: None

-   Result on success: **Dashboard information**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getMyDashboard",

        "id": 447898718

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1563664593,

        "result": {

            "projects": [

                {

                    "id": "2",

                    "name": "my project",

                    "is_active": "1",

                    "token": "",

                    "last_modified": "1438205337",

                    "is_public": "0",

                    "is_private": "1",

                    "is_everybody_allowed": "0",

                    "default_swimlane": "Default swimlane",

                    "show_default_swimlane": "1",

                    "description": null,

                    "identifier": "",

                    "columns": [

                        {

                            "id": "5",

                            "title": "Backlog",

                            "position": "1",

                            "project_id": "2",

                            "task_limit": "0",

                            "description": "",

                            "nb_tasks": 0

                        },

                        {

                            "id": "6",

                            "title": "Ready",

                            "position": "2",

                            "project_id": "2",

                            "task_limit": "0",

                            "description": "",

                            "nb_tasks": 0

                        },

                        {

                            "id": "7",

                            "title": "Work in progress",

                            "position": "3",

                            "project_id": "2",

                            "task_limit": "0",

                            "description": "",

                            "nb_tasks": 0

                        },

                        {

                            "id": "8",

                            "title": "Done",

                            "position": "4",

                            "project_id": "2",

                            "task_limit": "0",

                            "description": "",

                            "nb_tasks": 0

                        }

                    ],

                    "url": {

                        "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=2",

                        "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=2",

                        "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=2"

                    }

                }

            ],

            "tasks": [

                {

                    "id": "1",

                    "title": "new title",

                    "date_due": "0",

                    "date_creation": "1438205336",

                    "project_id": "2",

                    "color_id": "yellow",

                    "time_spent": "0",

                    "time_estimated": "0",

                    "project_name": "my project",

                    "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=1&project_id=2"

                }

            ],

            "subtasks": []

        }

    }



getMyActivityStream[¶](#getmyactivitystream "Ссылка на этот заголовок")

-----------------------------------------------------------------------



-   Purpose: **Get the last 100 events for the logged user**

-   Parameters: None

-   Result on success: **List of events**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getMyActivityStream",

        "id": 1132562181

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1132562181,

        "result": [

            {

                "id": "1",

                "date_creation": "1438205054",

                "event_name": "task.create",

                "creator_id": "2",

                "project_id": "2",

                "task_id": "1",

                "author_username": "user",

                "author_name": "",

                "email": "",

                "task": {

                    "id": "1",

                    "reference": "",

                    "title": "my user title",

                    "description": "",

                    "date_creation": "1438205054",

                    "date_completed": null,

                    "date_modification": "1438205054",

                    "date_due": "0",

                    "date_started": null,

                    "time_estimated": "0",

                    "time_spent": "0",

                    "color_id": "yellow",

                    "project_id": "2",

                    "column_id": "5",

                    "owner_id": "0",

                    "creator_id": "2",

                    "position": "1",

                    "is_active": "1",

                    "score": "0",

                    "category_id": "0",

                    "swimlane_id": "0",

                    "date_moved": "1438205054",

                    "recurrence_status": "0",

                    "recurrence_trigger": "0",

                    "recurrence_factor": "0",

                    "recurrence_timeframe": "0",

                    "recurrence_basedate": "0",

                    "recurrence_parent": null,

                    "recurrence_child": null,

                    "category_name": null,

                    "swimlane_name": null,

                    "project_name": "my project",

                    "default_swimlane": "Default swimlane",

                    "column_title": "Backlog",

                    "assignee_username": null,

                    "assignee_name": null,

                    "creator_username": "user",

                    "creator_name": ""

                },

                "changes": [],

                "author": "user",

                "event_title": "user created the task #1",

                "event_content": "\n<p class=\"activity-title\">\n    user created the task <a href=\"\/?controller=task&amp;action=show&amp;task_id=1&amp;project_id=2\" class=\"\" title=\"\" >#1<\/a><\/p>\n<p class=\"activity-description\">\n    <em>my user title<\/em>\n<\/p>"

            }

        ]

    }



createMyPrivateProject[¶](#createmyprivateproject "Ссылка на этот заголовок")

-----------------------------------------------------------------------------



-   Purpose: **Create a private project for the logged user**

-   Parameters:

    -   **name** (string, required)

    -   **description** (string, optional)

-   Result on success: **project\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "createMyPrivateProject",

        "id": 1271580569,

        "params": [

            "my project"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1271580569,

        "result": 2

    }



getMyProjectsList[¶](#getmyprojectslist "Ссылка на этот заголовок")

-------------------------------------------------------------------



-   Purpose: **Get projects of the connected user**

-   Parameters: None

-   Result on success: **dictionary of project\_id =\> project\_name**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getMyProjectsList",

        "id": 987834805

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 987834805,

        "result": {

            "2": "my project"

        }

    }



getMyOverdueTasks[¶](#getmyoverduetasks "Ссылка на этот заголовок")

-------------------------------------------------------------------



-   Purpose: **Get my overdue tasks**

-   Result on success: **List of tasks**

-   Result on failure: **false**



Request example to fetch all tasks on the board:



    {

        "jsonrpc": "2.0",

        "method": "getMyOverdueTasks",

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



getMyProjects[¶](#getmyprojects "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Get projects of connected user with full details**

-   Parameters:

    -   **none**

-   Result on success: **List of projects with details**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getmyProjects",

        "id": 2134420212

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 2134420212,

        "result": [

            {

                "id": "1",

                "name": "API test",

                "is_active": "1",

                "token": "",

                "last_modified": "1436119570",

                "is_public": "0",

                "is_private": "0",

                "is_everybody_allowed": "0",

                "default_swimlane": "Default swimlane",

                "show_default_swimlane": "1",

                "description": null,

                "identifier": "",

                "url": {

                    "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",

                    "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",

                    "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"

                }

            }

        ]

    }



### [Оглавление](index.markdown)



-   [User API Specific Procedures](#)

    -   [getMe](#getme)

    -   [getMyDashboard](#getmydashboard)

    -   [getMyActivityStream](#getmyactivitystream)

    -   [createMyPrivateProject](#createmyprivateproject)

    -   [getMyProjectsList](#getmyprojectslist)

    -   [getMyOverdueTasks](#getmyoverduetasks)

    -   [getMyProjects](#getmyprojects)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/api-me-procedures.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/api-me-procedures.txt)

