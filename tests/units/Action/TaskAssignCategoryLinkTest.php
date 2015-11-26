<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\TaskLink;
use Kanboard\Model\Category;
use Kanboard\Event\TaskLinkEvent;
use Kanboard\Action\TaskAssignCategoryLink;

class TaskAssignCategoryLinkTest extends Base
{
    public function testAssignCategory()
    {
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $c = new Category($this->container);

        $action = new TaskAssignCategoryLink($this->container, 1, TaskLink::EVENT_CREATE_UPDATE);
        $action->setParam('category_id', 1);
        $action->setParam('link_id', 2);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));
        $this->assertEquals(1, $c->create(array('name' => 'C1', 'project_id' => 1)));
        $this->assertEquals(1, $tc->create(array('title' => 'T1', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertEquals(0, $task['category_id']);

        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'opposite_task_id' => 2,
            'link_id' => 2,
        );

        $this->assertTrue($action->execute(new TaskLinkEvent($event)));

        $task = $tf->getById(1);
        $this->assertEquals(1, $task['category_id']);
    }

    public function testThatLinkDontMatch()
    {
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $c = new Category($this->container);

        $action = new TaskAssignCategoryLink($this->container, 1, TaskLink::EVENT_CREATE_UPDATE);
        $action->setParam('category_id', 1);
        $action->setParam('link_id', 1);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));
        $this->assertEquals(1, $c->create(array('name' => 'C1', 'project_id' => 1)));
        $this->assertEquals(1, $tc->create(array('title' => 'T1', 'project_id' => 1)));

        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'opposite_task_id' => 2,
            'link_id' => 2,
        );

        $this->assertFalse($action->execute(new TaskLinkEvent($event)));
    }

    public function testThatExistingCategoryWillNotChange()
    {
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $c = new Category($this->container);

        $action = new TaskAssignCategoryLink($this->container, 1, TaskLink::EVENT_CREATE_UPDATE);
        $action->setParam('category_id', 2);
        $action->setParam('link_id', 2);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));
        $this->assertEquals(1, $c->create(array('name' => 'C1', 'project_id' => 1)));
        $this->assertEquals(2, $c->create(array('name' => 'C2', 'project_id' => 1)));
        $this->assertEquals(1, $tc->create(array('title' => 'T1', 'project_id' => 1, 'category_id' => 1)));

        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'opposite_task_id' => 2,
            'link_id' => 2,
        );

        $this->assertFalse($action->execute(new TaskLinkEvent($event)));
    }
}
