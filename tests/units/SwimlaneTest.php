<?php

require_once __DIR__.'/Base.php';

use Model\Project;
use Model\Task;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Swimlane;

class SwimlaneTest extends Base
{
    public function testCreation()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(1, 'Swimlane #1'));

        $swimlanes = $s->getSwimlanes(1);
        $this->assertNotEmpty($swimlanes);
        $this->assertEquals(2, count($swimlanes));
        $this->assertEquals('Default swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Swimlane #1', $swimlanes[1]['name']);

        $this->assertEquals(1, $s->getIdByName(1, 'Swimlane #1'));
        $this->assertEquals(0, $s->getIdByName(2, 'Swimlane #2'));

        $this->assertEquals('Swimlane #1', $s->getNameById(1));
        $this->assertEquals('', $s->getNameById(23));
    }

    public function testGetList()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(1, 'Swimlane #1'));
        $this->assertEquals(2, $s->create(1, 'Swimlane #2'));

        $swimlanes = $s->getSwimlanesList(1);
        $expected = array('Default swimlane', 'Swimlane #1', 'Swimlane #2');

        $this->assertEquals($expected, $swimlanes);
    }

    public function testRename()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(1, 'Swimlane #1'));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals('Swimlane #1', $swimlane['name']);

        $this->assertTrue($s->rename(1, 'foobar'));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals('foobar', $swimlane['name']);
    }

    public function testRenameDefaultSwimlane()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($s->updateDefault(array('id' => 1, 'default_swimlane' => 'foo', 'show_default_swimlane' => 1)));

        $default = $s->getDefault(1);
        $this->assertNotEmpty($default);
        $this->assertEquals('foo', $default['default_swimlane']);
        $this->assertEquals(1, $default['show_default_swimlane']);

        $this->assertTrue($s->updateDefault(array('id' => 1, 'default_swimlane' => 'foo', 'show_default_swimlane' => 0)));

        $default = $s->getDefault(1);
        $this->assertNotEmpty($default);
        $this->assertEquals('foo', $default['default_swimlane']);
        $this->assertEquals(0, $default['show_default_swimlane']);
    }

    public function testDisable()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(1, 'Swimlane #1'));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $this->assertEquals(2, $s->getLastPosition(1));
        $this->assertTrue($s->disable(1, 1));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $this->assertEquals(1, $s->getLastPosition(1));

        // Create a new swimlane
        $this->assertEquals(2, $s->create(1, 'Swimlane #2'));

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        // Enable our disabled swimlane
        $this->assertTrue($s->enable(1, 1));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);
    }

    public function testRemove()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(1, 'Swimlane #1'));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'swimlane_id' => 1)));

        $task = $tf->getbyId(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['swimlane_id']);

        $this->assertTrue($s->remove(1, 1));

        $task = $tf->getbyId(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['swimlane_id']);

        $this->assertEmpty($s->getById(1));
    }

    public function testUpdatePositions()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(1, 'Swimlane #1'));
        $this->assertEquals(2, $s->create(1, 'Swimlane #2'));
        $this->assertEquals(3, $s->create(1, 'Swimlane #3'));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(3, $swimlane['position']);

        // Disable the 2nd swimlane
        $this->assertTrue($s->disable(1, 2));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        // Remove the first swimlane
        $this->assertTrue($s->remove(1, 1));

        $swimlane = $s->getById(1);
        $this->assertEmpty($swimlane);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);
    }

    public function testMoveUp()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(1, 'Swimlane #1'));
        $this->assertEquals(2, $s->create(1, 'Swimlane #2'));
        $this->assertEquals(3, $s->create(1, 'Swimlane #3'));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(3, $swimlane['position']);

        // Move the swimlane 3 up
        $this->assertTrue($s->moveUp(1, 3));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(3, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        // First swimlane can be moved up
        $this->assertFalse($s->moveUp(1, 1));

        // Move with a disabled swimlane
        $this->assertTrue($s->disable(1, 1));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        // Move the 2nd swimlane up
        $this->assertTrue($s->moveUp(1, 2));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);
    }

    public function testMoveDown()
    {
        $p = new Project($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $s->create(1, 'Swimlane #1'));
        $this->assertEquals(2, $s->create(1, 'Swimlane #2'));
        $this->assertEquals(3, $s->create(1, 'Swimlane #3'));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(3, $swimlane['position']);

        // Move the swimlane 1 down
        $this->assertTrue($s->moveDown(1, 1));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(3, $swimlane['position']);

        // Last swimlane can be moved down
        $this->assertFalse($s->moveDown(1, 3));

        // Move with a disabled swimlane
        $this->assertTrue($s->disable(1, 3));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);

        // Move the 2st swimlane down
        $this->assertTrue($s->moveDown(1, 2));

        $swimlane = $s->getById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(1, $swimlane['position']);

        $swimlane = $s->getById(2);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
        $this->assertEquals(2, $swimlane['position']);

        $swimlane = $s->getById(3);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
        $this->assertEquals(0, $swimlane['position']);
    }
}
