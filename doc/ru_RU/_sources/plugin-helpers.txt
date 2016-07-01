Registering new helpers
=======================

Helper skeleton:

.. code:: php

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

Register your helper class:

.. code:: php

    $this->helper->register('myHelper', '\Kanboard\Plugin\MyPlugin\Helper\MyHelper');

Using your helper from a template:

.. code:: php

    <p>
        <?= $this->myHelper->doSomething() ?>
    </p>

Using your helper from another class:

.. code:: php

    $this->helper->myHelper->doSomething();

