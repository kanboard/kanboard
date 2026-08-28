<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\ProjectCreationController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use KanboardTests\units\Base;

class ProjectCreationControllerTest extends Base
{
    public function testSaveRejectsTeamProjectForRegularUser()
    {
        $this->buildSaveRequest(Role::APP_USER, 0);

        try {
            (new ProjectCreationController($this->container))->save();
            $this->fail('A team project created by a regular user must be rejected');
        } catch (AccessForbiddenException $e) {
            $this->assertEmpty($this->container['projectModel']->getAll());
        }
    }

    public function testSaveAllowsTeamProjectForManager()
    {
        $this->buildSaveRequest(Role::APP_MANAGER, 0);
        $this->expectRedirect();

        (new ProjectCreationController($this->container))->save();

        $project = $this->container['projectModel']->getById(1);
        $this->assertEquals('My project', $project['name']);
        $this->assertEquals(0, $project['is_private']);
    }

    public function testSaveAllowsPersonalProjectForRegularUser()
    {
        $this->buildSaveRequest(Role::APP_USER, 1);
        $this->expectRedirect();

        (new ProjectCreationController($this->container))->save();

        $project = $this->container['projectModel']->getById(1);
        $this->assertEquals('My project', $project['name']);
        $this->assertEquals(1, $project['is_private']);
    }

    public function testSaveRejectsPersonalProjectWhenDisabled()
    {
        $this->container['configModel']->save(array('disable_private_project' => 1));
        $this->buildSaveRequest(Role::APP_USER, 1);

        try {
            (new ProjectCreationController($this->container))->save();
            $this->fail('A personal project must be rejected when they are disabled');
        } catch (AccessForbiddenException $e) {
            $this->assertEmpty($this->container['projectModel']->getAll());
        }
    }

    public function testCreatePrivateRejectedWhenPersonalProjectsDisabled()
    {
        $this->container['configModel']->save(array('disable_private_project' => 1));
        $this->buildSaveRequest(Role::APP_USER, 1);

        $this->expectException(AccessForbiddenException::class);

        (new ProjectCreationController($this->container))->createPrivate();
    }

    public function testSaveNeverDuplicatesATeamProjectForARegularUser()
    {
        $this->createSourceProject(0);
        $this->buildSaveRequest(Role::APP_USER, 2, array('src_project_id' => 1));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->expectRedirect();

        (new ProjectCreationController($this->container))->save();

        $project = $this->container['projectModel']->getById(2);
        $this->assertEquals('My project', $project['name']);
        $this->assertEquals(1, $project['is_private'], 'An unexpected is_private value must not produce a team project');
    }

    public function testSaveRejectsPrivateDuplicationWhenPersonalProjectsDisabled()
    {
        $this->container['configModel']->save(array('disable_private_project' => 1));
        $this->createSourceProject(1);
        $this->buildSaveRequest(Role::APP_MANAGER, 0, array('src_project_id' => 1));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 2, Role::PROJECT_MANAGER));

        try {
            (new ProjectCreationController($this->container))->save();
            $this->fail('A personal project must be rejected when they are disabled');
        } catch (AccessForbiddenException $e) {
            $this->assertCount(1, $this->container['projectModel']->getAll());
        }
    }

    public function testSaveRejectsInaccessibleSourceProjectBeforeReadingItsVisibility()
    {
        $this->createSourceProject(1);
        $this->buildSaveRequest(Role::APP_USER, 0, array('src_project_id' => 1, 'name' => ''));

        $this->expectException(AccessForbiddenException::class);

        (new ProjectCreationController($this->container))->save();
    }

    public function testSaveRejectsInaccessibleSourceProjectForManager()
    {
        $this->createSourceProject(0);
        $this->buildSaveRequest(Role::APP_MANAGER, 0, array('src_project_id' => 1));

        $this->expectException(AccessForbiddenException::class);

        (new ProjectCreationController($this->container))->save();
    }

    public function testSaveAllowsPersonalProjectDuplicationForRegularUser()
    {
        $this->createSourceProject(1);
        $this->buildSaveRequest(Role::APP_USER, 1, array('src_project_id' => 1));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->expectRedirect();

        (new ProjectCreationController($this->container))->save();

        $project = $this->container['projectModel']->getById(2);
        $this->assertEquals('My project', $project['name']);
        $this->assertEquals(1, $project['is_private']);
    }

    private function expectRedirect()
    {
        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');
    }

    private function buildSaveRequest($role, $isPrivate, array $extraValues = array())
    {
        $this->assertEquals(2, $this->container['userModel']->create(array('username' => 'user1', 'role' => $role)));
        $this->container['userSession']->initialize($this->container['userModel']->getById(2));

        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array(),
            array_merge(array(
                'csrf_token' => $this->container['token']->getCSRFToken(),
                'name' => 'My project',
                'identifier' => '',
                'task_limit' => 0,
                'is_private' => $isPrivate,
            ), $extraValues),
            array(),
            array()
        );
    }

    private function createSourceProject($isPrivate)
    {
        $this->assertEquals(1, $this->container['projectModel']->create(array(
            'name' => 'Source project',
            'is_private' => $isPrivate,
        )));
    }
}
