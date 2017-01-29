Plugin Overrides
================

Override HTTP Content Security Policy
-------------------------------------

If you would like to replace the default HTTP Content Security Policy header, you can use the method `setContentSecurityPolicy()`:

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

Template Overrides
------------------

Any templates defined in the core can be overridden. For example, you can redefine the default layout or change email notifications.

Example of template override:

```php
$this->template->setTemplateOverride('header', 'theme:layout/header');
```

The first argument is the original template name and the second argument the template to use as replacement.

You can still use the original template using the "kanboard:" prefix:

```php
<?= $this->render('kanboard:header') ?>
```

Formatter Overrides
-------------------

Here an example to override formatter objects in Kanboard:

```php
class MyFormatter extends UserAutoCompleteFormatter
{
    public function format()
    {
        $users = parent::format();

        foreach ($users as &$user) {
            $user['label'] = 'something'; // Do something useful here
        }

        return $users;
    }
}

class Plugin extends Base
{
    public function initialize()
    {
        $this->container['userAutoCompleteFormatter'] = $this->container->factory(function ($c) {
            return new MyFormatter($c);
        });
    }
}
```
