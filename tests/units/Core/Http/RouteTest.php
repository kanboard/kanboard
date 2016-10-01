<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Http\Route;
use Kanboard\Core\Http\Router;

class RouteTest extends Base
{
    public function testFindParams()
    {
        $route = new Route($this->container);
        $route->enable();

        $this->assertEquals(array('p1' => true, 'p2' => true), $route->findParams(array('something', ':p1', ':p2')));
        $this->assertEquals(array('p1' => true), $route->findParams(array('something', ':p1', '')));
        $this->assertEquals(array('p1' => true), $route->findParams(array('something', ':p1', 'something else')));
    }

    public function testFindRoute()
    {
        $route = new Route($this->container);
        $route->enable();

        $route->addRoute('/mycontroller/myaction', 'mycontroller', 'myaction');
        $this->assertEquals(
            array('controller' => 'mycontroller', 'action' => 'myaction', 'plugin' => ''),
            $route->findRoute('/mycontroller/myaction')
        );

        $route->addRoute('/a/b/c', 'mycontroller', 'myaction', 'myplugin');
        $this->assertEquals(
            array('controller' => 'mycontroller', 'action' => 'myaction', 'plugin' => 'myplugin'),
            $route->findRoute('/a/b/c')
        );

        $this->assertEquals(
            array('controller' => Router::DEFAULT_CONTROLLER, 'action' => Router::DEFAULT_METHOD, 'plugin' => ''),
            $route->findRoute('/notfound')
        );

        $route->addRoute('/a/b/:c', 'mycontroller', 'myaction', 'myplugin');
        $this->assertEquals(
            array('controller' => 'mycontroller', 'action' => 'myaction', 'plugin' => 'myplugin'),
            $route->findRoute('/a/b/myvalue')
        );

        $this->assertEquals('myvalue', $this->container['request']->getStringParam('c'));

        $route->addRoute('/a/:p1/b/:p2', 'mycontroller', 'myaction');
        $this->assertEquals(
            array('controller' => 'mycontroller', 'action' => 'myaction', 'plugin' => ''),
            $route->findRoute('/a/v1/b/v2')
        );

        $this->assertEquals('v1', $this->container['request']->getStringParam('p1'));
        $this->assertEquals('v2', $this->container['request']->getStringParam('p2'));
    }

    public function testFindUrl()
    {
        $route = new Route($this->container);
        $route->enable();
        $route->addRoute('a/b', 'controller1', 'action1');
        $route->addRoute('a/:myvar1/b/:myvar2', 'controller2', 'action2');
        $route->addRoute('/something', 'controller1', 'action1', 'myplugin');
        $route->addRoute('/myplugin/myroute', 'controller1', 'action2', 'myplugin');
        $route->addRoute('/foo/:myvar', 'controller1', 'action3', 'myplugin');

        $this->assertEquals('a/1/b/2', $route->findUrl('controller2', 'action2', array('myvar1' => 1, 'myvar2' => 2)));
        $this->assertEquals('', $route->findUrl('controller2', 'action2', array('myvar1' => 1)));
        $this->assertEquals('a/b', $route->findUrl('controller1', 'action1'));
        $this->assertEquals('', $route->findUrl('controller1', 'action2'));

        $this->assertEquals('myplugin/myroute', $route->findUrl('controller1', 'action2', array(), 'myplugin'));
        $this->assertEquals('something', $route->findUrl('controller1', 'action1', array(), 'myplugin'));
        $this->assertEquals('foo/123', $route->findUrl('controller1', 'action3', array('myvar' => 123), 'myplugin'));
        $this->assertEquals('foo/123', $route->findUrl('controller1', 'action3', array('myvar' => 123, 'plugin' => 'myplugin')));
    }
}
