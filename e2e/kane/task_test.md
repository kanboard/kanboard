---
mode: testing
url: http://localhost:8000/login
max_steps: 35
tags: [smoke, tasks]
---

# Create, comment on and close a task

## Log in as admin
Open http://localhost:8000/login.
Type "admin" into the "Username" field and "admin" into the "Password" field, then click "Sign in".
Verify the URL contains "/dashboard".

## Create a project and open its board
Open http://localhost:8000/project/create.
Type "Launch Plan" into the "Name" field and click "Save".
Click the "Board" link.
Verify the board shows a "Backlog" column.

## Add a task to the Backlog column
Click the "+" (Add a new task) button in the "Backlog" column header.
In the new task form, type "Write landing page copy" into the "Title" field and click "Save".
Verify a task card titled "Write landing page copy" is shown in the "Backlog" column.

## Comment on the task
Click the "Write landing page copy" task title to open the task.
Click "Add a comment" in the task sidebar, type "Draft ready for review" into the comment field and click "Save".
Verify the task page shows the comment "Draft ready for review".

## Close the task
Click "Close this task" in the task sidebar, then click "Yes" in the confirmation dialog.
Verify the task page shows that the task is closed.
