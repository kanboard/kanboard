API File Procedures[¶](#api-file-procedures "Ссылка на этот заголовок")

=======================================================================



createTaskFile[¶](#createtaskfile "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Create and upload a new task attachment**

-   Parameters:

    -   **project\_id** (integer, required)

    -   **task\_id** (integer, required)

    -   **filename** (integer, required)

    -   **blob** File content encoded in base64 (string, required)

-   Result on success: **file\_id**

-   Result on failure: **false**

-   Note: **The maximum file size depends of your PHP configuration, this method should not be used to upload large files**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "createTaskFile",

        "id": 94500810,

        "params": [

            1,

            1,

            "My file",

            "cGxhaW4gdGV4dCBmaWxl"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 94500810,

        "result": 1

    }



getAllTaskFiles[¶](#getalltaskfiles "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Get all files attached to task**

-   Parameters:

    -   **task\_id** (integer, required)

-   Result on success: **list of files**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getAllTaskFiles",

        "id": 1880662820,

        "params": {

            "task_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1880662820,

        "result": [

            {

                "id": "1",

                "name": "My file",

                "path": "1\/1\/0db4d0a897a4c852f6e12f0239d4805f7b4ab596",

                "is_image": "0",

                "task_id": "1",

                "date": "1432509941",

                "user_id": "0",

                "size": "15",

                "username": null,

                "user_name": null

            }

        ]

    }



getTaskFile[¶](#gettaskfile "Ссылка на этот заголовок")

-------------------------------------------------------



-   Purpose: **Get file information**

-   Parameters:

    -   **file\_id** (integer, required)

-   Result on success: **file properties**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getTaskFile",

        "id": 318676852,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 318676852,

        "result": {

            "id": "1",

            "name": "My file",

            "path": "1\/1\/0db4d0a897a4c852f6e12f0239d4805f7b4ab596",

            "is_image": "0",

            "task_id": "1",

            "date": "1432509941",

            "user_id": "0",

            "size": "15"

        }

    }



downloadTaskFile[¶](#downloadtaskfile "Ссылка на этот заголовок")

-----------------------------------------------------------------



-   Purpose: **Download file contents (encoded in base64)**

-   Parameters:

    -   **file\_id** (integer, required)

-   Result on success: **base64 encoded string**

-   Result on failure: **empty string**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "downloadTaskFile",

        "id": 235943344,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 235943344,

        "result": "cGxhaW4gdGV4dCBmaWxl"

    }



removeTaskFile[¶](#removetaskfile "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Remove file**

-   Parameters:

    -   **file\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeTaskFile",

        "id": 447036524,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 447036524,

        "result": true

    }



removeAllTaskFiles[¶](#removealltaskfiles "Ссылка на этот заголовок")

---------------------------------------------------------------------



-   Purpose: **Remove all files associated to a task**

-   Parameters:

    -   **task\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeAllTaskFiles",

        "id": 593312993,

        "params": {

            "task_id": 1

        }

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 593312993,

        "result": true

    }



### [Оглавление](index.markdown)



-   [API File Procedures](#)

    -   [createTaskFile](#createtaskfile)

    -   [getAllTaskFiles](#getalltaskfiles)

    -   [getTaskFile](#gettaskfile)

    -   [downloadTaskFile](#downloadtaskfile)

    -   [removeTaskFile](#removetaskfile)

    -   [removeAllTaskFiles](#removealltaskfiles)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/api-file-procedures.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/api-file-procedures.txt)

