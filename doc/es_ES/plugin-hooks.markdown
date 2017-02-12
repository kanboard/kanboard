Plugin Hooks
============

Hooks de aplicación
-----------------

Los hooks pueden extender , reemplazar, filtar datos o cambiar los comportamientos por default, cad hook es identificado con unico nombre, ejemplo : `controller:calendar:user:events`

### Escuchar en eventos hook 

En tu  metodo `initialize()` tu necesitas llamar al metodo `on()` de la clase `Kanboard\Core\Plugin\Hook`:

```php
$this->hook->on('hook_name', $callable);
```

El primer argumento es el nombre del hook y el segundo es un callable PHP

### Hooks ejecutado sólo una vez

Algunos hooks solo tienen un  listener:

#### model:subtask-time-tracking:calculate:time-spent

- Anular el cálculo del tiempo anterior cuando se detiene el temporizador de la subtarea
- Argumentos :
    - `$user_id` (integer)
    - `$start` (DateTime)
    - `$end` (DateTime)

### Fusionar hooks

"Fusionar hooks" actuar de la misma manera que la función de `array_merge`. el hook callback solo regresa un array. Este array se fusionará con la que viene por defecto.

Ejemplo para agregar eventos en el calendario del usuario:

```php
class Plugin extends Base
{
    public function initialize()
    {
        $container = $this->container;

        $this->hook->on('controller:calendar:user:events', function($user_id, $start, $end) use ($container) {
            $model = new SubtaskForecast($container);
            return $model->getCalendarEvents($user_id, $end); // Return new events
        });
    }
}
```

Ejemplo para sobrescribir los valores por default de las tareas:

```php
class Plugin extends Base
{
    public function initialize()
    {
        $this->hook->on('controller:task:form:default', function (array $default_values) {
            return empty($default_values['score']) ? array('score' => 4) : array();
        });
    }
}
```

Lista de fusión de hooks:

#### controller:task:form:default

- sobrescribir los valores por default de las tareas
- Argumentos:
    - `$default_values`: actual default values (array)

#### controller:calendar:project:events

- Agregar mas eventos a el calendario del projecto
- Argumentos:
    - `$project_id` (integer)
    - `$start` Calendar start date (string, ISO-8601 format)
    - `$end` Calendar` end date (string, ISO-8601 format)

#### controller:calendar:user:events

- Agregar mas eventos a el calendario de usuario
- Argumentos:
    - `$user_id` (integer)
    - `$start` Calendar start date (string, ISO-8601 format)
    - `$end` Calendar end date (string, ISO-8601 format)

Asset Hooks
-----------

Asset hooks pueden ser usados para agregar nuevas stylesheet facilmente o un nuevo archivo JavaScript en el layout. tu puedes usar estas caracteristicas para crear un tema y sobreescribir todo los estilos por dafault del kanboard.

Ejemplo para agregar un stylesheet **hoja de estilo**

```php
<?php

