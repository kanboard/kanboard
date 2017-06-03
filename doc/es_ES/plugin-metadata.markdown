Metadatos
=========

Tu piedes adjutnar metadatos para cada projecto,tarea , usuaio o para toda la aplicación
Los metadatos son campos personalisados, es una key/value de una tabla.

Por ejemplo nuestro plugin puede almacenar información externa para una tarea o nuevas configuraciones para un proyecto.
Básicamente le permiten ampliar los campos predeterminados sin tener que crear nuevas tablas .

Adjuntar y eliminar metadatos para tareas 
--------------------------------------------

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

Tipos de metadatos
------------------

- TaskMetadata: `$this->taskMetadataModel`
- ProjectMetadata: `$this->projectMetadataModel`
- UserMetadata: `$this->userMetadataModel`
- Settings/Config: `$this->configModel`

Notas
-----

- Siempre prefijo del nombre de metadatos con el nombre del plugin
