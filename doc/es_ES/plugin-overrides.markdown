Plugin Reescritura
================

Reesritura HTTP Contenido Política de Seguridad
-----------------------------------------------

Si desea reemplazar el encabezado HTTP predeterminado de la directiva de seguridad del contenido, puede utilizar el método`setContentSecurityPolicy()`:

```php
<?php

namespace Kanboard\Plugin\Csp;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->setContentSecurityPolicy(array('script-src' => 'something'));
    }
}
```

Plantillas de reescrituras 
--------------------------

Las plantillas definidas en el núcleo se pueden anular . Por ejemplo , se puede redefinir el diseño predeterminado o cambiar notificaciones por correo electrónico.

Ejemolo de plantilla de reescritura:

```php
$this->template->setTemplateOverride('header', 'theme:layout/header');
```

El primer argumento es el nombre de la plantilla original y el segundo argumento de la plantilla para usar como reemplazo.

Puede seguir utilizando la plantilla original utilizando el prefijo "kanboard:" :

```php
<?= $this->render('kanboard:header') ?>
```
