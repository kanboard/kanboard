Adding Automatic Actions[¶](#adding-automatic-actions "Ссылка на этот заголовок")

=================================================================================



Adding a new automatic action is pretty simple.



Creating a new action[¶](#creating-a-new-action "Ссылка на этот заголовок")

---------------------------------------------------------------------------



Your automatic action must inherit of the class `Kanboard\Action\Base`{.docutils .literal}. Several abstract methods must be implemented by yourself:



  ------------------------------------------------------------------------

  Method                      Description

  --------------------------- --------------------------------------------

  `getDescription()`{.docutil Description visible in the user interface

  s                           

  .literal}                   



  `getCompatibleEvents()`{.do Get the list of compatible events

  cutils                      

  .literal}                   



  `getActionRequiredParameter Get the required parameter for the action

  s()`{.docutils              (defined by the user)

  .literal}                   



  `getEventRequiredParameters Get the required parameter for the event

  ()`{.docutils               

  .literal}                   



  `doAction(array $data)`{.do Execute the action, must return true on

  cutils                      success

  .literal}                   



  `hasRequiredCondition(array Check if the event data meet the action

   $data)`{.docutils          condition

  .literal}                   

  ------------------------------------------------------------------------



Your automatic action is identified in Kanboard by using the absolute class name with the name space included.



Adding new events[¶](#adding-new-events "Ссылка на этот заголовок")

-------------------------------------------------------------------



The list of application events is available in the class `Kanboard\Core\Event\EventManager::getAll()`{.docutils .literal}. However, if your plugin fires new events, you can register these events like that:



    $this->actionManager->getAction('\Kanboard\Plugin\MyPlugin\MyActionName')->addEvent('my.event', 'My event description');



You can extend the list of compatible events of existing actions by using the same method.



Registering the action[¶](#registering-the-action "Ссылка на этот заголовок")

-----------------------------------------------------------------------------



You have to call the method `register()`{.docutils .literal} from the class `Kanboard\Core\Action\ActionManager`{.docutils .literal}:



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



Example[¶](#example "Ссылка на этот заголовок")

-----------------------------------------------



-   [Automatic Action example](https://github.com/kanboard/plugin-example-automatic-action)



### [Оглавление](index.markdown)



-   [Adding Automatic Actions](#)

    -   [Creating a new action](#creating-a-new-action)

    -   [Adding new events](#adding-new-events)

    -   [Registering the action](#registering-the-action)

    -   [Example](#example)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/plugin-automatic-actions.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/plugin-automatic-actions.txt)

