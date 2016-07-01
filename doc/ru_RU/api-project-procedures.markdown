API Project Procedures[¶](#api-project-procedures "Ссылка на этот заголовок")

=============================================================================



createProject[¶](#createproject "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Create a new project**

-   Parameters:

    -   **name** (string, required)

    -   **description** (string, optional)

-   Result on success: **project\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "createProject",

        "id": 1797076613,

        "params": {

            "name": "PHP client"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1797076613,

        "result": 2

    }



getProjectById[¶](#getprojectbyid "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Get project information**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **project properties**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getProjectById",

        "id": 226760253,

        "params": {

            "project_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 226760253,

        "result": {

            "id": "1",

            "name": "API test",

            "is_active": "1",

            "token": "",

            "last_modified": "1436119135",

            "is_public": "0",

            "is_private": "0",

            "is_everybody_allowed": "0",

            "default_swimlane": "Default swimlane",

            "show_default_swimlane": "1",

            "description": "test",

            "identifier": "",

            "url": {

                "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",

                "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",

                "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"

            }

        }

    }



getProjectByName[¶](#getprojectbyname "Ссылка на этот заголовок")

-----------------------------------------------------------------



-   Purpose: **Get project information**

-   Parameters:

    -   **name** (string, required)

-   Result on success: **project properties**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getProjectByName",

        "id": 1620253806,

        "params": {

            "name": "Test"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1620253806,

        "result": {

            "id": "1",

            "name": "Test",

            "is_active": "1",

            "token": "",

            "last_modified": "1436119135",

            "is_public": "0",

            "is_private": "0",

            "is_everybody_allowed": "0",

            "default_swimlane": "Default swimlane",

            "show_default_swimlane": "1",

            "description": "test",

            "identifier": "",

            "url": {

                "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",

                "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",

                "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"

            }

        }

    }



getAllProjects[¶](#getallprojects "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Get all available projects**

-   Parameters:

    -   **none**

-   Result on success: **List of projects**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getAllProjects",

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



updateProject[¶](#updateproject "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Update a project**

-   Parameters:

    -   **id** (integer, required)

    -   **name** (string, required)

    -   **description** (string, optional)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "updateProject",

        "id": 1853996288,

        "params": {

            "id": 1,

            "name": "PHP client update"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1853996288,

        "result": true

    }



removeProject[¶](#removeproject "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Remove a project**

-   Parameters: **project\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeProject",

        "id": 46285125,

        "params": {

            "project_id": "2"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 46285125,

        "result": true

    }



enableProject[¶](#enableproject "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Enable a project**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "enableProject",

        "id": 1775494839,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1775494839,

        "result": true

    }



disableProject[¶](#disableproject "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Disable a project**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "disableProject",

        "id": 1734202312,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1734202312,

        "result": true

    }



enableProjectPublicAccess[¶](#enableprojectpublicaccess "Ссылка на этот заголовок")

-----------------------------------------------------------------------------------



-   Purpose: **Enable public access for a given project**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "enableProjectPublicAccess",

        "id": 103792571,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 103792571,

        "result": true

    }



disableProjectPublicAccess[¶](#disableprojectpublicaccess "Ссылка на этот заголовок")

-------------------------------------------------------------------------------------



-   Purpose: **Disable public access for a given project**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "disableProjectPublicAccess",

        "id": 942472945,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 942472945,

        "result": true

    }



getProjectActivity[¶](#getprojectactivity "Ссылка на этот заголовок")

---------------------------------------------------------------------



-   Purpose: **Get activity stream for a project**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **List of events**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getProjectActivity",

        "id": 942472945,

        "params": [

            "project_id": 1

        ]

    }



getProjectActivities[¶](#getprojectactivities "Ссылка на этот заголовок")

-------------------------------------------------------------------------



-   Purpose: **Get Activityfeed for Project(s)**

-   Parameters:

    -   **project\_ids** (integer array, required)

-   Result on success: **List of events**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getProjectActivities",

        "id": 942472945,

        "params": [

            "project_ids": [1,2]

        ]

    }



### [Оглавление](index.markdown)



-   [API Project Procedures](#)

    -   [createProject](#createproject)

    -   [getProjectById](#getprojectbyid)

    -   [getProjectByName](#getprojectbyname)

    -   [getAllProjects](#getallprojects)

    -   [updateProject](#updateproject)

    -   [removeProject](#removeproject)

    -   [enableProject](#enableproject)

    -   [disableProject](#disableproject)

    -   [enableProjectPublicAccess](#enableprojectpublicaccess)

    -   [disableProjectPublicAccess](#disableprojectpublicaccess)

    -   [getProjectActivity](#getprojectactivity)

    -   [getProjectActivities](#getprojectactivities)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/api-project-procedures.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/api-project-procedures.txt)

