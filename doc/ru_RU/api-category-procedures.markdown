API Category Procedures[¶](#api-category-procedures "Ссылка на этот заголовок")

===============================================================================



createCategory[¶](#createcategory "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Create a new category**

-   Parameters:

-   **project\_id** (integer, required)

    -   **name** (string, required, must be unique for the given project)

-   Result on success: **category\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "createCategory",

        "id": 541909890,

        "params": {

            "name": "Super category",

            "project_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 541909890,

        "result": 4

    }



getCategory[¶](#getcategory "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Get category information**

-   Parameters:

    -   **category\_id** (integer, required)

-   Result on success: **category properties**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getCategory",

        "id": 203539163,

        "params": {

            "category_id": 1

        }

    }



Response example:



    {



        "jsonrpc": "2.0",

        "id": 203539163,

        "result": {

            "id": "1",

            "name": "Super category",

            "project_id": "1"

        }

    }



getAllCategories[¶](#getallcategories "Ссылка на этот заголовок")

-----------------------------------------------------------------



-   Purpose: **Get all available categories**

-   Parameters:

    -   **project\_id** (integer, required)

-   Result on success: **List of categories**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getAllCategories",

        "id": 1261777968,

        "params": {

            "project_id": 1

        }

    }



Response example:



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



updateCategory[¶](#updatecategory "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Update a category**

-   Parameters:

    -   **id** (integer, required)

    -   **name** (string, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "updateCategory",

        "id": 570195391,

        "params": {

            "id": 1,

            "name": "Renamed category"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 570195391,

        "result": true

    }



removeCategory[¶](#removecategory "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Remove a category**

-   Parameters:

    -   **category\_id** (integer)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeCategory",

        "id": 88225706,

        "params": {

            "category_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 88225706,

        "result": true

    }



### [Оглавление](index.markdown)



-   [API Category Procedures](#)

    -   [createCategory](#createcategory)

    -   [getCategory](#getcategory)

    -   [getAllCategories](#getallcategories)

    -   [updateCategory](#updatecategory)

    -   [removeCategory](#removecategory)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

