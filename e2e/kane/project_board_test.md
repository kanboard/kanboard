---
mode: testing
url: http://localhost:8000/login
max_steps: 20
tags: [smoke, projects]
---

# Create a project and open its board

## Log in as admin
Open http://localhost:8000/login.
Type "admin" into the "Username" field and "admin" into the "Password" field, then click "Sign in".
Verify the URL contains "/dashboard" and the page shows "Dashboard for admin".

## Create a project
Open http://localhost:8000/project/create.
Type "Website Redesign" into the "Name" field and click "Save".
Verify the page shows "Your project has been created successfully." and the project title "Website Redesign".

## Open the board
Click the "Board" link.
Verify the board shows the columns "Backlog", "Ready", "Work in progress" and "Done".
