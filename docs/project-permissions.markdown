Project permissions
===================

A project can have two kinds of people: **project managers** and **project members**.

- Project managers can manage the configuration of the project and access to the reports.
- Project members are standard users, they have less privileges.

When you create a new project, you are automatically assigned as a project manager.

Kanboard administrators can access to everything but they are not necessary project members or managers. Those permissions are defined at the project level.

Permissions for each role
-------------------------

### Project members

- Use the board (create, move and edit tasks)
- Remove only tasks created by themselves

### Project managers

- Use the board
- Configure the project
	- Share, rename, duplicate and disable the project
	- Manage swimlanes, categories, columns and users
	- Edit automatic actions
- CSV Exports
- Remove tasks of any project members
- Access to the analytics section

They **cannot remove the project**.

Manage users and permissions
----------------------------

To define project roles, go to the **project configuration page** then click on **User management**.

### User management

![Project permissions](http://kanboard.net/screenshots/documentation/project-permissions.png)

From there, you can choose to add new members, change the role or revoke user access.

### Allow everybody

If you choose to allow everybody (all Kanboard users), the project is considered public.

That means there is no role management anymore. Permissions per user cannot be applied.
