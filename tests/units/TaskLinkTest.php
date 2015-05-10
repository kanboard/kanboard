<?php

require_once __DIR__.'/Base.php';

use Model\Link;
use Model\TaskLink;
use Model\TaskCreation;
use Model\Project;

class TaskLinkTest extends Base
{
    public function testCreateTaskLinkWithNoOpposite()
    {
        $tl = new TaskLink($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertEquals(1, $tl->create(1, 2, 1));

        $links = $tl->getAll(1);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('relates to', $links[0]['label']);
        $this->assertEquals('B', $links[0]['title']);
        $this->assertEquals(2, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);

        $links = $tl->getAll(2);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('relates to', $links[0]['label']);
        $this->assertEquals('A', $links[0]['title']);
        $this->assertEquals(1, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);

        $task_link = $tl->getById(1);
        $this->assertNotEmpty($task_link);
        $this->assertEquals(1, $task_link['id']);
        $this->assertEquals(1, $task_link['task_id']);
        $this->assertEquals(2, $task_link['opposite_task_id']);
        $this->assertEquals(1, $task_link['link_id']);

        $opposite_task_link = $tl->getOppositeTaskLink($task_link);
        $this->assertNotEmpty($opposite_task_link);
        $this->assertEquals(2, $opposite_task_link['id']);
        $this->assertEquals(2, $opposite_task_link['task_id']);
        $this->assertEquals(1, $opposite_task_link['opposite_task_id']);
        $this->assertEquals(1, $opposite_task_link['link_id']);
    }

    public function testCreateTaskLinkWithOpposite()
    {
        $tl = new TaskLink($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertEquals(1, $tl->create(1, 2, 2));

        $links = $tl->getAll(1);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('blocks', $links[0]['label']);
        $this->assertEquals('B', $links[0]['title']);
        $this->assertEquals(2, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);

        $links = $tl->getAll(2);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('is blocked by', $links[0]['label']);
        $this->assertEquals('A', $links[0]['title']);
        $this->assertEquals(1, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);

        $task_link = $tl->getById(1);
        $this->assertNotEmpty($task_link);
        $this->assertEquals(1, $task_link['id']);
        $this->assertEquals(1, $task_link['task_id']);
        $this->assertEquals(2, $task_link['opposite_task_id']);
        $this->assertEquals(2, $task_link['link_id']);

        $opposite_task_link = $tl->getOppositeTaskLink($task_link);
        $this->assertNotEmpty($opposite_task_link);
        $this->assertEquals(2, $opposite_task_link['id']);
        $this->assertEquals(2, $opposite_task_link['task_id']);
        $this->assertEquals(1, $opposite_task_link['opposite_task_id']);
        $this->assertEquals(3, $opposite_task_link['link_id']);
    }

    public function testUpdate()
    {
        $tl = new TaskLink($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $tc->create(array('project_id' => 2, 'title' => 'B')));
        $this->assertEquals(3, $tc->create(array('project_id' => 1, 'title' => 'C')));

        $this->assertEquals(1, $tl->create(1, 2, 5));
        $this->assertTrue($tl->update(1, 1, 3, 11));

        $links = $tl->getAll(1);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('is fixed by', $links[0]['label']);
        $this->assertEquals('C', $links[0]['title']);
        $this->assertEquals(3, $links[0]['task_id']);

        $links = $tl->getAll(2);
        $this->assertEmpty($links);

        $links = $tl->getAll(3);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('fixes', $links[0]['label']);
        $this->assertEquals('A', $links[0]['title']);
        $this->assertEquals(1, $links[0]['task_id']);
    }

    public function testRemove()
    {
        $tl = new TaskLink($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertEquals(1, $tl->create(1, 2, 2));

        $links = $tl->getAll(1);
        $this->assertNotEmpty($links);
        $links = $tl->getAll(2);
        $this->assertNotEmpty($links);

        $this->assertTrue($tl->remove($links[0]['id']));

        $links = $tl->getAll(1);
        $this->assertEmpty($links);
        $links = $tl->getAll(2);
        $this->assertEmpty($links);
    }

    public function testValidation()
    {
        $tl = new TaskLink($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'B')));

        $links = $tl->getAll(1);
        $this->assertEmpty($links);

        $links = $tl->getAll(2);
        $this->assertEmpty($links);

        // Check validation
        $r = $tl->validateCreation(array('task_id' => 1, 'link_id' => 1, 'opposite_task_id' => 2));
        $this->assertTrue($r[0]);

        $r = $tl->validateCreation(array('task_id' => 1, 'link_id' => 1));
        $this->assertFalse($r[0]);

        $r = $tl->validateCreation(array('task_id' => 1, 'opposite_task_id' => 2));
        $this->assertFalse($r[0]);

        $r = $tl->validateCreation(array('task_id' => 1, 'opposite_task_id' => 2));
        $this->assertFalse($r[0]);

        $r = $tl->validateCreation(array('task_id' => 1, 'link_id' => 1, 'opposite_task_id' => 1));
        $this->assertFalse($r[0]);
    }
}
