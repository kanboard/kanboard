Uso de eventos
===============

Kanboard usar internamente el [ Componente EventDispatcher de Symfony ](https://symfony.com/doc/2.3/components/event_dispatcher/index.html) para manegar internamente los eventos.

Eventos escucha ** Listening **
-------------------------------

```php
$this->on('app.bootstrap', function($container) {
    // tu codigo
});
```

- El primer argumento es el nombre del evento (string)
- El segundo argumento es una funcion PHP callable (finalización o metodos de la clase)

Agregando un nuevo evento
-------------------------

Para agregar un nuevo, tienes que llamar al metodo `register()` de la clase `Kanboard\Core\Event\EventManager`:

```php
$this->eventManager->register('my.event.name', 'Mi descripcion del nuevo evento');
```

Estos eventos pueden ser utilizados por otros componentes de Kanboard como acciones automáticas .
