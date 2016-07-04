Using Events[¶](#using-events "Ссылка на этот заголовок")

=========================================================



Kanboard use internally the [Symfony EventDispatcher component](https://symfony.com/doc/2.3/components/event_dispatcher/index.html) to manage internal events.



Event Listening[¶](#event-listening "Ссылка на этот заголовок")

---------------------------------------------------------------



    $this->on('app.bootstrap', function($container) {

        // Do something

    });



-   The first argument is the event name (string)

-   The second argument is a PHP callable function (closure or class method)



Adding a new event[¶](#adding-a-new-event "Ссылка на этот заголовок")

---------------------------------------------------------------------



To add a new event, you have to call the method `register()`{.docutils .literal} of the class `Kanboard\Core\Event\EventManager`{.docutils .literal}:



    $this->eventManager->register('my.event.name', 'My new event description');



These events can be used by other components of Kanboard like automatic actions.



### [Оглавление](index.markdown)



-   [Using Events](#)

    -   [Event Listening](#event-listening)

    -   [Adding a new event](#adding-a-new-event)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

