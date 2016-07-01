Metadata[¶](#metadata "Ссылка на этот заголовок")

=================================================



You can attach metadata for each project, task, user or for the whole application. Metadata are custom fields, it’s a key/value table.



For example your plugin can store external information for a task or new settings for a project. Basically that allow you to extend the default fields without having to create new tables.



Attach metadata to tasks and remove them[¶](#attach-metadata-to-tasks-and-remove-them "Ссылка на этот заголовок")

-----------------------------------------------------------------------------------------------------------------



    // Return a dictionary of metadata (keys/values) for the $task_id

    $this->taskMetadata->getAll($task_id);



    // Get a value only for a task

    $this->taskMetadata->get($task_id, 'my_plugin_variable', 'default_value');



    // Return true if the metadata my_plugin_variable exists

    $this->taskMetadata->exists($task_id, 'my_plugin_variable');



    // Create or update metadata for the task

    $this->taskMetadata->save($task_id, ['my_plugin_variable' => 'something']);



    // Remove a metadata from a project

    $this->projectMetadata->remove($project_id, my_plugin_variable);



Metadata types[¶](#metadata-types "Ссылка на этот заголовок")

-------------------------------------------------------------



-   TaskMetadata: `$this->taskMetadata`{.docutils .literal}

-   ProjectMetadata: `$this->projectMetadata`{.docutils .literal}

-   UserMetadata: `$this->userMetadata`{.docutils .literal}

-   Settings/Config: `$this->config`{.docutils .literal}



Notes[¶](#notes "Ссылка на этот заголовок")

-------------------------------------------



-   Always prefix the metadata name with your plugin name



### [Оглавление](index.markdown)



-   [Metadata](#)

    -   [Attach metadata to tasks and remove them](#attach-metadata-to-tasks-and-remove-them)

    -   [Metadata types](#metadata-types)

    -   [Notes](#notes)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/plugin-metadata.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/plugin-metadata.txt)

