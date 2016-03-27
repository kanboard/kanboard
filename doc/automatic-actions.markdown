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

Click on the link **Add a new automatic action**.

![Automatique action](screenshots/automatic-action-creation.png)

- Choose an action
- Then, select an event
- And finally, define the parameters

List of available actions
-------------------------

- Create a comment from an external provider
- Add a comment log when moving the task between columns
- Assign automatically a category based on a color
- Change the category based on an external label
- Assign automatically a category based on a link
- Assign automatically a color based on a category
- Assign a color when the task is moved to a specific column
- Change task color when using a specific task link
- Assign a color to a specific user
- Assign the task to the person who does the action
- Assign the task to the person who does the action when the column is changed
- Assign the task to a specific user
- Change the assignee based on an external username
- Close the task
- Close a task in a specific column
- Create a task from an external provider
- Duplicate the task to another project
- Send a task by email to someone
- Move the task to another project
- Move the task to another column when assigned to a user
- Move the task to another column when the category is changed
- Move the task to another column when assignee is cleared
- Open a task
- Automatically update the start date

Examples
--------

Here are some examples used in real life:

### When I move a task to the column "Done", automatically close this task

- Choose action: **Close a task in a specific column**
- Choose the event: **Move a task to another column**
- Define action parameter: **Column = Done** (this is the destination column)

### When I move a task to the column "To be validated", assign this task to a specific user

- Choose the action: **Assign the task to a specific user**
- Choose the event: **Move a task to another column**
- Define the action parameters: **Column = To be validated** and **User = Bob** (Bob is our tester)

### When I move a task to the column "Work in progress", assign this task to the current user

- Choose action: **Assign the task to the person who does the action when the column is changed**
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
