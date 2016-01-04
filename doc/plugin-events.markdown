Using Events
============

Kanboard use internally the [Symfony EventDispatcher component](https://symfony.com/doc/2.3/components/event_dispatcher/index.html) to manage internal events.

Event Listening
---------------

```php
$this->on('app.bootstrap', function($container) {
    // Do something
});
```

- The first argument is the event name (string)
- The second argument is a PHP callable function (closure or class method)

Adding a new event
------------------

To add a new event, you have to call the method `register()` of the class `Kanboard\Core\Event\EventManager`:

```php
$this->eventManager->register('my.event.name', 'My new event description');
```

These events can be used by other components of Kanboard like automatic actions.
