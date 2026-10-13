<?php

namespace KanboardTests\units\Api\Procedure;

use Kanboard\Api\Procedure\MeProcedure;
use Kanboard\Api\Procedure\ProjectProcedure;
use Kanboard\Core\Security\Role;
use Kanboard\EventBuilder\CommentEventBuilder;
use Kanboard\Model\CommentModel;
use KanboardTests\units\Base;

class ProjectActivityProcedureTest extends Base
{
    public function testProjectActivityDoesNotExposeTheAuthorEmail()
    {
        $this->createCommentEvent();
        $procedure = new ProjectProcedure($this->container);

        $this->assertEventsWithoutEmail($procedure->getProjectActivity(1));
        $this->assertEventsWithoutEmail($procedure->getProjectActivities(array(1)));
    }

    public function testMyActivityStreamDoesNotExposeTheAuthorEmail()
    {
        $this->createCommentEvent();
        $procedure = new MeProcedure($this->container);

        $this->assertEventsWithoutEmail($procedure->getMyActivityStream());
    }

    private function assertEventsWithoutEmail(array $events)
    {
        $this->assertCount(1, $events);
        $this->assertEquals('owner', $events[0]['author_username']);
        $this->assertEquals('hello', $events[0]['comment']['comment']);
        $this->assertStringNotContainsString('owner@example.org', json_encode($events));
    }

    private function createCommentEvent()
    {
        $this->assertEquals(2, $this->container['userModel']->create(array('username' => 'owner', 'email' => 'owner@example.org')));
        $this->assertEquals(3, $this->container['userModel']->create(array('username' => 'peer')));
        $this->assertEquals(1, $this->container['projectModel']->create(array('name' => 'P1')));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertEquals(1, $this->container['taskCreationModel']->create(array('title' => 'Task', 'project_id' => 1)));
        $this->assertEquals(1, $this->container['commentModel']->create(array('task_id' => 1, 'user_id' => 2, 'comment' => 'hello')));

        $event = (new CommentEventBuilder($this->container))->withCommentId(1)->buildEvent();
        $this->assertNotFalse($this->container['projectActivityModel']->createEvent(1, 1, 2, CommentModel::EVENT_CREATE, $event->getAll()));

        $this->container['userSession']->initialize($this->container['userModel']->getById(3));
    }
}
