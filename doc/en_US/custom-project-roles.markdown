Custom Project Roles
====================

You can create custom project roles to apply a set of specific restrictions on the people that belong to this role.
These custom roles are defined for each project.

A custom role inherits from the project member role. 
For example, you may want to create a custom role to force someone to follow a process.
You can have a group of people that are allowed to move tasks only from the column "Work in progress" to the column "Done".

Available restrictions
----------------------

- Project Restrictions:
    - Task creation is not permitted
    - Closing or opening a task is not permitted
    - Moving a task is not permitted
- Columns Restrictions:
    - Task creation is **allowed** only for a specific column
    - Task creation is **blocked** only for a specific column
    - Closing or opening a task is **allowed** only for a specific column
    - Closing or opening a task is **blocked** only for a specific column
- Moving tasks only between specified columns

Configuration
-------------

### 1) Create a new custom role

From the project settings, click on the left on the menu **Custom Roles** and at the top of the page click on **Add a new custom role**.
 
![New custom role](../screenshots/new_custom_role.png)

Give a name to the role and submit the form.

### 2) Add a restriction to the role

There are different kinds of restrictions:

- Project restrictions
- Drag and drop restrictions
- Column restrictions

You can click on the drop-down menu on the table to add a new restriction:

![Add a new restriction](../screenshots/add_new_restriction.png)

### 3) List of restrictions

![List of restrictions](../screenshots/example-restrictions.png)

For example, this role is able to create tasks only in the column "Backlog" and to move tasks between the column "Ready" and "Work in progress".

### 4) Assign the role to someone

Go to the "permissions" section on the left menu and assign the desired role to the user. 

![Custom project role](../screenshots/custom_roles.png)

Examples
--------

### Allow people to create tasks only in specific columns

![Example restriction task creation](../screenshots/example-restriction-task-creation.png)

- Users that belong to this role will be able to create new tasks only in the column "Backlog".
- The combination of the 2 rules is important, otherwise that will not work.

### Allow people to change the task status only in specific columns

![Example restriction task status](../screenshots/example-restriction-task-status.png)

- Users that belong to this role will be able to change the task status in the column "Backlog".
- Tasks with the status open are visible on the board and tasks with the status closed is hidden by default on the board.

### Do not allow people to change task status in a specific column

![Example column restriction](../screenshots/example-restriction-task-status-blocked.png)

Users that belong to this role won't be able to change the task status in the column "Done".
However, it will be possible in other columns.

### Allow people to move tasks only between specific columns

![Example restriction task drag and drop](../screenshots/example-restriction-task-drag-and-drop.png)

Users that belong to this role will be able to move tasks only between the column "Ready" and "Work in progress".
