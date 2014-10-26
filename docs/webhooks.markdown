Webhooks
========

Webhooks are useful to perform actions with external applications.

- Webhooks can be used to create a task by calling a simple URL (You can also do that by using the API)
- An external URL can be called automatically when a task is created or modified

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

How to call an external URL when a task is created or updated?
--------------------------------------------------------------

- There is two events available: **task creation** and **task modification**
- External URLs can be defined on the settings page
- When an event is triggered Kanboard call automatically the predefined URL
- The task data encoded in JSON is sent with a POST HTTP request
- The webhook token is also sent as a query string parameter, so you can check if the request is not usurped, it's also better if you use HTTPS.
- **Your custom URL must answer in less than 1 second**, those requests are synchronous (PHP limitation) and that can slow down the application if your script is too slow!

### Quick example with PHP

Start by creating a basic PHP script `index.php`:

```php
<?php

$body = file_get_contents('php://input');
file_put_contents('/tmp/webhook', $body);
```

This script dump the task data to the file `/tmp/webhook`.

Now run a webserver from the command line:

```bash
php -S 127.0.0.1:8081
```

After that, go the settings page of Kanboard, enter the right URL here `http://127.0.0.1:8081/`.
And finally, create a task and you should see the JSON payload in the file.

```javascript
{
    "task_id":"2",
    "title":"boo",
    "description":"",
    "project_id":"1",
    "owner_id":"0",
    "category_id":"0",
    "column_id":"2",
    "color_id":"yellow",
    "score":0,
    "date_due":0,
    "creator_id":1,
    "date_creation":1405981280,
    "position":0
}
```

For our example, Kanboard use this request to call your program:

```
POST http:://127.0.0.1:8081/?token=RANDOM_TOKEN_HERE

{... JSON payload ...}
```