API Link Procedures[¶](#api-link-procedures "Ссылка на этот заголовок")

=======================================================================



getAllLinks[¶](#getalllinks "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Get the list of possible relations between tasks**

-   Parameters: none

-   Result on success: **List of links**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getAllLinks",

        "id": 113057196

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 113057196,

        "result": [

            {

                "id": "1",

                "label": "relates to",

                "opposite_id": "0"

            },

            {

                "id": "2",

                "label": "blocks",

                "opposite_id": "3"

            },

            {

                "id": "3",

                "label": "is blocked by",

                "opposite_id": "2"

            },

            {

                "id": "4",

                "label": "duplicates",

                "opposite_id": "5"

            },

            {

                "id": "5",

                "label": "is duplicated by",

                "opposite_id": "4"

            },

            {

                "id": "6",

                "label": "is a child of",

                "opposite_id": "7"

            },

            {

                "id": "7",

                "label": "is a parent of",

                "opposite_id": "6"

            },

            {

                "id": "8",

                "label": "targets milestone",

                "opposite_id": "9"

            },

            {

                "id": "9",

                "label": "is a milestone of",

                "opposite_id": "8"

            },

            {

                "id": "10",

                "label": "fixes",

                "opposite_id": "11"

            },

            {

                "id": "11",

                "label": "is fixed by",

                "opposite_id": "10"

            }

        ]

    }



getOppositeLinkId[¶](#getoppositelinkid "Ссылка на этот заголовок")

-------------------------------------------------------------------



-   Purpose: **Get the opposite link id of a task link**

-   Parameters:

    -   **link\_id** (integer, required)

-   Result on success: **link\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getOppositeLinkId",

        "id": 407062448,

        "params": [

            2

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 407062448,

        "result": "3"

    }



getLinkByLabel[¶](#getlinkbylabel "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Get a link by label**

-   Parameters:

    -   **label** (integer, required)

-   Result on success: **link properties**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getLinkByLabel",

        "id": 1796123316,

        "params": [

            "blocks"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1796123316,

        "result": {

            "id": "2",

            "label": "blocks",

            "opposite_id": "3"

        }

    }



getLinkById[¶](#getlinkbyid "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Get a link by id**

-   Parameters:

    -   **link\_id** (integer, required)

-   Result on success: **link properties**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getLinkById",

        "id": 1190238402,

        "params": [

            4

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1190238402,

        "result": {

            "id": "4",

            "label": "duplicates",

            "opposite_id": "5"

        }

    }



createLink[¶](#createlink "Ссылка на этот заголовок")

-----------------------------------------------------



-   Purpose: **Create a new task relation**

-   Parameters:

    -   **label** (integer, required)

    -   **opposite\_label** (integer, optional)

-   Result on success: **link\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "createLink",

        "id": 1040237496,

        "params": [

            "foo",

            "bar"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1040237496,

        "result": 13

    }



updateLink[¶](#updatelink "Ссылка на этот заголовок")

-----------------------------------------------------



-   Purpose: **Update a link**

-   Parameters:

    -   **link\_id** (integer, required)

    -   **opposite\_link\_id** (integer, required)

    -   **label** (string, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "updateLink",

        "id": 2110446926,

        "params": [

            "14",

            "12",

            "boo"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 2110446926,

        "result": true

    }



removeLink[¶](#removelink "Ссылка на этот заголовок")

-----------------------------------------------------



-   Purpose: **Remove a link**

-   Parameters:

    -   **link\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeLink",

        "id": 2136522739,

        "params": [

            "14"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 2136522739,

        "result": true

    }



createTaskLink[¶](#createtasklink "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Create a link between two tasks**

-   Parameters:

    -   **task\_id** (integer, required)

    -   **opposite\_task\_id** (integer, required)

    -   **link\_id** (integer, required)

-   Result on success: **task\_link\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "createTaskLink",

        "id": 509742912,

        "params": [

            2,

            3,

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 509742912,

        "result": 1

    }



updateTaskLink[¶](#updatetasklink "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Update task link**

-   Parameters:

    -   **task\_link\_id** (integer, required)

    -   **task\_id** (integer, required)

    -   **opposite\_task\_id** (integer, required)

    -   **link\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "updateTaskLink",

        "id": 669037109,

        "params": [

            1,

            2,

            4,

            2

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 669037109,

        "result": true

    }



getTaskLinkById[¶](#gettasklinkbyid "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Get a task link**

-   Parameters:

    -   **task\_link\_id** (integer, required)

-   Result on success: **task link properties**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getTaskLinkById",

        "id": 809885202,

        "params": [

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 809885202,

        "result": {

            "id": "1",

            "link_id": "1",

            "task_id": "2",

            "opposite_task_id": "3"

        }

    }



getAllTaskLinks[¶](#getalltasklinks "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Get all links related to a task**

-   Parameters:

    -   **task\_id** (integer, required)

-   Result on success: **list of task link**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getAllTaskLinks",

        "id": 810848359,

        "params": [

            2

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 810848359,

        "result": [

            {

                "id": "1",

                "task_id": "3",

                "label": "relates to",

                "title": "B",

                "is_active": "1",

                "project_id": "1",

                "task_time_spent": "0",

                "task_time_estimated": "0",

                "task_assignee_id": "0",

                "task_assignee_username": null,

                "task_assignee_name": null,

                "column_title": "Backlog"

            }

        ]

    }



removeTaskLink[¶](#removetasklink "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Remove a link between two tasks**

-   Parameters:

    -   **task\_link\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeTaskLink",

        "id": 473028226,

        "params": [

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 473028226,

        "result": true

    }



### [Оглавление](index.markdown)



-   [API Link Procedures](#)

    -   [getAllLinks](#getalllinks)

    -   [getOppositeLinkId](#getoppositelinkid)

    -   [getLinkByLabel](#getlinkbylabel)

    -   [getLinkById](#getlinkbyid)

    -   [createLink](#createlink)

    -   [updateLink](#updatelink)

    -   [removeLink](#removelink)

    -   [createTaskLink](#createtasklink)

    -   [updateTaskLink](#updatetasklink)

    -   [getTaskLinkById](#gettasklinkbyid)

    -   [getAllTaskLinks](#getalltasklinks)

    -   [removeTaskLink](#removetasklink)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/api-link-procedures.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/api-link-procedures.txt)

