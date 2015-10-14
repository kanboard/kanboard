<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Action;
use Kanboard\Model\Project;
use Kanboard\Model\Board;
use Kanboard\Model\Task;
use Kanboard\Model\TaskPosition;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Category;
use Kanboard\Model\User;
use Kanboard\Model\ProjectPermission;
use Kanboard\Integration\GithubWebhook;
use Kanboard\Integration\BitbucketWebhook;

class ActionTest extends Base
{
    public function testGetActions()
    {
        $a = new Action($this->container);

        $actions = $a->getAvailableActions();
        $this->assertNotEmpty($actions);
        $this->assertEquals('Add a comment log when moving the task between columns', current($actions));
        $this->assertEquals('TaskLogMoveAnotherColumn', key($actions));
    }

    public function testExtendActions()
    {
        $a = new Action($this->container);
        $a->extendActions('MyClass', 'Description');

        $actions = $a->getAvailableActions();
        $this->assertNotEmpty($actions);
        $this->assertContains('Description', $actions);
        $this->assertArrayHasKey('MyClass', $actions);
    }

    public function testGetEvents()
    {
        $a = new Action($this->container);

        $events = $a->getAvailableEvents();
        $this->assertNotEmpty($events);
        $this->assertEquals('Bitbucket commit received', current($events));
        $this->assertEquals(BitbucketWebhook::EVENT_COMMIT, key($events));
    }

    public function testGetCompatibleEvents()
    {
        $a = new Action($this->container);
        $events = $a->getCompatibleEvents('TaskAssignSpecificUser');

        $this->assertNotEmpty($events);
        $this->assertCount(2, $events);
        $this->assertArrayHasKey(Task::EVENT_CREATE_UPDATE, $events);
        $this->assertArrayHasKey(Task::EVENT_MOVE_COLUMN, $events);
    }

    public function testResolveDuplicatedParameters()
    {
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $a = new Action($this->container);
        $c = new Category($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));
        $this->assertEquals(2, $p->create(array('name' => 'P2')));

        $this->assertEquals(1, $c->create(array('name' => 'C1', 'project_id' => 1)));

        $this->assertEquals(2, $c->create(array('name' => 'C2', 'project_id' => 2)));
        $this->assertEquals(3, $c->create(array('name' => 'C1', 'project_id' => 2)));

        $this->assertEquals(2, $u->create(array('username' => 'unittest1')));
        $this->assertEquals(3, $u->create(array('username' => 'unittest2')));

        $this->assertTrue($pp->addMember(1, 2));
        $this->assertTrue($pp->addMember(1, 3));
        $this->assertTrue($pp->addMember(2, 3));

        // anything
        $this->assertEquals('blah', $a->resolveParameters(array('name' => 'foobar', 'value' => 'blah'), 2));

        // project_id
        $this->assertEquals(2, $a->resolveParameters(array('name' => 'project_id', 'value' => 'blah'), 2));

        // category_id
        $this->assertEquals(3, $a->resolveParameters(array('name' => 'category_id', 'value' => 1), 2));
        $this->assertFalse($a->resolveParameters(array('name' => 'category_id', 'value' => 0), 2));
        $this->assertFalse($a->resolveParameters(array('name' => 'category_id', 'value' => 5), 2));

        // column_id
        $this->assertFalse($a->resolveParameters(array('name' => 'column_id', 'value' => 10), 2));
        $this->assertFalse($a->resolveParameters(array('name' => 'column_id', 'value' => 0), 2));
        $this->assertEquals(5, $a->resolveParameters(array('name' => 'column_id', 'value' => 1), 2));
        $this->assertEquals(6, $a->resolveParameters(array('name' => 'dest_column_id', 'value' => 2), 2));
        $this->assertEquals(7, $a->resolveParameters(array('name' => 'dst_column_id', 'value' => 3), 2));
        $this->assertEquals(8, $a->resolveParameters(array('name' => 'src_column_id', 'value' => 4), 2));

