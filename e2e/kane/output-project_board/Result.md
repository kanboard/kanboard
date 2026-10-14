---
test: ../project_board_test.md
status: passed
started: 2026-10-14T07:35:44.923Z
duration_s: 126
session_id: 0a3d1721-2ed9-4fbf-8c81-90f0186c0d9c
---

# Create a project and open its board — Result

## Log in as admin ✓ passed (50.4s)
md5: 414dea075432aa3e307af0c75217ab2e
Open http://localhost:8000/login.
Type "admin" into the "Username" field and "admin" into the "Password" field, then click "Sign in".
Verify the URL contains "/dashboard" and the page shows "Dashboard for admin".

## Create a project ✓ passed (36.3s)
md5: 230337a23e49d99156766fc36d93945d
Open http://localhost:8000/project/create.
Type "Website Redesign" into the "Name" field and click "Save".
Verify the page shows "Your project has been created successfully." and the project title "Website Redesign".

## Open the board ✓ passed (35.1s)
md5: 510e98a59b019b5dc5a6ab3fc6950f68
Click the "Board" link.
Verify the board shows the columns "Backlog", "Ready", "Work in progress" and "Done".
