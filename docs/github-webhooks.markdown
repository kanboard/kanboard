Github webhooks integration
===========================

Kanboard can be synchronized with Github.
Currently, it's only a one-way synchronization: Github to Kanboard.

Github webhooks are plugged to Kanboard automatic actions.
When an event occurs on Github, an action can be performed on Kanboard.

List of available events
------------------------

- Github commit received
- Github issue opened
- Github issue closed
- Github issue reopened
- Github issue assignee change
- Github issue label change
- Github issue comment created

List of available actions
-------------------------

- Create a task from an external provider
- Change the assignee based on an external username
- Change the category based on an external label
- Create a comment from an external provider
- Close a task
- Open a task

Configuration on Github
-----------------------

Go to your project settings page, on the left choose "Webhooks & Services", then click on the button "Add webhook".

![Github configuration](http://kanboard.net/screenshots/documentation/github-webhooks.png)

- **Payload url**: Copy and paste the link from the Kanboard project settings (section **Integrations > Github**).
- Select **"Send me everything"**

![Github webhook](http://kanboard.net/screenshots/documentation/kanboard-github-webhooks.png)

Each time an event happens, Github will send an event to Kanboard now.
The Kanboard webhook url is protected by a random token.

Everything else is handled by automatic actions in your Kanboard project settings.

Examples
--------

### Close a Kanboard task when a commit pushed to Github

- Choose the event: **Github commit received**
- Choose the action: **Close the task**

When one or more commits are sent to Github, Kanboard will receive the information, each commit message with a task number included will be closed.

Example:

- Commit message: "Fix bug #1234"
- That will close the Kanboard task #1234

### Create a Kanboard task when a new issue is opened on Github

- Choose the event: **Github issue opened**
- Choose the action: **Create a task from an external provider**

When a task is created from a Github issue, the link to the issue is added to the description and the task have a new field named "Reference" (this is the Github ticket number).

### Close a Kanboard task when an issue is closed on Github

- Choose the event: **Github issue closed**
- Choose the action: **Close the task**

### Reopen a Kanboard task when an issue is reopened on Github

- Choose the event: **Github issue reopened**
- Choose the action: **Open the task**

### Assign a task to a Kanboard user when an issue is assigned on Github

- Choose the event: **Github issue assignee change**
- Choose the action: **Change the assignee based on an external username**

Note: The username must be the same between Github and Kanboard.

### Assign a category when an issue is tagged on Github

- Choose the event: **Github issue label change**
- Choose the action: **Change the category based on an external label**
- Define the label and the category

### Create a comment on Kanboard when an issue is commented on Github

- Choose the event: **Github issue comment created**
- Choose the action: **Create a comment from an external provider**

Note: The username of the comment author must be the same between Github and Kanboard and the task must exists before.