        // user_id
        $this->assertFalse($a->resolveParameters(array('name' => 'user_id', 'value' => 10), 2));
        $this->assertFalse($a->resolveParameters(array('name' => 'user_id', 'value' => 0), 2));
        $this->assertFalse($a->resolveParameters(array('name' => 'user_id', 'value' => 2), 2));
        $this->assertFalse($a->resolveParameters(array('name' => 'owner_id', 'value' => 2), 2));
        $this->assertEquals(3, $a->resolveParameters(array('name' => 'user_id', 'value' => 3), 2));
        $this->assertEquals(3, $a->resolveParameters(array('name' => 'owner_id', 'value' => 3), 2));
    }

    public function testDuplicateSuccess()
    {
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $a = new Action($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));
        $this->assertEquals(2, $p->create(array('name' => 'P2')));

        $this->assertEquals(2, $u->create(array('username' => 'unittest1')));
        $this->assertEquals(3, $u->create(array('username' => 'unittest2')));

        $this->assertTrue($pp->addMember(1, 2));
        $this->assertTrue($pp->addMember(1, 3));
        $this->assertTrue($pp->addMember(2, 3));

        $this->assertEquals(1, $a->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_CREATE_UPDATE,
            'action_name' => 'TaskAssignSpecificUser',
            'params' => array(
                'column_id' => 1,
                'user_id' => 3,
            )
        )));

        $action = $a->getById(1);
        $this->assertNotEmpty($action);
        $this->assertNotEmpty($action['params']);
        $this->assertEquals(1, $action['project_id']);

        $this->assertTrue($a->duplicate(1, 2));

        $action = $a->getById(2);
        $this->assertNotEmpty($action);
        $this->assertNotEmpty($action['params']);
        $this->assertEquals(2, $action['project_id']);
        $this->assertEquals(Task::EVENT_CREATE_UPDATE, $action['event_name']);
        $this->assertEquals('TaskAssignSpecificUser', $action['action_name']);
        $this->assertEquals('column_id', $action['params'][0]['name']);
        $this->assertEquals(5, $action['params'][0]['value']);
        $this->assertEquals('user_id', $action['params'][1]['name']);
        $this->assertEquals(3, $action['params'][1]['value']);
    }

    public function testDuplicateUnableToResolveParams()
    {
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $a = new Action($this->container);
        $u = new User($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));
        $this->assertEquals(2, $p->create(array('name' => 'P2')));

        $this->assertEquals(2, $u->create(array('username' => 'unittest1')));

        $this->assertTrue($pp->addMember(1, 2));

        $this->assertEquals(1, $a->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_CREATE_UPDATE,
            'action_name' => 'TaskAssignSpecificUser',
            'params' => array(
                'column_id' => 1,
                'user_id' => 2,
            )
        )));

        $action = $a->getById(1);
        $this->assertNotEmpty($action);
        $this->assertNotEmpty($action['params']);
        $this->assertEquals(1, $action['project_id']);
        $this->assertEquals('user_id', $action['params'][1]['name']);
        $this->assertEquals(2, $action['params'][1]['value']);

        $this->assertTrue($a->duplicate(1, 2));

        $action = $a->getById(2);
        $this->assertEmpty($action);
    }

    public function testDuplicateMixedResults()
    {
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $a = new Action($this->container);
        $u = new User($this->container);
        $c = new Category($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));
        $this->assertEquals(2, $p->create(array('name' => 'P2')));

        $this->assertEquals(1, $c->create(array('name' => 'C1', 'project_id' => 1)));
        $this->assertEquals(2, $c->create(array('name' => 'C2', 'project_id' => 2)));
        $this->assertEquals(3, $c->create(array('name' => 'C1', 'project_id' => 2)));

        $this->assertEquals(2, $u->create(array('username' => 'unittest1')));

        $this->assertTrue($pp->addMember(1, 2));

        $this->assertEquals(1, $a->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_CREATE_UPDATE,
            'action_name' => 'TaskAssignSpecificUser',
            'params' => array(
                'column_id' => 1,
                'user_id' => 2,
            )
        )));

        $action = $a->getById(1);
        $this->assertNotEmpty($action);
        $this->assertNotEmpty($action['params']);

        $this->assertEquals(2, $a->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_CREATE_UPDATE,
            'action_name' => 'TaskAssignCategoryColor',
            'params' => array(
                'color_id' => 'blue',
                'category_id' => 1,
            )
        )));

        $action = $a->getById(2);
        $this->assertNotEmpty($action);
        $this->assertNotEmpty($action['params']);
        $this->assertEquals('category_id', $action['params'][1]['name']);
        $this->assertEquals(1, $action['params'][1]['value']);

        $actions = $a->getAllByProject(1);
        $this->assertNotEmpty($actions);
        $this->assertCount(2, $actions);

        $this->assertTrue($a->duplicate(1, 2));

        $actions = $a->getAllByProject(2);
        $this->assertNotEmpty($actions);
        $this->assertCount(1, $actions);

        $actions = $a->getAll();
        $this->assertNotEmpty($actions);
        $this->assertCount(3, $actions);

        $action = $a->getById($actions[2]['id']);
        $this->assertNotEmpty($action);
        $this->assertNotEmpty($action['params']);
        $this->assertEquals('color_id', $action['params'][0]['name']);
        $this->assertEquals('blue', $action['params'][0]['value']);
        $this->assertEquals('category_id', $action['params'][1]['name']);
        $this->assertEquals(3, $action['params'][1]['value']);
    }

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
        $this->assertEquals(1, $action->create(array(
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
        $g = new GithubWebhook($this->container);

        // We create a project
        $this->assertEquals(1, $p->create(array('name' => 'unit_test')));

        // We create a new action
        $this->assertEquals(1, $a->create(array(
            'project_id' => 1,
            'event_name' => GithubWebhook::EVENT_ISSUE_OPENED,
            'action_name' => 'TaskCreation',
            'params' => array()
        )));

        $this->assertEquals(2, $a->create(array(
            'project_id' => 1,
            'event_name' => GithubWebhook::EVENT_ISSUE_LABEL_CHANGE,
            'action_name' => 'TaskAssignCategoryLabel',
            'params' => array(
                'label' => 'bug',
                'category_id' => 1,
            )
        )));

        $this->assertEquals(3, $a->create(array(
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
