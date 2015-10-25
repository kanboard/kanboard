<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Http\Router;

class RouterTest extends Base
{
    public function testSanitize()
    {
        $r = new Router($this->container);

        $this->assertEquals('PloP', $r->sanitize('PloP', 'default'));
        $this->assertEquals('default', $r->sanitize('', 'default'));
        $this->assertEquals('default', $r->sanitize('123-AB', 'default'));
        $this->assertEquals('default', $r->sanitize('R&D', 'default'));
        $this->assertEquals('Test123', $r->sanitize('Test123', 'default'));
        $this->assertEquals('Test_123', $r->sanitize('Test_123', 'default'));
        $this->assertEquals('userImport', $r->sanitize('userImport', 'default'));
    }

    public function testPath()
    {
        $r = new Router($this->container);

        $this->assertEquals('a/b/c', $r->getPath('/a/b/c'));
        $this->assertEquals('a/b/something', $r->getPath('/a/b/something?test=a', 'test=a'));

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PHP_SELF'] = '/a/index.php';

        $this->assertEquals('b/c', $r->getPath('/a/b/c'));
        $this->assertEquals('b/c', $r->getPath('/a/b/c?e=f', 'e=f'));
    }

    public function testFindRouteWithEmptyTable()
    {
        $r = new Router($this->container);
        $this->assertEquals(array('app', 'index'), $r->findRoute(''));
        $this->assertEquals(array('app', 'index'), $r->findRoute('/'));
    }

    public function testFindRouteWithoutPlaceholders()
    {
        $r = new Router($this->container);
        $r->addRoute('a/b', 'controller', 'action');
        $this->assertEquals(array('app', 'index'), $r->findRoute('a/b/c'));
        $this->assertEquals(array('controller', 'action'), $r->findRoute('a/b'));
    }

    public function testFindRouteWithPlaceholders()
    {
        $r = new Router($this->container);
        $r->addRoute('a/:myvar1/b/:myvar2', 'controller', 'action');
        $this->assertEquals(array('app', 'index'), $r->findRoute('a/123/b'));
        $this->assertEquals(array('controller', 'action'), $r->findRoute('a/456/b/789'));
        $this->assertEquals(array('myvar1' => 456, 'myvar2' => 789), $_GET);
    }

    public function testFindMultipleRoutes()
    {
        $r = new Router($this->container);
        $r->addRoute('a/b', 'controller1', 'action1');
        $r->addRoute('a/b', 'duplicate', 'duplicate');
        $r->addRoute('a', 'controller2', 'action2');
        $this->assertEquals(array('controller1', 'action1'), $r->findRoute('a/b'));
        $this->assertEquals(array('controller2', 'action2'), $r->findRoute('a'));
    }

    public function testFindUrl()
    {
        $r = new Router($this->container);
        $r->addRoute('a/b', 'controller1', 'action1');
        $r->addRoute('a/:myvar1/b/:myvar2', 'controller2', 'action2', array('myvar1', 'myvar2'));

        $this->assertEquals('a/1/b/2', $r->findUrl('controller2', 'action2', array('myvar1' => 1, 'myvar2' => 2)));
        $this->assertEquals('', $r->findUrl('controller2', 'action2', array('myvar1' => 1)));
        $this->assertEquals('a/b', $r->findUrl('controller1', 'action1'));
        $this->assertEquals('', $r->findUrl('controller1', 'action2'));
    }
}
