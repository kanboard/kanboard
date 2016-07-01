Group API Procedures
====================

createGroup
-----------

-  Purpose: **Create a new group**
-  Parameters:

   -  **name** (string, required)
   -  **external\_id** (string, optional)

-  Result on success: **link\_id**
-  Result on failure: **false**

Request example:

.. code:: json

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

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 1416806551,
        "result": 2
    }

updateGroup
-----------

-  Purpose: **Update a group**
-  Parameters:

   -  **group\_id** (integer, required)
   -  **name** (string, optional)
   -  **external\_id** (string, optional)

-  Result on success: **true**
-  Result on failure: **false**

Request example:

.. code:: json

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

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 866078030,
        "result": true
    }

removeGroup
-----------

-  Purpose: **Remove a group**
-  Parameters:

   -  **group\_id** (integer, required)

-  Result on success: **true**
-  Result on failure: **false**

Request example:

.. code:: json

    {
        "jsonrpc": "2.0",
        "method": "removeGroup",
        "id": 566000661,
        "params": [
            "1"
        ]
    }

Response example:

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 566000661,
        "result": true
    }

getGroup
--------

-  Purpose: **Get one group**
-  Parameters:

   -  **group\_id** (integer, required)

-  Result on success: **Group dictionary**
-  Result on failure: **false**

Request example:

.. code:: json

    {
        "jsonrpc": "2.0",
        "method": "getGroup",
        "id": 1968647622,
        "params": [
            "1"
        ]
    }

Response example:

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 1968647622,
        "result": {
            "id": "1",
            "external_id": "",
            "name": "My Group A"
        }
    }

getAllGroups
------------

-  Purpose: **Get all groups**
-  Parameters: none
-  Result on success: **list of groups**
-  Result on failure: **false**

Request example:

.. code:: json

    {
        "jsonrpc": "2.0",
        "method": "getAllGroups",
        "id": 546070742
    }

Response example:

.. code:: json

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

