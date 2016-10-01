Metadata
========

You can attach metadata for each project, task, user or for the whole application.
Metadata are custom fields, it's a key/value table.

For example your plugin can store external information for a task or new settings for a project.
Basically that allow you to extend the default fields without having to create new tables.

Attach metadata to tasks and remove them
------------------------

```php

// Return a dictionary of metadata (keys/values) for the $task_id
$this->taskMetadataModel->getAll($task_id);

// Get a value only for a task
$this->taskMetadataModel->get($task_id, 'my_plugin_variable', 'default_value');

// Return true if the metadata my_plugin_variable exists
$this->taskMetadataModel->exists($task_id, 'my_plugin_variable');

// Create or update metadata for the task
$this->taskMetadataModel->save($task_id, ['my_plugin_variable' => 'something']);

// Remove a metadata from a project
$this->projectMetadataModel->remove($project_id, my_plugin_variable);
```

Metadata types
--------------

- TaskMetadata: `$this->taskMetadataModel`
- ProjectMetadata: `$this->projectMetadataModel`
- UserMetadata: `$this->userMetadataModel`
- Settings/Config: `$this->configModel`

Notes
-----

- Always prefix the metadata name with your plugin name
