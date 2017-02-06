External Task Providers
=======================

Kanboard can be used to manage tasks stored in another system.
For example, an external system can be a bug tracker or any kind of ticketing software.
In this way, you can use Kanboard to manage external tasks in the same way as native tasks.

Workflow
--------

Creation:

1. The end-user select an alternative task provider during the task creation
2. The external task provider expose a form to the user to be able to fetch the external task
3. The external task is retrieved from the other system
4. A customized form is shown to the user

Visualization:

When the task detail page is opened, Kanboard will load asynchronously the remote task.
This information might be cached by the plugin to improve the loading time.

Modification:

Optionally, the plugin can offer a custom form to save extra information to the external system.

Interfaces
----------

External task providers must implements at least two interfaces:

- `Kanboard\Core\ExternalTask\ExternalTaskProviderInterface`
- `Kanboard\Core\ExternalTask\ExternalTaskInterface`

### ExternalTaskProviderInterface

| Method                                             | Usage                                                           |
|----------------------------------------------------|-----------------------------------------------------------------|
| `getName()`                                        | Get provider name (label)                                       |
| `fetch()`                                          | Retrieve task from external system or cache                     |
| `save($uri, array $formValues, array &$formErrors)`| Save external task to another system                            |
| `getImportFormTemplate()`                          | Get task import template name                                   |
| `getCreationFormTemplate()`                        | Get creation form template                                      |
| `getModificationFormTemplate()`                    | Get modification form template                                  |
| `getViewTemplate()`                                | Get task view template name                                     |
| `buildTaskUri(array $formValues)`                  | Build external task URI based on import form values             |

### ExternalTaskInterface

| Method                                            | Usage                                                           |
|---------------------------------------------------|-----------------------------------------------------------------|
| `getUri()`                                        | Return Uniform Resource Identifier for the task                 |
| `getFormValues()`                                 | Return a dict to populate the task form                         |


Exceptions
----------

The plugin may raise an exception if something goes wrong:

- `Kanboard\Core\ExternalTask\ExternalTaskException`: Generic error related to the external system
- `Kanboard\Core\ExternalTask\AccessForbiddenException`: Access not allowed by the external system
- `Kanboard\Core\ExternalTask\NotFoundException`: External task not found

Provider Registration
---------------------

```php
class Plugin extends Base
{
    public function initialize()
    {
        $this->externalTaskManager->register(new MyExternalTaskProvider());
    }
}
```
