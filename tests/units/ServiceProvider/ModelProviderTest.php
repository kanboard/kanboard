<?php

namespace KanboardTests\units\ServiceProvider;

use KanboardTests\units\Base;
use Kanboard\ServiceProvider\ClassProvider;
use Pimple\Container;

class ModelProviderTest extends Base
{
    public function testServiceInstance()
    {
        $container = new Container();
        $serviceProvider = new ClassProvider($container);
        $serviceProvider->register($container);

        $instance1 = $container['userModel'];
        $instance2 = $container['userModel'];

        $this->assertSame($instance1, $instance2);
    }
}
