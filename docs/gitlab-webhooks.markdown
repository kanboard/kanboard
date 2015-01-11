Gitlab webhooks
===============

Gitlab events can be connected to Kanboard automatic actions.

List of supported events
------------------------

- Gitlab commit received
- Gitlab issue opened
- Gitlab issue closed

List of supported actions
-------------------------

- Create a task from an external provider
- Close a task

Configuration
-------------

![Github configuration](http://kanboard.net/screenshots/documentation/gitlab-webhooks.png)

1. On Kanboard, go to the project settings and choose the section **Integrations**
2. Copy the Gitlab webhook url
3. On Gitlab, go to the project settings and go the section **Webhooks**
4. Check the boxes **Push Events** and **Issues Events**
5. Paste the url and save

Examples
--------

### Close a Kanboard task when a commit pushed to Gitlab

- Choose the event: **Gitlab commit received**
- Choose the action: **Close the task**

When one or more commits are sent to Gitlab, Kanboard will receive the information, each commit message with a task number included will be closed.

Example:

- Commit message: "Fix bug #1234"
- That will close the Kanboard task #1234

### Create a Kanboard task when a new issue is opened on Gitlab

- Choose the event: **Gitlab issue opened**
- Choose the action: **Create a task from an external provider**

When a task is created from a Gitlab issue, the link to the issue is added to the description and the task have a new field named "Reference" (this is the Gitlab ticket number).

### Close a Kanboard task when an issue is closed on Gitlab

- Choose the event: **Gitlab issue closed**
- Choose the action: **Close the task**
