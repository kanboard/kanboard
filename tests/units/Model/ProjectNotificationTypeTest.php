<?php

namespace KanboardTests\units\Model;

use KanboardTests\units\Base;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectNotificationTypeModel;

class ProjectNotificationTypeTest extends Base
{
    public function testGetTypes()
    {
        $nt = new ProjectNotificationTypeModel($this->container);
        $this->assertEmpty($nt->getTypes());

        $nt->setType('foo', 'Foo', 'Something1');
        $nt->setType('bar', 'Bar', 'Something2');
        $nt->setType('baz', 'Baz', 'Something3', true);

        $this->assertEquals(array('bar' => 'Bar', 'foo' => 'Foo'), $nt->getTypes());
        $this->assertEquals(array('baz'), $nt->getHiddenTypes());
    }

    public function testGetSelectedTypes()
    {
        $nt = new ProjectNotificationTypeModel($this->container);
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        // No type defined
        $this->assertEmpty($nt->getSelectedTypes(1));

        // Hidden type
        $nt->setType('baz', 'Baz', 'Something3', true);
        $this->assertEmpty($nt->getSelectedTypes(1));

        // User defined types but not registered
        $this->assertTrue($nt->saveSelectedTypes(1, array('foo', 'bar')));
        $this->assertEmpty($nt->getSelectedTypes(1));

        // User defined types and registered
        $nt->setType('bar', 'Bar', 'Something4');
        $nt->setType('foo', 'Foo', 'Something3');
        $this->assertEquals(array('bar', 'foo'), $nt->getSelectedTypes(1));
    }
}
