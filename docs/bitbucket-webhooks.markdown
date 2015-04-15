Bitbucket webhooks
==================

Bitbucket events can be connected to Kanboard automatic actions.

List of supported events
------------------------

- Bitbucket commit received

List of supported actions
-------------------------

- Close a task

Configuration
-------------

![Bitbucket configuration](http://kanboard.net/screenshots/documentation/bitbucket-webhooks.png)

1. On Kanboard, go to the project settings and choose the section **Integrations**
2. Copy the Bitbucket webhook url
3. On Bitbucket, go to the project settings and go to the section **Hooks**
4. Select the service **POST**
5. Paste the url and save

Examples
--------

### Close a Kanboard task when a commit pushed to Bitbucket

- Choose the event: **Bitbucket commit received**
- Choose the action: **Close the task**

When one or more commits are sent to Bitbucket, Kanboard will receive the information, each commit message with a task number included will be closed.

Example:

- Commit message: "Fix bug #1234"
- That will close the Kanboard task #1234
