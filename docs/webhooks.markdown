Webhooks
========

Webhooks are useful to perform actions from external applications (shell-scripts, git hooks...).

How to create a task with a webhook?
------------------------------------

Firstly, you have to get the token from the preferences page. After that, just call this url from anywhere:

```bash
# Create a task for the default project inside the first column
curl "http://myserver/?controller=task&action=add&token=superSecretToken&title=mySuperTask"

# Create a task to another project inside a specific column with the color red
curl "http://myserver/?controller=task&action=add&token=superSecretToken&title=task123&project_id=3&column_id=7&color_id=red"
```
