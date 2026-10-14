---
test: ../task_test.md
status: passed
started: 2026-10-14T07:38:13.656Z
duration_s: 230
session_id: 95a9ae6e-6d0c-4231-8370-a139c6150e8c
---

# Create, comment on and close a task — Result

## Log in as admin ✓ passed (45.3s)
md5: a3ac19836908eb7aaafd158a5f9fd5e0
Open http://localhost:8000/login.
Type "admin" into the "Username" field and "admin" into the "Password" field, then click "Sign in".
Verify the URL contains "/dashboard".

## Create a project and open its board ✓ passed (45.7s)
md5: 68491fa3bf5b6326e35336d992b0ee7f
Open http://localhost:8000/project/create.
Type "Launch Plan" into the "Name" field and click "Save".
Click the "Board" link.
Verify the board shows a "Backlog" column.

## Add a task to the Backlog column ✓ passed (41.6s)
md5: 6d7d4a0ff5176c152d2a7ee5c638c908
Click the "+" (Add a new task) button in the "Backlog" column header.
In the new task form, type "Write landing page copy" into the "Title" field and click "Save".
Verify a task card titled "Write landing page copy" is shown in the "Backlog" column.

## Comment on the task ✓ passed (43.1s)
md5: 27648d38396c6c4a26663fdd9bca6f15
Click the "Write landing page copy" task title to open the task.
Click "Add a comment" in the task sidebar, type "Draft ready for review" into the comment field and click "Save".
Verify the task page shows the comment "Draft ready for review".

## Close the task ✓ passed (50.9s)
md5: 44d6ab3ab440e95188f4d4b4aa066f9b
Click "Close this task" in the task sidebar, then click "Yes" in the confirmation dialog.
Verify the task page shows that the task is closed.
