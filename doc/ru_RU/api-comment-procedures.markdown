API Comment Procedures[¶](#api-comment-procedures "Ссылка на этот заголовок")

=============================================================================



createComment[¶](#createcomment "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Create a new comment**

-   Parameters:

    -   **task\_id** (integer, required)

    -   **user\_id** (integer, required)

    -   **content** Markdown content (string, required)

-   Result on success: **comment\_id**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "createComment",

        "id": 1580417921,

        "params": {

            "task_id": 1,

            "user_id": 1,

            "content": "Comment #1"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1580417921,

        "result": 11

    }



getComment[¶](#getcomment "Ссылка на этот заголовок")

-----------------------------------------------------



-   Purpose: **Get comment information**

-   Parameters:

    -   **comment\_id** (integer, required)

-   Result on success: **comment properties**

-   Result on failure: **null**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getComment",

        "id": 867839500,

        "params": {

            "comment_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 867839500,

        "result": {

            "id": "1",

            "task_id": "1",

            "user_id": "1",

            "date_creation": "1410881970",

            "comment": "Comment #1",

            "username": "admin",

            "name": null

        }

    }



getAllComments[¶](#getallcomments "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Get all available comments**

-   Parameters:

    -   **task\_id** (integer, required)

-   Result on success: **List of comments**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getAllComments",

        "id": 148484683,

        "params": {

            "task_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 148484683,

        "result": [

            {

                "id": "1",

                "date_creation": "1410882272",

                "task_id": "1",

                "user_id": "1",

                "comment": "Comment #1",

                "username": "admin",

                "name": null

            },

            ...

        ]

    }



updateComment[¶](#updatecomment "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Update a comment**

-   Parameters:

    -   **id** (integer, required)

    -   **content** Markdown content (string, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "updateComment",

        "id": 496470023,

        "params": {

            "id": 1,

            "content": "Comment #1 updated"

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1493368950,

        "result": true

    }



removeComment[¶](#removecomment "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Remove a comment**

-   Parameters:

    -   **comment\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeComment",

        "id": 328836871,

        "params": {

            "comment_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 328836871,

        "result": true

    }



### [Оглавление](index.markdown)



-   [API Comment Procedures](#)

    -   [createComment](#createcomment)

    -   [getComment](#getcomment)

    -   [getAllComments](#getallcomments)

    -   [updateComment](#updatecomment)

    -   [removeComment](#removecomment)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/api-comment-procedures.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/api-comment-procedures.txt)

