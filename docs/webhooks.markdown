Webhooks
========

Webhooks are useful to perform actions with external applications.

- Webhooks can be used to create a task by calling a simple URL (You can also do that with the API)
- An external URL can be called automatically when an event occurs in Kanboard (task creation, comment updated, etc)

How to write a webhook receiver?
--------------------------------

All internal events of Kanboard can be sent to an external URL.

- The webhook url have to be defined in **Settings > Webhooks > Webhook URL**.
- When an event is triggered Kanboard call automatically the predefined URL
- The data are encoded in JSON format and sent with a POST HTTP request
- The webhook token is also sent as a query string parameter, so you can check if the request really come from Kanboard.
- **Your custom URL must answer in less than 1 second**, those requests are synchronous (PHP limitation) and that can slow down the user interface if your script is too slow!

### List of supported events

- comment.create
- comment.update
- file.create
- task.move.project
- task.move.column
- task.move.position
- task.move.swimlane
- task.update
- task.create
- task.close
- task.open
- task.assignee_change
- subtask.update
- subtask.create

### Example of HTTP request

```
POST https://your_webhook_url/?token=WEBHOOK_TOKEN_HERE
User-Agent: Kanboard Webhook
Content-Type: application/json
Connection: close

{
  "event_name": "task.move.column",
  "event_data": {
    "task_id": "1",
    "project_id": "1",
    "position": 1,
    "column_id": "1",
    "swimlane_id": "0",
    "src_column_id": "2",
    "dst_column_id": "1",
    "date_moved": "1431991532",
    "recurrence_status": "0",
    "recurrence_trigger": "0"
  }
}
```

All event payloads are in the following format:

```json
{
  "event_name": "model.event_name",
  "event_data": {
    "key1": "value1",
    "key2": "value2",
    ...
  }
}
```

The `event_data` values are not necessary normalized across events.

### Examples of event payloads

Task creation:

```json
{
  "event_name": "task.create",
  "event_data": {
    "title": "Demo",
    "description": "",
    "project_id": "1",
    "owner_id": "1",
    "category_id": 0,
    "swimlane_id": 0,
    "column_id": "2",
    "color_id": "yellow",
    "score": 0,
    "time_estimated": 0,
    "date_due": 0,
    "creator_id": 1,
    "date_creation": 1431991532,
    "date_modification": 1431991532,
    "date_moved": 1431991532,
    "position": 1,
    "task_id": 1
  }
}
```

Task modification:

```json
{
  "event_name": "task.update",
  "event_data": {
    "id": "1",
    "title": "Demo",
    "description": "",
    "date_creation": "1431991532",
    "color_id": "yellow",
    "project_id": "1",
    "column_id": "1",
    "owner_id": "1",
    "position": "1",
    "is_active": "1",
    "date_completed": null,
    "score": "0",
    "date_due": "0",
    "category_id": "2",
    "creator_id": "1",
    "date_modification": 1431991603,
    "reference": "",
    "date_started": 1431993600,
    "time_spent": 0,
    "time_estimated": 0,
    "swimlane_id": "0",
    "date_moved": "1431991572",
    "recurrence_status": "0",
    "recurrence_trigger": "0",
    "recurrence_factor": "0",
    "recurrence_timeframe": "0",
    "recurrence_basedate": "0",
    "recurrence_parent": null,
    "recurrence_child": null,
    "task_id": "1",
    "changes": {
      "category_id": "2"
    }
  }
}
```

Task update events have a field called `changes` that contains updated values.

Move a task to another column:

```json
{
  "event_name": "task.move.column",
  "event_data": {
    "task_id": "1",
    "project_id": "1",
    "position": 1,
    "column_id": "1",
    "swimlane_id": "0",
    "src_column_id": "2",
    "dst_column_id": "1",
    "date_moved": "1431991532",
    "recurrence_status": "0",
    "recurrence_trigger": "0"
  }
}
```

Move a task to another position:

```json
{
  "event_name": "task.move.position",
  "event_data": {
    "task_id": "2",
    "project_id": "1",
    "position": 1,
    "column_id": "1",
    "swimlane_id": "0",
    "src_column_id": "1",
    "dst_column_id": "1",
    "date_moved": "1431996905",
    "recurrence_status": "0",
    "recurrence_trigger": "0"
  }
}
```

Comment creation:

```json
{
  "event_name": "comment.create",
  "event_data": {
    "id": 1,
    "task_id": "1",
    "user_id": "1",
    "comment": "test",
    "date_creation": 1431991615
  }
}
```

Comment modification:

```
{
  "event_name": "comment.update",
  "event_data": {
    "id": "1",
    "task_id": "1",
    "user_id": "1",
    "comment": "test edit"
  }
}
```

Subtask creation:

```json
{
  "event_name": "subtask.create",
  "event_data": {
    "id": 3,
    "task_id": "1",
    "title": "Test",
    "user_id": "1",
    "time_estimated": "2",
    "position": 3
  }
}
```

Subtask modification:

```json
{
  "event_name": "subtask.update",
  "event_data": {
    "id": "1",
    "status": 1,
    "task_id": "1"
  }
}
```

File upload:

```json
{
  "event_name": "file.create",
  "event_data": {
    "task_id": "1",
    "name": "test.png"
  }
}
```

Screenshot created:

```json
{
  "event_name": "file.create",
  "event_data": {
    "task_id": "2",
    "name": "Screenshot taken May 19, 2015 at 10:56 AM"
  }
}
```

Note: Webhooks configuration and payload have changed since Kanboard >= 1.0.15

How to create a task with a webhook?
------------------------------------

Firstly, you have to get the token from the settings page. After that, just call this url from anywhere:

```bash
# Create a task for the default project inside the first column
curl "http://myserver/?controller=webhook&action=task&token=superSecretToken&title=mySuperTask"

# Create a task to another project inside a specific column with the color red
curl "http://myserver/?controller=webhook&action=task&token=superSecretToken&title=task123&project_id=3&column_id=7&color_id=red"
```

### Available responses

- When a task is created successfully, Kanboard return the message "OK" in plain text.
- However if the task creation fail, you will got a "FAILED" message.
- If the token is wrong, you got a "Not Authorized" message and a HTTP status code 401.

### Available parameters

Base URL: `http://YOUR_SERVER_HOSTNAME/?controller=webhook&action=task`

- `token`: Token displayed on the settings page (required)
- `title`: Task title (required)
- `description`: Task description
- `color_id`: Supported colors are yellow, blue, green, purple, red, orange and grey
- `project_id`: Project id (Get the id from the project page)
- `owner_id`: Assignee (Get the user id from the users page)
- `column_id`: Column on the board (Get the column id from the projects page, mouse over on the column name)

Only the token and the title parameters are mandatory. The different id can also be found in the database.
