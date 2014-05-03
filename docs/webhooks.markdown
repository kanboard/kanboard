Webhooks
========

Webhooks are useful to perform actions from external applications (shell-scripts, git hooks...).

How to create a task with a webhook?
------------------------------------

Firstly, you have to get the token from the settings page. After that, just call this url from anywhere:

```bash
# Create a task for the default project inside the first column
curl "http://myserver/?controller=task&action=add&token=superSecretToken&title=mySuperTask"

# Create a task to another project inside a specific column with the color red
curl "http://myserver/?controller=task&action=add&token=superSecretToken&title=task123&project_id=3&column_id=7&color_id=red"
```

Available responses
-------------------

- When a task is created successfully, Kanboard return the message "OK" in plain text.
- However if the task creation fail, you will got a "FAILED" message.
- If the token is wrong, you got a "Not Authorized" message and a HTTP status code 401.

Available parameters
--------------------

Base url: `http://YOUR_SERVER_HOSTNAME/?controller=task&action=add`

- `token`: Token displayed on the settings page (required)
- `title`: Task title (required)
- `description`: Task description
- `color_id`: Supported colors are yellow, blue, green, purple, red, orange and grey
- `project_id`: Project id (Get the id from the projects page, mouse over on the project title)
- `owner_id`: Assignee (Get the user id from the users page, mouse over on the username)
- `column_id`: Column on the board (Get the column id from the projects page, mouse over on the column name)

Only the token and the title parameters are mandatory. The different id can also be found in the database.
