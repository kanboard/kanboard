<?php

namespace KanboardTests\units\Helper;

use KanboardTests\units\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Helper\ProjectActivityHelper;
use Kanboard\Model\CommentModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectActivityModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;

class ProjectActivityHelperTest extends Base
{
    public function testGetProjectEvents()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(3, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 2, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(2))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 3, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(3))));

        $helper = new ProjectActivityHelper($this->container);
        $events = $helper->getProjectEvents(1);

        $this->assertCount(3, $events);
        $this->assertEquals(3, $events[0]['task_id']);
        $this->assertNotEmpty($events[0]['event_content']);
        $this->assertNotEmpty($events[0]['event_title']);
        $this->assertNotEmpty($events[0]['author']);
        $this->assertIsArray($events[0]['task']);
    }

    public function testGetProjectsEvents()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'P2')));
        $this->assertEquals(3, $projectModel->create(array('name' => 'P3')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 2)));
        $this->assertEquals(3, $taskCreation->create(array('title' => 'Test', 'project_id' => 3)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(2, 2, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(2))));
        $this->assertNotFalse($projectActivityModel->createEvent(3, 3, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(3))));

        $helper = new ProjectActivityHelper($this->container);
        $events = $helper->getProjectsEvents(array(1, 2));

        $this->assertCount(2, $events);
        $this->assertEquals(2, $events[0]['task_id']);
        $this->assertNotEmpty($events[0]['event_content']);
        $this->assertNotEmpty($events[0]['event_title']);
        $this->assertNotEmpty($events[0]['author']);
        $this->assertIsArray($events[0]['task']);
    }

    public function testGetTaskEvents()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(1, 2, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(2))));

        $helper = new ProjectActivityHelper($this->container);
        $events = $helper->getTaskEvents(1);

        $this->assertCount(1, $events);
        $this->assertEquals(1, $events[0]['task_id']);
        $this->assertNotEmpty($events[0]['event_content']);
        $this->assertNotEmpty($events[0]['event_title']);
        $this->assertNotEmpty($events[0]['author']);
        $this->assertIsArray($events[0]['task']);
    }

    public function testGetProjectEventsWithCommentVisibility()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $task = $taskFinder->getDetails(1);

        $comments = array(
            'legacy' => array('id' => 1, 'task_id' => 1, 'comment' => 'Legacy comment'),
            'user' => array('id' => 2, 'task_id' => 1, 'comment' => 'User comment', 'visibility' => Role::APP_USER),
            'manager' => array('id' => 3, 'task_id' => 1, 'comment' => 'Manager comment', 'visibility' => Role::APP_MANAGER),
            'admin' => array('id' => 4, 'task_id' => 1, 'comment' => 'Admin comment', 'visibility' => Role::APP_ADMIN),
        );

        foreach ($comments as $comment) {
            $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, CommentModel::EVENT_CREATE, array('task' => $task, 'comment' => $comment)));
        }

        $expected = array(
            '' => array('Legacy comment', 'User comment'),
            Role::APP_USER => array('Legacy comment', 'User comment'),
            Role::APP_MANAGER => array('Legacy comment', 'User comment', 'Manager comment'),
            Role::APP_ADMIN => array('Legacy comment', 'User comment', 'Manager comment', 'Admin comment'),
        );

        $helper = new ProjectActivityHelper($this->container);

        foreach ($expected as $role => $visibleComments) {
            $_SESSION['user'] = $role === '' ? array() : array('id' => 1, 'role' => $role);

            $events = $helper->getProjectEvents(1);
            $this->assertSame($visibleComments, array_reverse(array_column(array_column($events, 'comment'), 'comment')), $role);
        }

        $_SESSION['user'] = array('id' => 1, 'role' => Role::APP_ADMIN);

        foreach (array($helper->getProjectEvents(1, 50, Role::APP_USER), $helper->getProjectsEvents(array(1), 50, Role::APP_USER)) as $events) {
            $this->assertSame($expected[Role::APP_USER], array_reverse(array_column(array_column($events, 'comment'), 'comment')));
        }
    }
}
