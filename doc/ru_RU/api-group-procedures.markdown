Group API Procedures[¶](#group-api-procedures "Ссылка на этот заголовок")

=========================================================================



createGroup[¶](#creategroup "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Create a new group**

-   Parameters:

    -   **name** (string, required)

    -   **external\_id** (string, optional)

-   Result on success: **link\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "createGroup",

        "id": 1416806551,

        "params": [

            "My Group B",

            "1234"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1416806551,

        "result": 2

    }



updateGroup[¶](#updategroup "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Update a group**

-   Parameters:

    -   **group\_id** (integer, required)

    -   **name** (string, optional)

    -   **external\_id** (string, optional)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "updateGroup",

        "id": 866078030,

        "params": {

            "group_id": "1",

            "name": "ABC",

            "external_id": "something"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 866078030,

        "result": true

    }



removeGroup[¶](#removegroup "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Remove a group**

-   Parameters:

    -   **group\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeGroup",

        "id": 566000661,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 566000661,

        "result": true

    }



getGroup[¶](#getgroup "Ссылка на этот заголовок")

-------------------------------------------------



-   Purpose: **Get one group**

-   Parameters:

    -   **group\_id** (integer, required)

-   Result on success: **Group dictionary**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getGroup",

        "id": 1968647622,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1968647622,

        "result": {

            "id": "1",

            "external_id": "",

            "name": "My Group A"

        }

    }



getAllGroups[¶](#getallgroups "Ссылка на этот заголовок")

---------------------------------------------------------



-   Purpose: **Get all groups**

-   Parameters: none

-   Result on success: **list of groups**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getAllGroups",

        "id": 546070742

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 546070742,

        "result": [

            {

                "id": "1",

                "external_id": "",

                "name": "My Group A"

            },

            {

                "id": "2",

                "external_id": "1234",

                "name": "My Group B"

            }

        ]

    }



### [Оглавление](index.markdown)



-   [Group API Procedures](#)

    -   [createGroup](#creategroup)

    -   [updateGroup](#updategroup)

    -   [removeGroup](#removegroup)

    -   [getGroup](#getgroup)

    -   [getAllGroups](#getallgroups)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

