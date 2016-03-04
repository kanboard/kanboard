<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Comment;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectUserRole;
use Kanboard\Model\User;
use Kanboard\Action\CommentCreation;
use Kanboard\Core\Security\Role;

class CommentCreationTest extends Base
{
    public function testSuccess()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $projectUserRoleModel = new ProjectUserRole($this->container);
        $commentModel = new Comment($this->container);
        $taskCreationModel = new TaskCreation($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'comment' => 'test123', 'reference' => 'ref123', 'user_id' => 2));

        $action = new CommentCreation($this->container);
        $action->setProjectId(1);
        $action->addEvent('test.event', 'Test Event');

        $this->assertTrue($action->execute($event, 'test.event'));

        $comment = $commentModel->getById(1);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals('test123', $comment['comment']);
        $this->assertEquals('ref123', $comment['reference']);
        $this->assertEquals(2, $comment['user_id']);
    }

    public function testWithUserNotAssignable()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $projectUserRoleModel = new ProjectUserRole($this->container);
        $commentModel = new Comment($this->container);
        $taskCreationModel = new TaskCreation($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'comment' => 'test123', 'user_id' => 2));

        $action = new CommentCreation($this->container);
        $action->setProjectId(1);
        $action->addEvent('test.event', 'Test Event');

        $this->assertTrue($action->execute($event, 'test.event'));

        $comment = $commentModel->getById(1);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals('test123', $comment['comment']);
        $this->assertEquals('', $comment['reference']);
        $this->assertEquals(0, $comment['user_id']);
    }

    public function testWithNoComment()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $projectUserRoleModel = new ProjectUserRole($this->container);
        $commentModel = new Comment($this->container);
        $taskCreationModel = new TaskCreation($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1));

        $action = new CommentCreation($this->container);
        $action->setProjectId(1);
        $action->addEvent('test.event', 'Test Event');

        $this->assertFalse($action->execute($event, 'test.event'));
    }
}
