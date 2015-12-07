Automatic Actions
=================

To minimize the user interaction, Kanboard support automated actions.

Each automatic action is defined like that:

- An event to listen
- Action linked to this event
- Eventually there is some parameters to define

Each project has a different set of automatic actions, the configuration panel is located on the project listing page, just click on the link **Automatic actions**.

Add a new action
----------------

### Choose an action

![Choose action](http://kanboard.net/screenshots/documentation/project-automatic-action-step1.png)

### Choose an event

![Choose an event](http://kanboard.net/screenshots/documentation/project-automatic-action-step2.png)

### Define action parameters

![Define parameters](http://kanboard.net/screenshots/documentation/project-automatic-action-step3.png)

List of available events
------------------------

- Move a task to another column
- Move a task to another position in the same column
- Task modification
- Task creation
- Reopen a task
- Closing a task
- Task creation or modification
- Task assignee change
- Task link created or updated
- Github commit received
- Github issue opened
- Github issue closed
- Github issue reopened
- Github issue assignee change
- Github issue label change
- Github issue comment created
- Gitlab issue opened
- Gitlab issue closed
- Gitlab commit received
- Bitbucket commit received
- Bitbucket issue opened
- Bitbucket issue closed
- Bitbucket issue reopened
- Bitbucket issue assignee change
- Bitbucket issue comment created

List of available actions
-------------------------

- Close the task
- Open a task
- Assign the task to a specific user
- Assign the task to the person who does the action
- Duplicate the task to another project
- Move the task to another project
- Move the task to another column when assigned to a user
- Move the task to another column when assignee is cleared
- Assign color when the task is moved to a specific column
- Assign color to a specific user
- Assign automatically a color based on a category
- Assign automatically a category based on a color
- Create a comment from an external provider
- Create a task from an external provider
- Add a comment log when moving the task between columns
- Change the assignee based on an external username
- Change the category based on an external label
- Automatically update the start date
- Move the task to another column when the category is changed
- Send a task by email to someone
- Change task color when using a specific task link

Examples
--------

Here are some examples used in real life:

### When I move a task to the column "Done", automatically close this task

- Choose action: **Close the task**
- Choose the event: **Move a task to another column**
- Define action parameter: **Column = Done** (this is the destination column)

### When I move a task to the column "To be validated", assign this task to a specific user

- Choose the action: **Assign the task to a specific user**
- Choose the event: **Move a task to another column**
- Define the action parameters: **Column = To be validated** and **User = Bob** (Bob is our tester)

### When I move a task to the column "Work in progress", assign this task to the current user

- Choose action: **Assign the task to the person who does the action**
- Choose the event: **Move a task to another column**
- Define action parameter: **Column = Work in progress**

### When a task is completed, duplicate this task to another project

Let's say we have two projects "Customer orders" and "Production", once the order is validated, swap it to the "Production" project.

- Choose action: **Duplicate the task to another project**
- Choose the event: **Closing a task**
- Define action parameters: **Column = Validated** and **Project = Production**

### When a task is moved to the last column, move the exact same task to another project

Let's say we have two projects "Ideas" and "Development", once the idea is validated, swap it to the "Development" project.

- Choose action: **Move the task to another project**
- Choose the event: **Move a task to another column**
- Define action parameters: **Column = Validated** and **Project = Development**

### I want to assign automatically a color to the user Bob

- Choose action: **Assign a color to a specific user**
- Choose the event: **Task assignee change**
- Define action parameters: **Color = Green** and **Assignee = Bob**

### I want to assign a color automatically to the defined category "Feature Request"

- Choose action: **Assign automatically a color based on a category**
- Choose the event: **Task creation or modification**
- Define action parameters: **Color = Blue** and **Category = Feature Request**

### I want to set the start date automatically when the task is moved to the column "Work in progress"

- Choose action: **Automatically update the start date**
- Choose the event: **Move a task to another column**
- Define action parameters: **Column = Work in progress**
