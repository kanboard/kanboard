<?php

use Kanboard\ServiceProvider\FormatterProvider;
use Pimple\Container;

require_once __DIR__.'/../Base.php';

class FormatterProviderTest extends Base
{
    public function testServiceInstance()
    {
        $container = new Container();
        $serviceProvider = new FormatterProvider($container);
        $serviceProvider->register($container);

        $instance1 = $container['userAutoCompleteFormatter'];
        $instance2 = $container['userAutoCompleteFormatter'];

        $this->assertNotSame($instance1, $instance2);
    }
}
