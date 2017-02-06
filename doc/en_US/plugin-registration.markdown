Plugin Registration
===================

Project skeleton generator
--------------------------

You can use `cookiecutter` to create the project structure of your plugin automatically.

Install Cookiecutter:

```bash
pip install -U cookiecutter
```

Run Kanboard cookiecutter:

```bash
cookiecutter gh:kanboard/cookiecutter-plugin
plugin_name [My Plugin]: Some Plugin
plugin_namespace [MyPlugin]: SomePlugin
plugin_author [Plugin Author]: Me
plugin_description [My plugin is awesome]:
plugin_homepage [https://github.com/kanboard/plugin-myplugin]:
```

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

Only the registration file `Plugin.php` is required. Other folders are optional.

The first letter of the plugin name must be capitalized.

Plugin Registration File
------------------------

Kanboard will scan the directory `plugins` and load automatically everything under this directory. The file `Plugin.php` is used to load and register the plugin.

Example of `Plugin.php` file (`plugins/Foobar/Plugin.php`):

```php
<?php

namespace Kanboard\Plugin\Foobar;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->template->hook->attach('template:layout:head', 'theme:layout/head');
    }

    public function getCompatibleVersion()
    {
        // Examples:
        // >=1.0.37
        // <1.0.37
        // <=1.0.37
        return '1.0.37';
    }
}
```

This file should contain a class `Plugin` defined under the namespace `Kanboard\Plugin\Yourplugin` and extends `Kanboard\Core\Plugin\Base`.

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
- `onStartup()`: If present, this method is executed automatically when the event "app.bootstrap" is triggered
- `getCompatibleVersion()`: You may want to specify the Kanboard version compatible with the plugin

Your plugin registration class can also inherit from Kanboard\Core\Base, that way you can access all classes and methods of Kanboard easily.

This example will fetch the user #123:

```php
$this->user->getById(123);
```

Plugin Translations
-------------------

Plugin can be translated in the same way as the rest of the application. You must load the translations yourself when the session is created:

```php
public function onStartup()
{
    Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
}
```

The translations must be stored in the file `plugins/Myplugin/Locale/xx_XX/translations.php` (replace xx_XX by the language code fr_FR, en_US...).

Translations are stored in a dictionary, if you would like to override an existing string, you just need to use the same key in your translation file.

Dependency Injection Container
------------------------------

Kanboard uses Pimple, a simple PHP Dependency Injection Container. However, Kanboard can register any class in the container easily.

Those classes are available everywhere in the application and only one instance is created.

Here an example to register your own models in the container:

```php
public function getClasses()
{
    return array(
        'Plugin\Budget\Model' => array(
            'HourlyRateModel',
            'BudgetModel',
        )
    );
}
```

Now, if you use a class that extends from `Core\Base`, you can access directly to those class instance:

```php
$this->hourlyRateModel->remove(123);
$this->budgetModel->getDailyBudgetBreakdown(456);

// It's the same thing as using the container:
$this->container['hourlyRateModel']->getAll();
```

Keys of the containers are unique across the application. If you override an existing class, you will change the default behavior.

Add new API methods
-------------------

Kanboard use this library [JSON-RPC](https://github.com/fguillot/JsonRPC) to handle API calls.

To add a new method you can do something like that from your plugin:

```php
$this->api->getProcedureHandler()->withCallback('my_method', function() {
    return 'foobar';
});
```

`$this->container['api']` or `$this->api` expose an instance of the object `JsonRPC\Server`.

Read the library documentation for more information.

Add new console commands
------------------------

Kanboard use the library [Symfony Console](http://symfony.com/doc/current/components/console/introduction.html) to handle local command lines.

Kanboard expose an instance of the object `Symfony\Component\Console\Application` via `$this->cli`. 
You can add new commands from your plugin:

```php
$this->cli->add(new MyCommand());
```

Read the library documentation for more information.

Add new task filters
--------------------

Since the task lexer is a factory that returns a new instance each time, 
you have to extend the `taskLexer` container with the method `extend()` of Pimple.

Here is a example:

```php
public function initialize()
{
    $this->container->extend('taskLexer', function($taskLexer, $c) {
        $taskLexer->withFilter(TaskBoardDateFilter::getInstance($c)->setDateParser($c['dateParser']));
        return $taskLexer;
    });
}
```

For the filter class implementation, there are several examples in the source code under the namespace `Kanboard\Filter`.
