<?php

require_once __DIR__.'/base.php';

use Model\Task;
use Model\Project;

class TaskTest extends Base
{
    public function testDateFormat()
    {
        $t = new Task($this->db, $this->event);

        $this->assertEquals('2014-03-05', date('Y-m-d', $t->getTimestampFromDate('05/03/2014', 'd/m/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $t->getTimestampFromDate('03/05/2014', 'm/d/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $t->getTimestampFromDate('3/5/2014', 'm/d/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $t->getTimestampFromDate('5/3/2014', 'd/m/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $t->getTimestampFromDate('5/3/14', 'd/m/y')));
        $this->assertEquals(0, $t->getTimestampFromDate('5/3/14', 'd/m/Y'));
        $this->assertEquals(0, $t->getTimestampFromDate('5-3-2014', 'd/m/Y'));
    }

    public function testDuplicateToAnotherProject()
    {
        $t = new Task($this->db, $this->event);
        $p = new Project($this->db, $this->event);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a task
        $this->assertEquals(1, $t->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $t->duplicateToAnotherProject(1, 2));
        $this->assertEquals(Task::EVENT_CREATE, $this->event->getLastTriggeredEvent());
    }

    public function testEvents()
    {
        $t = new Task($this->db, $this->event);
        $p = new Project($this->db, $this->event);

        // We create a project
        $this->assertEquals(1, $p->create(array('name' => 'test')));

        // We create task
        $this->assertEquals(1, $t->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(Task::EVENT_CREATE, $this->event->getLastTriggeredEvent());

        // We update a task
        $this->assertTrue($t->update(array('title' => 'test2', 'id' => 1)));
        $this->assertEquals(Task::EVENT_UPDATE, $this->event->getLastTriggeredEvent());

        // We close our task
        $this->assertTrue($t->close(1));
        $this->assertEquals(Task::EVENT_CLOSE, $this->event->getLastTriggeredEvent());

        // We open our task
        $this->assertTrue($t->open(1));
        $this->assertEquals(Task::EVENT_OPEN, $this->event->getLastTriggeredEvent());

        // We change the column of our task
        $this->assertTrue($t->move(1, 2, 1));
        $this->assertEquals(Task::EVENT_MOVE_COLUMN, $this->event->getLastTriggeredEvent());

        // We change the position of our task
        $this->assertTrue($t->move(1, 2, 2));
        $this->assertEquals(Task::EVENT_MOVE_POSITION, $this->event->getLastTriggeredEvent());

        // We change the column and the position of our task
        $this->assertTrue($t->move(1, 1, 3));
        $this->assertEquals(Task::EVENT_MOVE_COLUMN, $this->event->getLastTriggeredEvent());
    }
}
