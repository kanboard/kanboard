Agregando acciones automáticas
===============================

Agregando una nueva acccion automática es bastante simple.

Creando una nueva accion
------------------------

Tus acciones automáticas deben heradarse de la clase `Kanboard\Action\Base`.

Varios métodos abstractos deben ser implementadas por sí mismo:

| Metodos                             | Descripción                                                      |
|-------------------------------------|------------------------------------------------------------------|
| `getDescription()`                  | Descripcion visible en la interface del usuario                  |
| `getCompatibleEvents()`             | Obtener las listas de eventos compatibles                        |
| `getActionRequiredParameters()`     | Obtener los parametros requeridos para la acción (definidos por el usuario)|
| `getEventRequiredParameters()`      | Obtener los parametros requeridos para el evento                 |
| `doAction(array $data)`             | Ejecutar la acción, Debe regresar true si fue satisfactorio      |
| `hasRequiredCondition(array $data)` | Comprobar si los datos de eventos cumplen la condición de acción |

Tu accion automatica es identificada en kanboard utilizando el nombre de la clase absoluta con el espacio de nombre incluido

Agregando nuevos eventos
------------------------

La lista de eventos de la aplicación está disponible en la clase `Kanboard\Core\Event\EventManager::getAll()`.
Sin embargo, si tu plugin dispara nuevos eventos, tu puedes registrar estos eventos :

```php
$this->actionManager->getAction('\Kanboard\Plugin\MyPlugin\MyActionName')->addEvent('my.event', 'My event description');
```

Tu puedes extender la lista de eventos compatibles de acciones existentes para ser usada en algun metodo.

Registra la acción
----------------------

Tu tienes que llamar el metodo `register()` desde la clase `Kanboard\Core\Action\ActionManager`:

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

Ejemplo
-------

- [Ejemplo de acción automatica](https://github.com/kanboard/plugin-example-automatic-action)
