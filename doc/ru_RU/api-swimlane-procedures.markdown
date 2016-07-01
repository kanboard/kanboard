API Swimlane Procedures[¶](#api-swimlane-procedures "Ссылка на этот заголовок")

===============================================================================



getDefaultSwimlane[¶](#getdefaultswimlane "Ссылка на этот заголовок")

---------------------------------------------------------------------



-   Purpose: **Get the default swimlane for a project**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getDefaultSwimlane",

        "id": 898774713,

        "params": [

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 898774713,

        "result": {

            "id": "1",

            "default_swimlane": "Default swimlane",

            "show_default_swimlane": "1"

        }

    }



getActiveSwimlanes[¶](#getactiveswimlanes "Ссылка на этот заголовок")

---------------------------------------------------------------------



-   Purpose: **Get the list of enabled swimlanes of a project (include default swimlane if enabled)**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **List of swimlanes**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getActiveSwimlanes",

        "id": 934789422,

        "params": [

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 934789422,

        "result": [

            {

                "id": 0,

                "name": "Default swimlane"

            },

            {

                "id": "2",

                "name": "Swimlane A"

            }

        ]

    }



getAllSwimlanes[¶](#getallswimlanes "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Get the list of all swimlanes of a project (enabled or disabled) and sorted by position**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **List of swimlanes**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getAllSwimlanes",

        "id": 509791576,

        "params": [

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 509791576,

        "result": [

            {

                "id": "1",

                "name": "Another swimlane",

                "position": "1",

                "is_active": "1",

                "project_id": "1"

            },

            {

                "id": "2",

                "name": "Swimlane A",

                "position": "2",

                "is_active": "1",

                "project_id": "1"

            }

        ]

    }



getSwimlane[¶](#getswimlane "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Get the a swimlane by id**

-   Parameters:

    -   **swimlane\_id** (integer, required)

-   Result on success: **swimlane properties**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getSwimlane",

        "id": 131071870,

        "params": [

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 131071870,

        "result": {

            "id": "1",

            "name": "Swimlane 1",

            "position": "1",

            "is_active": "1",

            "project_id": "1"

        }

    }



getSwimlaneById[¶](#getswimlanebyid "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Get the a swimlane by id**

-   Parameters:

    -   **swimlane\_id** (integer, required)

-   Result on success: **swimlane properties**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getSwimlaneById",

        "id": 131071870,

        "params": [

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 131071870,

        "result": {

            "id": "1",

            "name": "Swimlane 1",

            "position": "1",

            "is_active": "1",

            "project_id": "1"

        }

    }



getSwimlaneByName[¶](#getswimlanebyname "Ссылка на этот заголовок")

-------------------------------------------------------------------



-   Purpose: **Get the a swimlane by name**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **name** (string, required)

-   Result on success: **swimlane properties**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getSwimlaneByName",

        "id": 824623567,

        "params": [

            1,

            "Swimlane 1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 824623567,

        "result": {

            "id": "1",

            "name": "Swimlane 1",

            "position": "1",

            "is_active": "1",

            "project_id": "1"

        }

    }



changeSwimlanePosition[¶](#changeswimlaneposition "Ссылка на этот заголовок")

-----------------------------------------------------------------------------



-   Purpose: **Move up the swimlane position** (only for active swimlanes)

-   Parameters:

    -   **project\_id** (integer, required)

    -   **swimlane\_id** (integer, required)

    -   **position** (integer, required, must be \>= 1)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "changeSwimlanePosition",

        "id": 99275573,

        "params": [

            1,

            2,

            3

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 99275573,

        "result": true

    }



updateSwimlane[¶](#updateswimlane "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Update swimlane properties**

-   Parameters:

    -   **swimlane\_id** (integer, required)

    -   **name** (string, required)

    -   **description** (string, optional)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "updateSwimlane",

        "id": 87102426,

        "params": [

            "1",

            "Another swimlane"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 87102426,

        "result": true

    }



addSwimlane[¶](#addswimlane "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Add a new swimlane**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **name** (string, required)

    -   **description** (string, optional)

-   Result on success: **swimlane\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "addSwimlane",

        "id": 849940086,

        "params": [

            1,

            "Swimlane 1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 849940086,

        "result": 1

    }



removeSwimlane[¶](#removeswimlane "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Remove a swimlane**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **swimlane\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeSwimlane",

        "id": 1433237746,

        "params": [

            2,

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1433237746,

        "result": true

    }



disableSwimlane[¶](#disableswimlane "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Enable a swimlane**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **swimlane\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "disableSwimlane",

        "id": 1433237746,

        "params": [

            2,

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1433237746,

        "result": true

    }



enableSwimlane[¶](#enableswimlane "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Enable a swimlane**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **swimlane\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "enableSwimlane",

        "id": 1433237746,

        "params": [

            2,

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1433237746,

        "result": true

    }



### [Оглавление](index.markdown)



-   [API Swimlane Procedures](#)

    -   [getDefaultSwimlane](#getdefaultswimlane)

    -   [getActiveSwimlanes](#getactiveswimlanes)

    -   [getAllSwimlanes](#getallswimlanes)

    -   [getSwimlane](#getswimlane)

    -   [getSwimlaneById](#getswimlanebyid)

    -   [getSwimlaneByName](#getswimlanebyname)

    -   [changeSwimlanePosition](#changeswimlaneposition)

    -   [updateSwimlane](#updateswimlane)

    -   [addSwimlane](#addswimlane)

    -   [removeSwimlane](#removeswimlane)

    -   [disableSwimlane](#disableswimlane)

    -   [enableSwimlane](#enableswimlane)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/api-swimlane-procedures.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/api-swimlane-procedures.txt)

