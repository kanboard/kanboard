Registracion de Plugin 
======================

Estructura del directorio
--------------------------

Los Plugins  estan almacenados en el subdirectorio `plugins. Un ejemplo de estructura del directorio de un plugin:

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

Solamente el archivo de registración `Plugin.php` es requerido. Las otras carpetas son opcionales.

La primera letra del nombre del plugin debe ser capitalizada.

Archivo de registración del plugin
----------------------------------

Kanboard Kanboard escaneará el directorio `plugins` y cargara automaticamente todo en este directorio. el archivo `Plugin.php` se utiliza para cargar y registrar el plugin.

Ejemplo del archivo `Plugin.php` (`plugins/Foobar/Plugin.php`):

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
}
```

Este archivo debera contener una clase `Plugin` se define en el namespace `Kanboard\Plugin\Yourplugin` y extiende `Kanboard\Core\Plugin\Base`.

El único método requerido es  `initialize()`. Este método se llama para cada petición cuando el plugin es cargado.

Metodos del plugin
--------------

Los métodos disponibles son `Kanboard\Core\Plugin\Base`:

- `initialize()`: ejecuta cuando el plugin es cargafo
- `getClasses()`: Regresa todas las clases que se deben almacenar en el contenedor de inyección de dependencias
- `on($event, $callback)`: Escucha eventos internos.
- `getPluginName()`: devuelve el nombre del plugin.
- `getPluginAuthor()`: Devuelve el autor del plugin.
- `getPluginVersion()`: Devuelve la versíon del plugin.
- `getPluginDescription()`: Devuelve la descripcion del plugin.
- `getPluginHomepage()`: Devuelve la pagina inicial del plugin (link)
- `setContentSecurityPolicy(array $rules)`: Reescribe las reglas por default del HTTP CSP.
- `onStartup()`: Si está presente este método, se ejecuta automáticamente cuando el evento "app.bootstrap" se dispara.

Su registro de plugin tambien puede heradar de la clase Kanboard\Core\Base, de esta manera se puede acceder a todas las clases y los métodos de Kanboard facilmente.

En este ejemplo se obtendrá el usuario #123:

```php
$this->user->getById(123);
```

Plugin de traducción 
-------------------

Este plugin se puede traducir en la misma forma que el resto de la aplicación . Debe cargar las traducciones a sí mismo cuando se crea la sesión :

```php
public function onStartup()
{
    Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
}
```

Las traducciones deben ser almacenadas en el archivo `plugins/Myplugin/Locale/xx_XX/translations.php` (remplazar xx_XX por el codigo del lenguaje fr_FR, en_US...).

Las traducciones estan almacenadas en un diccionario, Si desea sobreescribir una cadena existente, sólo tiene que utilizar la misma clave en el archivo de traducción.

Inyección de dependencias de contenedores
------------------------------
Kanboard utiliza la Pimple , un simple inyección de contenedores de dependencia PHP . Sin embargo , Kanboard puede registrar cualquier clase en el contenedor fácilmente.

Estas clases están disponibles en todas partes en la aplicación y se crea una única instancia .

Aquí un ejemplo para registrar sus propios modelos en el contenedor:

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

Ahora bien, si se utiliza una clase que se extiende desde `Core\Base`, tu puedes acceder directamente cualquier instancia de la clase:

```php
$this->hourlyRateModel->remove(123);
$this->budgetModel->getDailyBudgetBreakdown(456);

// It's the same thing as using the container:
$this->container['hourlyRateModel']->getAll();
```

Llaves de los contenedores son únicos a través de la aplicación . Si reemplaza una clase existente , que va a cambiar el comportamiento por defecto .

Agregar un metodo nuevo en la API
---------------------------------

Kanboard usa la libreria [JSON-RPC](https://github.com/fguillot/JsonRPC) para manejar las llamadas de la api.

Para agregar un nuevo metodo tu puedes que puede hacer algo para llamar a tu plugin:

```php
$this->api->getProcedureHandler()->withCallback('my_method', function() {
    return 'foobar';
});
```

`$this->container['api']` or `$this->api` expose an instance of the object `JsonRPC\Server`.

Leer la documentacion de la libreria para mas información.

Agregar una nueva consola de comandos
--------------------------------------

Kanboard usa la libreria [Symfony Console](http://symfony.com/doc/current/components/console/introduction.html) para manejar las lineas de comandos.

Kanboard genera una instancia del objeto `Symfony\Component\Console\Application` via `$this->cli`. 
Tu puedes agregar nuevos comandos desde tu plugin:

```php
$this->cli->add(new MyCommand());
```

Lea la documentación de la biblioteca para obtener más información.
