Group Member API Procedures[¶](#group-member-api-procedures "Ссылка на этот заголовок")

=======================================================================================



getMemberGroups[¶](#getmembergroups "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Get all groups for a given user**

-   Parameters:

    -   **user\_id** (integer, required)

-   Result on success: **List of groups**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getMemberGroups",

        "id": 1987176726,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1987176726,

        "result": [

            {

                "id": "1",

                "name": "My Group A"

            }

        ]

    }



getGroupMembers[¶](#getgroupmembers "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Purpose: **Get all members of a group**

-   Parameters:

    -   **group\_id** (integer, required)

-   Result on success: **List of users**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "getGroupMembers",

        "id": 1987176726,

        "params": [

            "1"

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1987176726,

        "result": [

            {

                "group_id": "1",

                "user_id": "1",

                "id": "1",

                "username": "admin",

                "is_ldap_user": "0",

                "name": null,

                "email": null,

                "notifications_enabled": "0",

                "timezone": null,

                "language": null,

                "disable_login_form": "0",

                "notifications_filter": "4",

                "nb_failed_login": "0",

                "lock_expiration_date": "0",

                "is_project_admin": "0",

                "gitlab_id": null,

                "role": "app-admin"

            }

        ]

    }



addGroupMember[¶](#addgroupmember "Ссылка на этот заголовок")

-------------------------------------------------------------



-   Purpose: **Add a user to a group**

-   Parameters:

    -   **group\_id** (integer, required)

    -   **user\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "addGroupMember",

        "id": 1589058273,

        "params": [

            1,

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1589058273,

        "result": true

    }



removeGroupMember[¶](#removegroupmember "Ссылка на этот заголовок")

-------------------------------------------------------------------



-   Purpose: **Remove a user from a group**

-   Parameters:

    -   **group\_id** (integer, required)

    -   **user\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "removeGroupMember",

        "id": 1730416406,

        "params": [

            1,

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1730416406,

        "result": true

    }



isGroupMember[¶](#isgroupmember "Ссылка на этот заголовок")

-----------------------------------------------------------



-   Purpose: **Check if a user is member of a group**

-   Parameters:

    -   **group\_id** (integer, required)

    -   **user\_id** (integer, required)

-   Result on success: **true**

-   Result on failure: **false**



Request example:



    {

        "jsonrpc": "2.0",

        "method": "isGroupMember",

        "id": 1052800865,

        "params": [

            1,

            1

        ]

    }



Response example:



    {

        "jsonrpc": "2.0",

        "id": 1052800865,

        "result": false

    }



### [Оглавление](index.markdown)



-   [Group Member API Procedures](#)

    -   [getMemberGroups](#getmembergroups)

    -   [getGroupMembers](#getgroupmembers)

    -   [addGroupMember](#addgroupmember)

    -   [removeGroupMember](#removegroupmember)

    -   [isGroupMember](#isgroupmember)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

