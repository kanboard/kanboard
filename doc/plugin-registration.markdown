Plugin Registration
===================

Directory structure
-------------------

Plugins are stored in the `plugins` subdirectory. An example of a plugin directory structure:

```bash
plugins
└── Budget            <= Plugin name
    ├── Asset         <= Javascript/CSS files
    ├── Controller
    ├── LICENSE       <= Plugin license
    ├── Locale
    │   ├── fr_FR
    │   ├── it_IT
    │   ├── ja_JP
    │   └── zh_CN
    ├── Model
    ├── Plugin.php    <= Plugin registration file
    ├── README.md
    ├── Schema        <= Database migrations
    ├── Template
    └── Test          <= Unit tests
```

Only the registration file `Plugin.php` is required. Other folders are optionals.

The first letter of the plugin name must be capitalized.

Plugin Registration File
------------------------

Kanboard will scan the directory `plugins` and load automatically everything under this directory. The file `Plugin.php` is used to load and register the plugin.

Example of `Plugin.php` file (`plugins/Foobar/Plugin.php`):

```php
<?php

namespace Kanboard\Plugin\Foobar;

use Kanboard\Core\Plugin\Base;

class Plugin extends Plugin\Base
{
    public function initialize()
    {
        $this->template->hook->attach('template:layout:head', 'theme:layout/head');
    }
}
```

This file should contains a class `Plugin` defined under the namespace `Kanboard\Plugin\Yourplugin` and extends `Kanboard\Core\Plugin\Base`.

The only required method is `initialize()`. This method is called for each request when the plugin is loaded.

Plugin Methods
--------------

Available methods from `Kanboard\Core\Plugin\Base`:

- `initialize()`: Executed when the plugin is loaded
- `getClasses()`: Return all classes that should be stored in the dependency injection container
- `on($event, $callback)`: Listen on internal events
- `getPluginName()`: Should return plugin name
- `getPluginAuthor()`: Should return plugin author
- `getPluginVersion()`: Should return plugin version
- `getPluginDescription()`: Should return plugin description
- `getPluginHomepage()`: Should return plugin Homepage (link)
- `setContentSecurityPolicy(array $rules)`: Override default HTTP CSP rules

Your plugin registration class also inherit from `Kanboard\Core\Base`, that means you can access to all classes and methods of Kanboard easily.

This example will fetch the user #123:

```php
$this->user->getById(123);
```

Plugin Translations
-------------------

Plugin can be translated in the same way the rest of the application. You must load the translations yourself when the session is created:

```php
$this->on('session.bootstrap', function($container) {
    Translator::load($container['config']->getCurrentLanguage(), __DIR__.'/Locale');
});
```

The translations must be stored in `plugins/Myplugin/Locale/xx_XX/translations.php`.

Dependency Injection Container
------------------------------

Kanboard use Pimple, a simple PHP Dependency Injection Container. However, Kanboard can register any class in the container easily.

Those classes are available everywhere in the application and only one instance is created.

Here an example to register your own models in the container:

```php
public function getClasses()
{
    return array(
        'Plugin\Budget\Model' => array(
            'HourlyRate',
            'Budget',
        )
    );
}
```

Now, if you use a class that extends from `Core\Base`, you can access directly to those class instance:

```php
$this->hourlyRate->remove(123);
$this->budget->getDailyBudgetBreakdown(456);

// It's the same thing as using the container:
$this->container['hourlyRate']->getAll();
```

Keys of the containers are unique across the application. If you override an existing class you will change the default behavior.

Event Listening
----------------

Kanboard use internal events and your plugin can listen and perform actions on these events.

```php
$this->on('session.bootstrap', function($container) {
    // Do something
});
```

- The first argument is the event name
- The second argument is a PHP callable function (closure or class method)

Extend Automatic Actions
------------------------

To define a new automatic action with a plugin, you just need to call the method `extendActions()` from the class `Kanboard\Model\Action`, here an example:

```php
<?php

namespace Kanboard\Plugin\AutomaticAction;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->action->extendActions(
            '\Kanboard\Plugin\AutomaticAction\Action\DoSomething', // Use absolute namespace
            t('Do something when the task color change')
        );
    }
}
```

- The first argument of the method `extendActions()` is the action class with the complete namespace path. **The namespace path must starts with a backslash** otherwise Kanboard will not be able to load your class.
- The second argument is the description of your automatic action.

The automatic action class must inherits from the class `Kanboard\Action\Base` and implements all abstract methods:

- `getCompatibleEvents()`
- `getActionRequiredParameters()`
- `getEventRequiredParameters()`
- `doAction(array $data)`
- `hasRequiredCondition(array $data)`

For more details you should take a look to existing automatic actions or this [plugin example](https://github.com/kanboard/plugin-example-automatic-action).

Extend ACL
----------

Kanboard use an access list for privilege separations. Your extension can add new rules:

```php
$this->acl->extend('project_manager_acl', array('mycontroller' => '*'));
```

- The first argument is the ACL name
- The second argument are the new rules
    + Syntax to include only some actions: `array('controller' => array('action1', 'action2'))`
    + Syntax to include all actions of a controller: `array('controller' => '*')`
    + Everything is lowercase

List of ACL:

- `public_acl`: Public access without authentication
- `project_member_acl`: Project member access
- `project_manager_acl`: Project manager access
- `project_admin_acl`: Project Admins
- `admin_acl`: Administrators
