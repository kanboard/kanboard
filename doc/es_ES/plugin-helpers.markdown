Registrar nuevos helpers
=======================

Estrucura del helper:

```php
<?php

namespace Kanboard\Plugin\MyPlugin\Helper\MyHelper;

use Kanboard\Core\Base;

class MyHelper extends Base
{
    public function doSomething()
    {
        return 'foobar';
    }
}
```
Registrar tu helper en la clase:

```php
$this->helper->register('myHelper', '\Kanboard\Plugin\MyPlugin\Helper\MyHelper');
```

Usar tu helper desde una plantilla **Template**:

```php
<p>
    <?= $this->myHelper->doSomething() ?>
</p>
```

Usar tu helper dese otra clase:

```php
$this->helper->myHelper->doSomething();
```
