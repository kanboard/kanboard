<?php

require_once __DIR__.'/Base.php';

use Model\Action;
use Model\Project;
use Model\Board;
use Model\Task;
use Model\TaskPosition;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Category;
use Integration\GithubWebhook;

class ActionTest extends Base
{
    public function testSingleAction()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $board = new Board($this->container);
        $project = new Project($this->container);
        $action = new Action($this->container);

        // We create a project
        $this->assertEquals(1, $project->create(array('name' => 'unit_test')));

        // We create a new action
        $this->assertTrue($action->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_MOVE_COLUMN,
            'action_name' => 'TaskClose',
            'params' => array(
                'column_id' => 4,
            )
        )));

        // We create a task
        $this->assertEquals(1, $tc->create(array(
            'title' => 'unit_test',
            'project_id' => 1,
            'owner_id' => 1,
            'color_id' => 'red',
            'column_id' => 1,
        )));

        // We attach events
        $action->attachEvents();

        // Our task should be open
        $t1 = $tf->getById(1);
        $this->assertEquals(1, $t1['is_active']);
        $this->assertEquals(1, $t1['column_id']);

        // We move our task
        $tp->movePosition(1, 1, 4, 1);

        // Our task should be closed
        $t1 = $tf->getById(1);
        $this->assertEquals(4, $t1['column_id']);
        $this->assertEquals(0, $t1['is_active']);
    }

    public function testMultipleActions()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $b = new Board($this->container);
        $p = new Project($this->container);
        $a = new Action($this->container);
        $c = new Category($this->container);
        $g = new GithubWebhook($this->container);

        // We create a project
        $this->assertEquals(1, $p->create(array('name' => 'unit_test')));
        $this->assertEquals(1, $c->create(array('name' => 'unit_test')));

        // We create a new action
        $this->assertTrue($a->create(array(
            'project_id' => 1,
            'event_name' => GithubWebhook::EVENT_ISSUE_OPENED,
            'action_name' => 'TaskCreation',
            'params' => array()
        )));

        $this->assertTrue($a->create(array(
            'project_id' => 1,
            'event_name' => GithubWebhook::EVENT_ISSUE_LABEL_CHANGE,
            'action_name' => 'TaskAssignCategoryLabel',
            'params' => array(
                'label' => 'bug',
                'category_id' => 1,
            )
        )));

        $this->assertTrue($a->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_CREATE_UPDATE,
            'action_name' => 'TaskAssignColorCategory',
            'params' => array(
                'color_id' => 'red',
                'category_id' => 1,
            )
        )));

        // We attach events
        $a->attachEvents();
        $g->setProjectId(1);

        // We create a Github issue
        $issue = array(
            'number' => 123,
            'title' => 'Bugs everywhere',
            'body' => 'There is a bug!',
            'html_url' => 'http://localhost/',
        );

        $this->assertTrue($g->handleIssueOpened($issue));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['is_active']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals('yellow', $task['color_id']);

        // We assign a label to our issue
        $label = array(
            'name' => 'bug',
        );

        $this->assertTrue($g->handleIssueLabeled($issue, $label));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['is_active']);
        $this->assertEquals(1, $task['category_id']);
        $this->assertEquals('red', $task['color_id']);
    }
}