namespace Kanboard\Plugin\Css;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->hook->on('template:layout:css', array('template' => 'plugins/Css/skin.css'));
    }
}
```

Lista de asset Hooks:

- `template:layout:css`
- `template:layout:js`


Referencia de hooks
---------------

La reference hooks son pasados a una variable por referencia.

Ejemplo:

```php
$this->hook->on('formatter:board:query', function (\PicoDb\Table &query) {
    $query->eq('color_id', 'red');
});
```

El código anterior mostrará sólo las tareas en rojo en el tablero.

Lista de referencias de hooks:

| Hook                                       | Description                                                   |
|--------------------------------------------|---------------------------------------------------------------|
| `formatter:board:query`                    | Alterar consulta a la base antes de tablero de renderizado    |
| `pagination:dashboard:task:query`          | Alterar consulta de base de datos para las tareas de la paginación en el dashboard  |
| `pagination:dashboard:subtask:query`       | Alterar consulta a la base de la paginación en el subtareas del dashboard           |
| `model:task:creation:prepare`              | Alterar los valores del formulario antes de guardar una tarea                       |
| `model:task:modification:prepare`          | Alterar los valores del formulario antes de editar una tarea

Template Hooks
--------------

Template hooks permitirá añadir nuevos contenidos en las plantillas existentes.

Ejemplo para agregar nuevos contenidos en el sidebar del dashboard:

```php
$this->template->hook->attach('template:dashboard:sidebar', 'myplugin:dashboard/sidebar');
```

Ejemplo para adjuntar un template con variables locales:

```php
$this->template->hook->attach('template:dashboard:sidebar', 'myplugin:dashboard/sidebar', array(
    'variable' => 'foobar',
));
```

Ejemplo para adjuntar un template con un callable:

```php
$this->template->hook->attach('template:dashboard:sidebar', 'myplugin:dashboard/sidebar', function($hook_param1, $hook_param2) {
    return array('new_template_variable' => 'foobar'); // Inyectar una nueva variable en la plantilla Plugin
});
```

Este llamada es usualmente definida en el metodo `initialize()`
El primer argumento es el nombre del hook y el segundo argumento es el nombre del template.

Los nombres de plantillas con el prefijo del nombre del plugin y los dos puntos indican la ubicación de la plantilla.

Ejemplo con `myplugin:dashboard/sidebar`:

- `myplugin` Es el nombre de tu plugin (minusculas)
- `dashboard/sidebar` es el nombre del template
- En el filesystem, El plugin esta localizado aqui: `plugins\Myplugin\Template\dashboard\sidebar.php`
- Templates estan escritos en puro PHP (no se olvide de escapar los datos)

Los nombres de plantillas sin prefijo son plantillas básicas.

Lista de templates hooks:

| Hook                                       | Descripción                                      |
|--------------------------------------------|----------------------------------------------------|
| `template:analytic:sidebar`                | Sidebar en paginas analiticas                      |
| `template:app:filters-helper:before`       | Filtro helper dropdown (top)                       |
| `template:app:filters-helper:after`        | Filtro helper dropdown (bottom)                    |
| `template:auth:login-form:before`          | Pagina de login  (top)                             |
| `template:auth:login-form:after`           | Pagina de login (bottom)                           |
| `template:board:private:task:before-title` | Tarea en un tablero privado : despues del titulo   |
| `template:board:private:task:after-title`  | Tarea en un tablero privado : antes del titulo     |
| `template:board:public:task:before-title`  | Tarea en un tablero publico : despues del titulo   |
| `template:board:public:task:after-title`   | Tarea en un tablero publico : antes del titulo     |
| `template:board:task:footer`               | Tarea en el tablero : footer                       |
| `template:board:task:icons`                | Tarea en el tablero: tooltip icon                  |
| `template:board:column:dropdown`           | Menu Dropdown en las columnas del tablero          |
| `template:config:sidebar`                  | Sidebar pagina de configuración                    |
| `template:config:application `             | Aplicación de configuración del formulario         |
| `template:config:email`                    | Email settings page                                |
| `template:config:integrations`             | Integration page in global settings                |
| `template:dashboard:sidebar`               | Sidebar en la pagina del dashboard                 |
| `template:dashboard:show`                  | Pagina principal del dashboard                     |
| `template:export:sidebar`                  | Sidebar para exportar paginas                      |
| `template:import:sidebar`                  | Sidebar para importar paginas                      |
| `template:header:dropdown`                 | Dropdown menu Pagina header (icono avatar de usuario) |
| `template:header:creation-dropdown`        | Dropdown menu Pagina header (plus icon)            |
| `template:layout:head`                     | Layout de la pagina  `<head/>` tag                 |
| `template:layout:top`                      | Layout de la pagina top header                     |
| `template:layout:bottom`                   | Layout de la pagina footer                                 |
| `template:project:dropdown`                | "Actions" menu a la izquierda vista para diferentes proyectos |
| `template:project:header:before`           | Filtros de Proyectos  (antes)                           |
| `template:project:header:after`            | Filtros de Proyectos (despues)                     |
| `template:project:integrations`            | Pagina de integracion de configuración de proyectos|
| `template:project:sidebar`                 | Sidebar configuración de proyectos                 |
| `template:project-user:sidebar`            | Sidebar en la página de la información de usuario del proyecto            |
| `template:task:layout:top`                 | Task layout top (antes page header)                |
| `template:task:details:top`                | Resumen de tarea top                               |
| `template:task:details:bottom`             | Resumen de tarea bottom                            |
| `template:task:details:first-column`       | Resumen de tarea columna                           |
| `template:task:details:second-column`      | Resumen de tarea dos columnas                      |
| `template:task:details:third-column`       | Resumen de tarea tres columnas                     |
| `template:task:details:fourth-column`      | Resumen de tarea cuatro columnas                   |
| `template:task:dropdown`                   | Task dropdown menu en listado de paginas           |
| `template:task:sidebar:actions`            | Sidebar on task page (sección de acciones)         |
| `template:task:sidebar:information`        | Sidebar on task page (sección de información)         |
| `template:task:form:first-column`          | 1st columna en forma de tarea                           |
| `template:task:form:second-column`         | 2nd columna en forma de tarea                            |
| `template:task:form:third-column`          | 3nd columna en forma de tarea                            |
| `template:task:show:top   `                | Mostrar página de tareas : top                                |
| `template:task:show:bottom`                | Mostrar página de tareas : bottom                             |
| `template:task:show:before-description`    | Mostrar página de tareas : despues de la descripción            |
| `template:task:show:before-tasklinks`      | Mostrar página de tareas : despues tasklinks                   |
| `template:task:show:before-subtasks`       | Mostrar página de tareas : despues subtareas                   |
| `template:task:show:before-timetracking`   | Mostrar página de tareas : despues timetracking                |
| `template:task:show:before-attachments`    | Mostrar página de tareas : despues archivos adjuntos                 |
| `template:task:show:before-comments`       | Mostrar página de tareas : despues comentarios                  |
| `template:user:authentication:form`        | "Editar autenticación" formulario de perfil de usuario  |
| `template:user:create-remote:form`         | "Crear un usuario remoto" formulario                         |
| `template:user:external`                   | "Autenticación externa" la página de perfil de usuario   |
| `template:user:integrations`               | Integración en el perfil de usuario                  |
| `template:user:sidebar:actions`            | Sidebar en el perfil de usuario (sección de acciones)          |
| `template:user:sidebar:information`        | Sidebar en el perfil de usuario (sección de información)      |


Otro de los ganchos plantilla se pueden añadir en caso necesario , sólo hay que preguntar en el seguimiento de incidencias  ** issue tracking **. 
