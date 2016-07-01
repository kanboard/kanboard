Project Permission API Procedures[¶](#project-permission-api-procedures "Ссылка на этот заголовок")

===================================================================================================



getProjectUsers[¶](#getprojectusers "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Get all members of a project**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **Dictionary of user\_id =\> user name**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getProjectUsers",

        "id": 1601016721,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1601016721,

        "result": {

            "1": "admin"

        }

    }



getAssignableUsers[¶](#getassignableusers "Ссылка на этот заголовок")

---------------------------------------------------------------------



-   Purpose: **Get users that can be assigned to a task for a project** (all members except viewers)

-   Parameters:

    -   **project\_id** (integer, required)

    -   **prepend\_unassigned** (boolean, optional, default is false)

-   Result on success: **Dictionary of user\_id =\> user name**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getAssignableUsers",

        "id": 658294870,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 658294870,

        "result": {

            "1": "admin"

        }

    }



addProjectUser[¶](#addprojectuser "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Grant access to a project for a user**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **user\_id** (integer, required)

    -   **role** (string, optional)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "addProjectUser",

        "id": 1294688355,

        "params": [

            "1",

            "1",

            "project-viewer"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1294688355,

        "result": true

    }



addProjectGroup[¶](#addprojectgroup "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Grant access to a project for a group**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **group\_id** (integer, required)

    -   **role** (string, optional)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "addProjectGroup",

        "id": 1694959089,

        "params": [

            "1",

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1694959089,

        "result": true

    }



removeProjectUser[¶](#removeprojectuser "Ссылка на этот заголовок")

-------------------------------------------------------------------



-   Purpose: **Revoke user access to a project**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **user\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeProjectUser",

        "id": 645233805,

        "params": [

            1,

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 645233805,

        "result": true

    }



removeProjectGroup[¶](#removeprojectgroup "Ссылка на этот заголовок")

---------------------------------------------------------------------



-   Purpose: **Revoke group access to a project**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **group\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeProjectGroup",

        "id": 557146966,

        "params": [

            1,

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 557146966,

        "result": true

    }



changeProjectUserRole[¶](#changeprojectuserrole "Ссылка на этот заголовок")

---------------------------------------------------------------------------



-   Purpose: **Change role of a user for a project**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **user\_id** (integer, required)

    -   **role** (string, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "changeProjectUserRole",

        "id": 193473170,

        "params": [

            "1",

            "1",

            "project-viewer"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 193473170,

        "result": true

    }



changeProjectGroupRole[¶](#changeprojectgrouprole "Ссылка на этот заголовок")

-----------------------------------------------------------------------------



-   Purpose: **Change role of a group for a project**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **group\_id** (integer, required)

    -   **role** (string, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "changeProjectGroupRole",

        "id": 2114673298,

        "params": [

            "1",

            "1",

            "project-viewer"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 2114673298,

        "result": true

    }



### [Оглавление](index.markdown)



-   [Project Permission API Procedures](#)

    -   [getProjectUsers](#getprojectusers)

    -   [getAssignableUsers](#getassignableusers)

    -   [addProjectUser](#addprojectuser)

    -   [addProjectGroup](#addprojectgroup)

    -   [removeProjectUser](#removeprojectuser)

    -   [removeProjectGroup](#removeprojectgroup)

    -   [changeProjectUserRole](#changeprojectuserrole)

    -   [changeProjectGroupRole](#changeprojectgrouprole)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/api-project-permission-procedures.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/api-project-permission-procedures.txt)

