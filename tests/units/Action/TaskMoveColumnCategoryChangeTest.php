<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\Category;
use Kanboard\Integration\GithubWebhook;
use Kanboard\Action\TaskMoveColumnCategoryChange;

class TaskMoveColumnCategoryChangeTest extends Base
{
    public function testExecute()
    {
        $action = new TaskMoveColumnCategoryChange($this->container, 1, Task::EVENT_UPDATE);
        $action->setParam('dest_column_id', 3);
        $action->setParam('category_id', 1);

        $this->assertEquals(3, $action->getParam('dest_column_id'));
        $this->assertEquals(1, $action->getParam('category_id'));

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $c = new Category($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $c->create(array('name' => 'bug', 'project_id' => 1)));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));

        // No category should be assigned + column_id=1
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEmpty($task['category_id']);
        $this->assertEquals(1, $task['column_id']);

        // We create an event to move the task to the 2nd column
        $event = array(
            'task_id' => 1,
            'column_id' => 1,
            'project_id' => 1,
            'category_id' => 1,
        );

        // Our event should be executed
        $this->assertTrue($action->hasCompatibleEvent());
        $this->assertTrue($action->hasRequiredProject($event));
        $this->assertTrue($action->hasRequiredParameters($event));
        $this->assertTrue($action->hasRequiredCondition($event));
        $this->assertTrue($action->isExecutable($event));
        $this->assertTrue($action->execute(new GenericEvent($event)));

        // Our task should be moved to the other column
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(3, $task['column_id']);
    }
}
