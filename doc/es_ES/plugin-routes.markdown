Personalizar Rutas
==================

Cuando está habilitada la reescritura de URL, tu puedes definir rutas personalizadas desde tus plugins.

Definir nuevas rutas
--------------------

Las rutas son manejadas por la clase `Kanboard\Core\Http\Route`.

Las nuevas rutas se pueden agregar mediante el uso del método `addRoute($path, $controller, $action, $plugin)`, here an example:

```php
$this->route->addRoute('/my/custom/route', 'myController', 'myAction', 'myplugin');
```

Cuando el usuario final **end-user** van a la URL `/my/custom/route`, el metodo `Kanboard\Plugin\Myplugin\Controller\MyController::myAction()` será ejecutado.

El primer caracter del contraldor y el nombre del plugin serán convertidos en mayusculas con la funcion `ucfirst()`.

Tu puedes ademas definir rutas con variables:

```php
$this->route->addRoute('/my/route/:my_variable', 'myController', 'myAction', 'myplugin');
```

El prefijo colon `:`, define una variable.
Por ejemplo `:my_variable` declare el nombre de la nueva variable `my_variable`.

Para extraer los valores de la variable puedes usar el metodo `getStringParam()` or `getIntegerParam()` desde la clase `Kanboard\Core\Http\Request`:

Si tenemos la URL `/my/route/foobar`, el valor de `my_variable` es `foobar`:

```php
$this->request->getStringParam('my_variable'); // Return foobar
```

Generate links based on the routing table
-----------------------------------------

Desde las plantillas , se tiene que usar el helper `Kanboard\Helper\Url`.

### Generar un link HTML

```php
<?= $this->url->link('My link', 'mycontroller', 'myaction', array('plugin' => 'myplugin')) ?>
```

Generara este HTML:

```html
<a href="/my/custom/route">My link</a>
```

### Generara solamente el atributo `href`:

```php
<?= $this->url->href('My link', 'mycontroller', 'myaction', array('plugin' => 'myplugin')) ?>
```

HTML salida:

```html
/my/custom/route
```

Salida HTML cuando la reescritura del URL no esta habilitada:

```html
?controller=mycontroller&amp;action=myaction&amp;plugin=myplugin
```

### Generar enlace de redirección:

Desde un controlador, si tu necesitas para From a controller, si tu necesitas realizar una redirección:

```php
$this->url->to('mycontroller', 'myaction', array('plugin' => 'myplugin'));
```

Generar:

```
?controller=mycontroller&action=myaction&plugin=myplugin
```
