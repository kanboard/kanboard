Project Permission API Procedures
=================================

getProjectUsers
---------------

-  Purpose: **Get all members of a project**
-  Parameters:

   -  **project\_id** (integer, required)

-  Result on success: **Dictionary of user\_id => user name**
-  Result on failure: **false**

Request example:

.. code:: json

    {
        "jsonrpc": "2.0",
        "method": "getProjectUsers",
        "id": 1601016721,
        "params": [
            "1"
        ]
    }

Response example:

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 1601016721,
        "result": {
            "1": "admin"
        }
    }

getAssignableUsers
------------------

-  Purpose: **Get users that can be assigned to a task for a project**
   (all members except viewers)
-  Parameters:

   -  **project\_id** (integer, required)
   -  **prepend\_unassigned** (boolean, optional, default is false)

-  Result on success: **Dictionary of user\_id => user name**
-  Result on failure: **false**

Request example:

.. code:: json

    {
        "jsonrpc": "2.0",
        "method": "getAssignableUsers",
        "id": 658294870,
        "params": [
            "1"
        ]
    }

Response example:

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 658294870,
        "result": {
            "1": "admin"
        }
    }

addProjectUser
--------------

-  Purpose: **Grant access to a project for a user**
-  Parameters:

   -  **project\_id** (integer, required)
   -  **user\_id** (integer, required)
   -  **role** (string, optional)

-  Result on success: **true**
-  Result on failure: **false**

Request example:

.. code:: json

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

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 1294688355,
        "result": true
    }

addProjectGroup
---------------

-  Purpose: **Grant access to a project for a group**
-  Parameters:

   -  **project\_id** (integer, required)
   -  **group\_id** (integer, required)
   -  **role** (string, optional)

-  Result on success: **true**
-  Result on failure: **false**

Request example:

.. code:: json

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

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 1694959089,
        "result": true
    }

removeProjectUser
-----------------

-  Purpose: **Revoke user access to a project**
-  Parameters:

   -  **project\_id** (integer, required)
   -  **user\_id** (integer, required)

-  Result on success: **true**
-  Result on failure: **false**

Request example:

.. code:: json

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

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 645233805,
        "result": true
    }

removeProjectGroup
------------------

-  Purpose: **Revoke group access to a project**
-  Parameters:

   -  **project\_id** (integer, required)
   -  **group\_id** (integer, required)

-  Result on success: **true**
-  Result on failure: **false**

Request example:

.. code:: json

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

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 557146966,
        "result": true
    }

changeProjectUserRole
---------------------

-  Purpose: **Change role of a user for a project**
-  Parameters:

   -  **project\_id** (integer, required)
   -  **user\_id** (integer, required)
   -  **role** (string, required)

-  Result on success: **true**
-  Result on failure: **false**

Request example:

.. code:: json

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

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 193473170,
        "result": true
    }

changeProjectGroupRole
----------------------

-  Purpose: **Change role of a group for a project**
-  Parameters:

   -  **project\_id** (integer, required)
   -  **group\_id** (integer, required)
   -  **role** (string, required)

-  Result on success: **true**
-  Result on failure: **false**

Request example:

.. code:: json

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

.. code:: json

    {
        "jsonrpc": "2.0",
        "id": 2114673298,
        "result": true
    }

