Metadata
========

You can attach metadata for each project, task and user.
Metadata are custom fields, it's a key/value table.

By example your plugin can store external information for a task or new settings for a project.
Basically that allow you to exend the default fields without having to create new tables.

Attach metadata to tasks
------------------------

```php

// Return a dictionary of metadata (keys/values) for the $task_id
$this->taskMetadata->getAll($task_id);

// Get a value only for a task
$this->taskMetadata->get($task_id, 'my_plugin_variable', 'default_value');

// Return true if the metadata my_plugin_variable exists
$this->taskMetadata->exists($task_id, 'my_plugin_variable');

// Create or update metadata for the task
$this->taskMetadata->save($task_id, ['my_plugin_variable' => 'something']);
```

Metadata types
--------------

- TaskMetadata: `$this->taskMetadata`
- ProjectMetadata: `$this->projectMetadata`
- UserMetadata: `$this->userMetadata`

Notes
-----

- Always prefix the metadata name with your plugin name
