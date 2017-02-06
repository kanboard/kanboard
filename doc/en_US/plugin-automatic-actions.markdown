Adding Automatic Actions
========================

Adding a new automatic action is pretty simple.

Creating a new action
---------------------

Your automatic action must inherit of the class `Kanboard\Action\Base`.
Several abstract methods must be implemented by yourself:

| Method                              | Description                                                      |
|-------------------------------------|------------------------------------------------------------------|
| `getDescription()`                  | Description visible in the user interface                        |
| `getCompatibleEvents()`             | Get the list of compatible events                                |
| `getActionRequiredParameters()`     | Get the required parameter for the action (defined by the user)  |
| `getEventRequiredParameters()`      | Get the required parameter for the event                         |
| `doAction(array $data)`             | Execute the action, must return true on success                  |
| `hasRequiredCondition(array $data)` | Check if the event data meet the action condition                |

Your automatic action is identified in Kanboard by using the absolute class name with the name space included.

Adding new events
-----------------

The list of application events is available in the class `Kanboard\Core\Event\EventManager::getAll()`.
However, if your plugin fires new events, you can register these events like that:

```php
$this->actionManager->getAction('\Kanboard\Plugin\MyPlugin\MyActionName')->addEvent('my.event', 'My event description');
```

You can extend the list of compatible events of existing actions by using the same method.

Registering the action
----------------------

You have to call the method `register()` from the class `Kanboard\Core\Action\ActionManager`:

```php
<?php

namespace Kanboard\Plugin\AutomaticAction;

use Kanboard\Core\Plugin\Base;
use Kanboard\Plugin\AutomaticAction\Action\TaskRename;

class Plugin extends Base
{
    public function initialize()
    {
        $this->actionManager->register(new TaskRename($this->container));
    }
}
```

Example
-------

- [Automatic Action example](https://github.com/kanboard/plugin-example-automatic-action)
