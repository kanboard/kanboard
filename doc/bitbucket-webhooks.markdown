Bitbucket webhooks
==================

Bitbucket events can be connected to Kanboard automatic actions.

List of supported events
------------------------

- Bitbucket commit received
- Bitbucket issue opened
- Bitbucket issue closed
- Bitbucket issue reopened
- Bitbucket issue assignee change
- Bitbucket issue comment created

List of supported actions
-------------------------

- Create a task from an external provider
- Change the assignee based on an external username
- Create a comment from an external provider
- Close a task
- Open a task

Configuration
-------------

![Bitbucket configuration](http://kanboard.net/screenshots/documentation/bitbucket-webhooks.png)

1. On Kanboard, go to the project settings and choose the section **Integrations**
2. Copy the Bitbucket webhook url
3. On Bitbucket, go to the project settings and go to the section **Webhooks**
4. Choose a title for your webhook and paste the Kanboard url

Examples
--------

### Close a Kanboard task when a commit pushed to Bitbucket

- Choose the event: **Bitbucket commit received**
- Choose the action: **Close the task**

When one or more commits are sent to Bitbucket, Kanboard will receive the information, each commit message with a task number included will be closed.

Example:

- Commit message: "Fix bug #1234"
- That will close the Kanboard task #1234

### Add comment when a commit received

- Choose the event: **Bitbucket commit received**
- Choose the action: **Create a comment from an external provider**

The comment will contains the commit message and the url to the commit.
