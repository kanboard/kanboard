Registering new helpers
=======================

Helper skeleton:

```php
<?php

namespace Kanboard\Plugin\MyPlugin\Helper;

use Kanboard\Core\Base;

class MyHelper extends Base
{
    public function doSomething()
    {
        return 'foobar';
    }
}
```

Register your helper class:

```php
$this->helper->register('myHelper', '\Kanboard\Plugin\MyPlugin\Helper\MyHelper');
```

Using your helper from a template:

```php
<p>
    <?= $this->myHelper->doSomething() ?>
</p>
```

Using your helper from another class:

```php
$this->helper->myHelper->doSomething();
```
