<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\TaskLinkValidator;
use Kanboard\Model\TaskLink;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Project;

class TaskLinkValidatorTest extends Base
{
    public function testValidateCreation()
    {
        $validator = new TaskLinkValidator($this->container);
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

        // Check creation
        $r = $validator->validateCreation(array('task_id' => 1, 'link_id' => 1, 'opposite_task_id' => 2));
        $this->assertTrue($r[0]);

        $r = $validator->validateCreation(array('task_id' => 1, 'link_id' => 1));
        $this->assertFalse($r[0]);

        $r = $validator->validateCreation(array('task_id' => 1, 'opposite_task_id' => 2));
        $this->assertFalse($r[0]);

        $r = $validator->validateCreation(array('task_id' => 1, 'opposite_task_id' => 2));
        $this->assertFalse($r[0]);

        $r = $validator->validateCreation(array('task_id' => 1, 'link_id' => 1, 'opposite_task_id' => 1));
        $this->assertFalse($r[0]);
    }

    public function testValidateModification()
    {
        $validator = new TaskLinkValidator($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'B')));

        // Check modification
        $r = $validator->validateModification(array('id' => 1, 'task_id' => 1, 'link_id' => 1, 'opposite_task_id' => 2));
        $this->assertTrue($r[0]);

        $r = $validator->validateModification(array('id' => 1, 'task_id' => 1, 'link_id' => 1));
        $this->assertFalse($r[0]);

        $r = $validator->validateModification(array('id' => 1, 'task_id' => 1, 'opposite_task_id' => 2));
        $this->assertFalse($r[0]);

        $r = $validator->validateModification(array('id' => 1, 'task_id' => 1, 'opposite_task_id' => 2));
        $this->assertFalse($r[0]);

        $r = $validator->validateModification(array('id' => 1, 'task_id' => 1, 'link_id' => 1, 'opposite_task_id' => 1));
        $this->assertFalse($r[0]);
    }
}
