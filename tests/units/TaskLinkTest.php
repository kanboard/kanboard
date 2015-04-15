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

        $links = $tl->getLinks(1);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('relates to', $links[0]['label']);
        $this->assertEquals('B', $links[0]['title']);
        $this->assertEquals(2, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);

        $links = $tl->getLinks(2);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('relates to', $links[0]['label']);
        $this->assertEquals('A', $links[0]['title']);
        $this->assertEquals(1, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);
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

        $links = $tl->getLinks(1);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('blocks', $links[0]['label']);
        $this->assertEquals('B', $links[0]['title']);
        $this->assertEquals(2, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);

        $links = $tl->getLinks(2);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('is blocked by', $links[0]['label']);
        $this->assertEquals('A', $links[0]['title']);
        $this->assertEquals(1, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);
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

        $links = $tl->getLinks(1);
        $this->assertNotEmpty($links);
        $links = $tl->getLinks(2);
        $this->assertNotEmpty($links);

        $this->assertTrue($tl->remove($links[0]['id']));

        $links = $tl->getLinks(1);
        $this->assertEmpty($links);
        $links = $tl->getLinks(2);
        $this->assertEmpty($links);
    }

    public function testValidation()
    {
        $tl = new TaskLink($this->container);

        $r = $tl->validateCreation(array('task_id' => 1, 'link_id' => 1, 'title' => 'a'));
        $this->assertTrue($r[0]);

        $r = $tl->validateCreation(array('task_id' => 1, 'link_id' => 1));
        $this->assertFalse($r[0]);

        $r = $tl->validateCreation(array('task_id' => 1, 'title' => 'a'));
        $this->assertFalse($r[0]);

        $r = $tl->validateCreation(array('link_id' => 1, 'title' => 'a'));
        $this->assertFalse($r[0]);
    }
}
