Plugin Registration[¶](#plugin-registration "Ссылка на этот заголовок")

=======================================================================



Directory structure[¶](#directory-structure "Ссылка на этот заголовок")

-----------------------------------------------------------------------



Plugins are stored in the `plugins`{.docutils .literal} subdirectory. An example of a plugin directory structure:



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



Only the registration file `Plugin.php`{.docutils .literal} is required. Other folders are optional.



The first letter of the plugin name must be capitalized.



Plugin Registration File[¶](#plugin-registration-file "Ссылка на этот заголовок")

---------------------------------------------------------------------------------



Kanboard will scan the directory `plugins`{.docutils .literal} and load automatically everything under this directory. The file `Plugin.php`{.docutils .literal} is used to load and register the plugin.



Example of `Plugin.php`{.docutils .literal} file (`plugins/Foobar/Plugin.php`{.docutils .literal}):



    <?php



    namespace Kanboard\Plugin\Foobar;



    use Kanboard\Core\Plugin\Base;



    class Plugin extends Base

    {

        public function initialize()

        {

            $this->template->hook->attach('template:layout:head', 'theme:layout/head');

        }

    }



This file should contain a class `Plugin`{.docutils .literal} defined under the namespace `Kanboard\Plugin\Yourplugin`{.docutils .literal} and extends `Kanboard\Core\Plugin\Base`{.docutils .literal}.



The only required method is `initialize()`{.docutils .literal}. This method is called for each request when the plugin is loaded.



Plugin Methods[¶](#plugin-methods "Ссылка на этот заголовок")

-------------------------------------------------------------



Available methods from `Kanboard\Core\Plugin\Base`{.docutils .literal}:



-   `initialize()`{.docutils .literal}: Executed when the plugin is loaded

-   `getClasses()`{.docutils .literal}: Return all classes that should be stored in the dependency injection container

-   `on($event, $callback)`{.docutils .literal}: Listen on internal events

-   `getPluginName()`{.docutils .literal}: Should return plugin name

-   `getPluginAuthor()`{.docutils .literal}: Should return plugin author

-   `getPluginVersion()`{.docutils .literal}: Should return plugin version

-   `getPluginDescription()`{.docutils .literal}: Should return plugin description

-   `getPluginHomepage()`{.docutils .literal}: Should return plugin Homepage (link)

-   `setContentSecurityPolicy(array $rules)`{.docutils .literal}: Override default HTTP CSP rules

-   `onStartup()`{.docutils .literal}: If present, this method is executed automatically when the event “app.bootstrap” is triggered



Your plugin registration class can also inherit from Kanboard, that way you can access all classes and methods of Kanboard easily.



This example will fetch the user \#123:



    $this->user->getById(123);



Plugin Translations[¶](#plugin-translations "Ссылка на этот заголовок")

-----------------------------------------------------------------------



Plugin can be translated in the same way as the rest of the application. You must load the translations yourself when the session is created:



    public function onStartup()

    {

        Translator::load($this->language->getCurrentLanguage(), __DIR__.'/Locale');

    }



The translations must be stored in the file `plugins/Myplugin/Locale/xx_XX/translations.php`{.docutils .literal} (replace xx\_XX by the language code fr\_FR, en\_US...).



Translations are stored in a dictionary, if you would like to override an existing string, you just need to use the same key in your translation file.



Dependency Injection Container[¶](#dependency-injection-container "Ссылка на этот заголовок")

---------------------------------------------------------------------------------------------



Kanboard uses Pimple, a simple PHP Dependency Injection Container. However, Kanboard can register any class in the container easily.



Those classes are available everywhere in the application and only one instance is created.



Here an example to register your own models in the container:



    public function getClasses()

    {

        return array(

            'Plugin\Budget\Model' => array(

                'HourlyRateModel',

                'BudgetModel',

            )

        );

    }



Now, if you use a class that extends from `Core\Base`{.docutils .literal}, you can access directly to those class instance:



    $this->hourlyRateModel->remove(123);

    $this->budgetModel->getDailyBudgetBreakdown(456);



    // It's the same thing as using the container:

    $this->container['hourlyRateModel']->getAll();



Keys of the containers are unique across the application. If you override an existing class, you will change the default behavior.



Add new API methods[¶](#add-new-api-methods "Ссылка на этот заголовок")

-----------------------------------------------------------------------



Kanboard use this library [JSON-RPC](https://github.com/fguillot/JsonRPC) to handle API calls.



To add a new method you can do something like that from your plugin:



    $this->api->getProcedureHandler()->withCallback('my_method', function() {

        return 'foobar';

    });



`$this->container['api']`{.docutils .literal} or `$this->api`{.docutils .literal} expose an instance of the object `JsonRPC\Server`{.docutils .literal}.



Read the library documentation for more information.



Add new console commands[¶](#add-new-console-commands "Ссылка на этот заголовок")

---------------------------------------------------------------------------------



Kanboard use the library [Symfony Console](http://symfony.com/doc/current/components/console/introduction.html) to handle local command lines.



Kanboard expose an instance of the object `Symfony\Component\Console\Application`{.docutils .literal} via `$this->cli`{.docutils .literal}. You can add new commands from your plugin:



    $this->cli->add(new MyCommand());



Read the library documentation for more information.



### [Оглавление](index.markdown)



-   [Plugin Registration](#)

    -   [Directory structure](#directory-structure)

    -   [Plugin Registration File](#plugin-registration-file)

    -   [Plugin Methods](#plugin-methods)

    -   [Plugin Translations](#plugin-translations)

    -   [Dependency Injection Container](#dependency-injection-container)

    -   [Add new API methods](#add-new-api-methods)

    -   [Add new console commands](#add-new-console-commands)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/plugin-registration.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/plugin-registration.txt)

