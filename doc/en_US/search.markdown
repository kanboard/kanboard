Advanced Search Syntax
======================

Kanboard uses a simple query language for advanced search. 
You can search in tasks, comments, subtasks, links but also in the activity stream.

Example of query
----------------

This example will return all tasks assigned to me with a due date for tomorrow and a title that contains "my title":

```
assigne:me due:tomorrow my title
```

Global search
-------------

### Search by task id or title

- Search by task id: `#123`
- Search by task id and task title: `123`
- Search by task title: anything that doesn't match any search attributes

### Search by status

Attribute: **status**

- Query to find open tasks: `status:open`
- Query to find closed tasks: `status:closed`

### Search by assignee

Attribute: **assignee**

- Query with the full name: `assignee:"Frederic Guillot"`
- Query with the username: `assignee:fguillot`
- Multiple assignee lookup: `assignee:user1 assignee:"John Doe"`
- Query for unassigned tasks: `assignee:nobody`
- Query for my assigned tasks: `assignee:me`

### Search by task creator

Attribute: **creator**

- Tasks created by myself: `creator:me`
- Tasks created by John Doe: `creator:"John Doe"`
- Tasks created by the user id #1: `creator:1`

### Search by subtask assignee

Attribute: **subtask:assignee**

- Example: `subtask:assignee:"John Doe"`

### Search by color

Attribute: **color**

- Query to search by color id: `color:blue`
- Query to search by color name: `color:"Deep Orange"`

### Search by the due date

Attribute: **due**

- Search tasks due today: `due:today`
- Search tasks due tomorrow: `due:tomorrow`
- Search tasks due yesterday: `due:yesterday`
- Search tasks due with the exact date: `due:2015-06-29`

The date must use the ISO 8601 format: **YYYY-MM-DD**.

All string formats supported by the `strtotime()` function are supported, for example `next Thursday`, `-2 days`, `+2 months`, `tomorrow`, etc.

Operators supported with a date:

- Greater than: **due:>2015-06-29**
- Lower than: **due:<2015-06-29**
- Greater than or equal: **due:>=2015-06-29**
- Lower than or equal: **due:<=2015-06-29**

### Search by modification date

Attribute: **modified** or **updated**

The date formats are the same as the due date.

There is also a filter by recently modified tasks: `modified:recently`.

This query will use the same value as the board highlight period configured in settings.

### Search by creation date

Attribute: **created**

Works in the same way as the modification date queries.

### Search by start date

Attribute: **started**

### Search by description

Attribute: **description** or **desc**

Example: `description:"text search"`

### Search by completion

Attribute: **completed**

### Search by external reference

The task reference is an external id of your task, by example a ticket number from another software.

- Find tasks with a reference: `ref:1234` or `reference:TICKET-1234`
- Wildcard search: `ref:TICKET-*`

### Search by category

Attribute: **category**

- Find tasks with a specific category: `category:"Feature Request"`
- Find all tasks that have those categories: `category:"Bug" category:"Improvements"`
- Find tasks with no category assigned: `category:none`

### Search by project

Attribute: **project**

- Find tasks by project name: `project:"My project name"`
- Find tasks by project id: `project:23`
- Find tasks for several projects: `project:"My project A" project:"My project B"`

### Search by columns

Attribute: **column**

- Find tasks by column name: `column:"Work in progress"`
- Find tasks for several columns: `column:"Backlog" column:ready`

### Search by swim-lane

Attribute: **swimlane**

- Find tasks by swim-lane: `swimlane:"Version 42"`
- Find tasks into several swim-lanes: `swimlane:"Version 1.2" swimlane:"Version 1.3"`

### Search by task link

Attribute: **link**

- Find tasks by link name: `link:"is a milestone of"`
- Find tasks into several links: `link:"is a milestone of" link:"relates to"`

### Search by comment

Attribute: **comment**

- Find comments that contains this title: `comment:"My comment message"`

### Search by tags

Attribute: **tag**

- Example: `tag:"My tag"`

### Search by score/complexity

Attribute: **score** or **complexity**

- `score:>=21`
- `complexity:8`

Activity stream search
----------------------

### Search events by task title

Attribute: **title** or none (default)

- Example: `title:"My task"`
- Search by task id: `#123`

### Search events by task status

Attribute: **status**

### Search by event creator

Attribute: **creator**

### Search by event creation date

Attribute: **created**

### Search events by project

Attribute: **project**
