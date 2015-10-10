User management
===============

Roles at the application level
------------------------------

Kanboard use a basic permission system, there are 3 type of users:

### Administrators

- Access to everything

### Project Administrators

- Can create multi-users and private projects
- Can convert multi-users and private projects
- Can see only their own projects
- Cannot change application settings
- Cannot manage users

### Standard Users

- Can create only private projects
- Can see only their own projects
- Cannot remove projects

Roles at the project level
--------------------------

These role are related to the project permission.

### Project Managers

- Can manage only their own projects
- Can access to reports and budget section

### Project Members

- Can do any daily operations in their projects (create and move tasks...)
- Cannot configure projects

Note: Any "Standard User" can be promoted "Project Manager" for a given project, they don't necessary need to be "Project Administrator".

Local and remote users
----------------------

- A local user is an account that use the database to store credentials. Local users use the login form for the authentication.
- A remote user is an account that use an external system to store credentials. By example, it can be LDAP, Github or Google accounts. Authentication of these users can be done through the login form or not.

Add a new user
--------------

To add a new user, you must be administrator.

1. From the dashboard, go to the menu **User Management**
2. On the top, you have a link **New local user** or **New remote user**
3. Fill the form and save

![New user](http://kanboard.net/screenshots/documentation/new-user.png)

When you create a **local user**, you have to specify at least those values:

- **username**: This is the unique identifier of your user (login)
- **password**: The password of your user must have at least 6 characters

For **remote users**, only the username is mandatory. You can also pre-link Github or Google accounts if you already know their unique id.

Edit users
----------

When you go to the **users** menu, you have the list of users, to modify a user click on the **edit link**.

- If you are a regular user, you can change only your own profile
- You have to be administrator to be able to edit any users

Remove users
------------

From the **users** menu, click on the link **remove**. This link is visible only if you are administrator.

If you remove a specific user, **tasks assigned to this person will be unassigned** after the operation.
