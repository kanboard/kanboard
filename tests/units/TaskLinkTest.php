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
        $this->assertTrue($tl->create(1, 2, 1));

        $links = $tl->getAll(1);
        $this->assertNotEmpty($links['relates to']);
        $this->assertCount(1, $links);
        $this->assertCount(1, $links['relates to']);
        $this->assertEquals('relates to', $links['relates to'][0]['label']);
        $this->assertEquals('B', $links['relates to'][0]['title']);
        $this->assertEquals(2, $links['relates to'][0]['task_id']);
        $this->assertEquals(1, $links['relates to'][0]['is_active']);

        $links = $tl->getAll(2);
        $this->assertNotEmpty($links);
        $this->assertNotEmpty($links['relates to']);
        $this->assertCount(1, $links);
        $this->assertCount(1, $links['relates to']);
        $this->assertEquals('relates to', $links['relates to'][0]['label']);
        $this->assertEquals('A', $links['relates to'][0]['title']);
        $this->assertEquals(1, $links['relates to'][0]['task_id']);
        $this->assertEquals(1, $links['relates to'][0]['is_active']);
    }

    public function testCreateTaskLinkWithOpposite()
    {
        $tl = new TaskLink($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertTrue($tl->create(1, 2, 2));

        $links = $tl->getAll(1);
        $this->assertNotEmpty($links);
        $this->assertNotEmpty($links['blocks']);
        $this->assertCount(1, $links);
        $this->assertCount(1, $links['blocks']);
        $this->assertEquals('blocks', $links['blocks'][0]['label']);
        $this->assertEquals('B', $links['blocks'][0]['title']);
        $this->assertEquals(2, $links['blocks'][0]['task_id']);
        $this->assertEquals(1, $links['blocks'][0]['is_active']);

        $links = $tl->getAll(2);
        $this->assertNotEmpty($links);
        $this->assertNotEmpty($links['is blocked by']);
        $this->assertCount(1, $links);
        $this->assertCount(1, $links['is blocked by']);
        $this->assertEquals('is blocked by', $links['is blocked by'][0]['label']);
        $this->assertEquals('A', $links['is blocked by'][0]['title']);
        $this->assertEquals(1, $links['is blocked by'][0]['task_id']);
        $this->assertEquals(1, $links['is blocked by'][0]['is_active']);
    }

    public function testRemove()
    {
        $tl = new TaskLink($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertTrue($tl->create(1, 2, 2));

        $links = $tl->getAll(1);
        $this->assertNotEmpty($links);
        $links = $tl->getAll(2);
        $this->assertNotEmpty($links);
        $this->assertNotEmpty($links['is blocked by']);

        $this->assertTrue($tl->remove($links['is blocked by'][0]['id']));

        $links = $tl->getAll(1);
        $this->assertEmpty($links);
        $links = $tl->getAll(2);
        $this->assertEmpty($links);
    }

    public function testValidation()
    {
        // Create tasks
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
