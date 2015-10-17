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

Any templates defined in the core can be overrided. By example, you can redefine the default layout or change email notifications.

Example of template override:

```php
$this->template->setTemplateOverride('header', 'theme:layout/header');
```

The first argument is the original template name and the second argument the template to use as replacement.
