Custom Project Roles
====================

You can create custom project roles to apply a set of specific restrictions on the people that belong to this role.
These custom roles are defined for each project.

A custom role inherits from the project member role. 
For example, you may want to create a custom role to force someone to follow a process.
You can have a group of people that are allowed to move tasks only from the column "Work in progress" to the column "Done".

Available restrictions
----------------------

- Task creation is not permitted
- Moving tasks only from a given column to another one

Configuration
-------------

### 1) Create a new custom role

From the project settings, click on the left on the menu **Custom Roles** and at the top of the page click on **Add a new custom role**.
 
![New custom role](screenshots/new_custom_role.png)

Give a name to the role and submit the form.

### 2) Add a restriction to the role

Each role is grouped in a table.
You can click on the dropdown menu in the table to add a new restriction.

![New column restriction](screenshots/new_restriction.png)

If you choose a column restriction, you just need to select the source and the destination column.

### 3) List of restrictions

![List of restrictions](screenshots/restrictions.png)

### 4) Assign the role to someone

Go to the "permissions" section on the left menu and assign the desired role to the user. 

![Custom project role](screenshots/custom_roles.png)
