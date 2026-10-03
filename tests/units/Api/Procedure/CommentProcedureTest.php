<?php

namespace KanboardTests\units\Api\Procedure;

use Kanboard\Api\Procedure\CommentProcedure;
use Kanboard\Core\Security\Role;
use KanboardTests\units\Base;

class CommentProcedureTest extends Base
{
    public function testCommentsDoNotExposeTheAuthorEmail()
    {
        $this->assertEquals(2, $this->container['userModel']->create(array('username' => 'owner', 'email' => 'owner@example.org')));
        $this->assertEquals(3, $this->container['userModel']->create(array('username' => 'peer')));
        $this->assertEquals(1, $this->container['projectModel']->create(array('name' => 'P1')));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertEquals(1, $this->container['taskCreationModel']->create(array('title' => 'Task', 'project_id' => 1)));
        $this->assertEquals(1, $this->container['commentModel']->create(array('task_id' => 1, 'user_id' => 2, 'comment' => 'hello')));

        $this->container['userSession']->initialize($this->container['userModel']->getById(3));
        $procedure = new CommentProcedure($this->container);

        $comment = $procedure->getComment(1);
        $this->assertEquals('owner', $comment['username']);
        $this->assertArrayNotHasKey('email', $comment);

        $comments = $procedure->getAllComments(1);
        $this->assertCount(1, $comments);
        $this->assertEquals('owner', $comments[0]['username']);
        $this->assertArrayNotHasKey('email', $comments[0]);
    }
}
