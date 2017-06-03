Vinculación externa de proveedores
==================================

Esta funcionalidad le permite vincular una tarea a los elementos adicionales almacenados en otro sistema.

Por ejemplo, tu puedes vincular  una tarea a:
For example, you can link a task to:

- Una pagina web tradicional
- Datos adjuntos (Documentos PDF almacenados en la web, archivos ...) 
- Cualquier sitema de tickets (bug tracker, soporte personalizado de tickets...)

Cada item tiene un tipo, u na URL, un tipo dedependencia y un titulo.

Por default,Kanboard incluye dos tipos de proveedores

- Vinculación Web : Copiar y pegar un enlace y Kanboard se ha podido ir a la página de título de forma automática.
- Datos adjuntos: Enlace a todo lo que no es una página web

Flujo de trabajo ** Workflow **
--------------------------------

1. El usuario final copia y pega el URL al submit del formulario.
2.- Si el tipo de link es "auto", Kanboard crea un bucle a través de todos los proveedores registrados hasta que hay una coincidencia 
3. Entonces , el proveedor de enlace devuelve un objeto que implementa la interfaz de `ExternalLinkInterface`
4. El formulario se muestra al usuario con todos los datos precargados antes de guardar el enlace

Interfaces
----------

Para implementar un nuevo vinculo al proveedor desde un plugin, es necesario crear 2 clases que implementarlas en las interfaces:

- `Kanboard\Core\ExternalLink\ExternalLinkProviderInterface`
- `Kanboard\Core\ExternalLink\ExternalLinkInterface`

### ExternalLinkProviderInterface

| Method                     | Usage                                                                           |
|----------------------------|---------------------------------------------------------------------------------|
| `getName()`                | Obtener el nombre del proveedor(label)                                          |
| `getType()`                | Obtener el tipo de link (se guardarán en la base de datos)                      |
| `getDependencies()`        | Obtener un diccionario de los tipos de dependencia soportados por el proveedor  |
| `setUserTextInput($input)` | Texto introducido por el usuario                                                |
| `match()`                  | Regresa true si el proveedor puede analizar correctamente la entrada del usuario|
| `getLink()`                | Obtener el enlace que se encuentra con las propiedades                          |

### ExternalLinkInterface

| Method            | Usage                       |
|-------------------|-----------------------------|
| `getTitle()`      | Obtiene un titulo de link   |
| `getUrl()`        | Obtiene una link de URL     |
| `setUrl($url)`    | URL del enlace              |

El registro de un nuevo proveedor de enlace
------------------------------------------

En tu `Plugin.php`, solo puedes llamar al metodo `register()` desde el objeto `ExternalLinkManager`:

```php
<?php

namespace Kanboard\Plugin\MyExternalLink;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->externalLinkManager->register(new MyLinkProvider());
    }
}
```

Ejemplos
--------

- Kanboard incluye proveedores por default "WebLink" y "Attachment"
