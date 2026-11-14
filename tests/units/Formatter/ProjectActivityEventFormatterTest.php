<?php

namespace KanboardTests\units\Formatter;

use KanboardTests\units\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Helper\ProjectActivityHelper;
use Kanboard\Model\CommentModel;
use Kanboard\Model\ProjectActivityModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;

class ProjectActivityEventFormatterTest extends Base
{
    private $projectId;

    private $taskId;

    protected function setUp(): void
    {
        parent::setUp();

        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task', 'project_id' => 1)));

        $this->projectId = 1;
        $this->taskId = 1;

        $this->createCommentEvent(Role::APP_USER, 'public comment');
        $this->createCommentEvent(Role::APP_MANAGER, 'manager comment');
        $this->createCommentEvent(Role::APP_ADMIN, 'admin comment');
    }

    private function createCommentEvent($visibility, $body)
    {
        $projectActivityModel = new ProjectActivityModel($this->container);

        return $projectActivityModel->createEvent(
            $this->projectId,
            $this->taskId,
            1,
            CommentModel::EVENT_CREATE,
            array(
                'task' => array('id' => $this->taskId, 'title' => 'Task'),
                'comment' => array(
                    'id' => 1,
                    'comment' => $body,
                    'visibility' => $visibility,
                ),
            )
        );
    }

    private function getBodies($role)
    {
        $helper = new ProjectActivityHelper($this->container);
        $events = $helper->getProjectEvents($this->projectId, 50, $role);
        $bodies = array();

        foreach ($events as $event) {
            if (isset($event['comment'])) {
                $bodies[] = $event['comment']['comment'];
            }
        }

        return $bodies;
    }

    public function testAdministratorSeesEveryComment()
    {
        $bodies = $this->getBodies(Role::APP_ADMIN);

        $this->assertContains('public comment', $bodies);
        $this->assertContains('manager comment', $bodies);
        $this->assertContains('admin comment', $bodies);
    }

    public function testManagerDoesNotSeeAdministratorComments()
    {
        $bodies = $this->getBodies(Role::APP_MANAGER);

        $this->assertContains('public comment', $bodies);
        $this->assertContains('manager comment', $bodies);
        $this->assertNotContains('admin comment', $bodies);
    }

    public function testRegularUserOnlySeesPublicComments()
    {
        $bodies = $this->getBodies(Role::APP_USER);

        $this->assertContains('public comment', $bodies);
        $this->assertNotContains('manager comment', $bodies);
        $this->assertNotContains('admin comment', $bodies);
    }

    public function testUnknownRoleOnlySeesPublicComments()
    {
        // Token authenticated feeds and any unexpected role must not see restricted comments
        foreach (array('', null, Role::APP_PUBLIC, 'unexpected-role') as $role) {
            $bodies = $this->getBodies($role);
            $this->assertContains('public comment', $bodies);
            $this->assertNotContains('manager comment', $bodies);
            $this->assertNotContains('admin comment', $bodies);
        }
    }

    public function testUnknownVisibilityIsDenied()
    {
        $this->createCommentEvent('future-visibility', 'unknown visibility comment');

        foreach (array(Role::APP_ADMIN, Role::APP_MANAGER, Role::APP_USER, '') as $role) {
            $this->assertNotContains('unknown visibility comment', $this->getBodies($role));
        }
    }

    public function testMissingVisibilityIsDenied()
    {
        $projectActivityModel = new ProjectActivityModel($this->container);
        $projectActivityModel->createEvent($this->projectId, $this->taskId, 1, CommentModel::EVENT_CREATE, array(
            'task' => array('id' => $this->taskId, 'title' => 'Task'),
            'comment' => array('id' => 9, 'comment' => 'no visibility key'),
        ));

        foreach (array(Role::APP_ADMIN, Role::APP_MANAGER, Role::APP_USER, '') as $role) {
            $this->assertNotContains('no visibility key', $this->getBodies($role));
        }
    }

    public function testRoleDefaultsToSessionWhenNotProvided()
    {
        $helper = new ProjectActivityHelper($this->container);
        $events = $helper->getProjectEvents($this->projectId, 50);
        $bodies = array();

        foreach ($events as $event) {
            if (isset($event['comment'])) {
                $bodies[] = $event['comment']['comment'];
            }
        }

        // The test session has no role, so restricted comments must be hidden
        $this->assertContains('public comment', $bodies);
        $this->assertNotContains('admin comment', $bodies);
    }
}
