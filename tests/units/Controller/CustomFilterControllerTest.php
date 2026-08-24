<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\CustomFilterController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use KanboardTests\units\Base;

class CustomFilterControllerTest extends Base
{
    public function testEditRejectsFilterFromAnotherProject()
    {
        $this->createProjectsAndFilter();
        $this->buildRequest('GET', 2, 1);

        $this->expectException(AccessForbiddenException::class);

        (new CustomFilterController($this->container))->edit();
    }

    public function testUpdateRejectsAndPreservesFilterFromAnotherProject()
    {
        $this->createProjectsAndFilter();
        $this->buildRequest('POST', 2, 1, array(
            'csrf_token' => $this->container['token']->getCSRFToken(),
            'user_id' => 3,
            'name' => 'PWNED_BY_ATTACKER',
            'filter' => 'status:closed',
            'is_shared' => 1,
            'append' => 0,
        ));

        $originalFilter = $this->container['customFilterModel']->getById(1);

        try {
            (new CustomFilterController($this->container))->update();
            $this->fail('A filter from another project must be rejected');
        } catch (AccessForbiddenException $e) {
            $this->assertSame($originalFilter, $this->container['customFilterModel']->getById(1));
        }
    }

    public function testUpdateAllowsProjectManagerForFilterInSameProject()
    {
        $this->createProjectsAndFilter();
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 3, Role::PROJECT_MANAGER));
        $this->buildRequest('POST', 1, 1, array(
            'csrf_token' => $this->container['token']->getCSRFToken(),
            'user_id' => 3,
            'name' => 'Updated filter',
            'filter' => 'status:closed',
            'is_shared' => 1,
            'append' => 0,
        ));

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');

        (new CustomFilterController($this->container))->update();

        $filter = $this->container['customFilterModel']->getById(1);
        $this->assertEquals(1, $filter['project_id']);
        $this->assertEquals(2, $filter['user_id'], 'the owner must not be taken from the request');
        $this->assertEquals('Updated filter', $filter['name']);
        $this->assertEquals('status:closed', $filter['filter']);
        $this->assertEquals(1, $filter['is_shared']);
    }

    public function testUpdateKeepsOwnerAndSharingForPlainMember()
    {
        $this->createProjectsAndFilter();
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertEquals(2, $this->container['customFilterModel']->create(array(
            'project_id' => 1,
            'user_id' => 3,
            'name' => 'Member filter',
            'filter' => 'status:open',
            'is_shared' => 0,
        )));

        $this->assertFalse($this->container['helper']->user->hasProjectAccess('ProjectEditController', 'show', 1));

        $this->buildRequest('POST', 1, 2, array(
            'csrf_token' => $this->container['token']->getCSRFToken(),
            'user_id' => 2,
            'name' => 'Updated filter',
            'filter' => 'status:closed',
            'is_shared' => 1,
            'append' => 0,
        ));

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');

        (new CustomFilterController($this->container))->update();

        $filter = $this->container['customFilterModel']->getById(2);
        $this->assertEquals(3, $filter['user_id'], 'a member must not give their filter away');
        $this->assertEquals(0, $filter['is_shared'], 'a member must not share a filter');
        $this->assertEquals('Updated filter', $filter['name']);
    }

    public function testUpdateKeepsSharedFlagWhenPlainMemberUnshares()
    {
        $this->createProjectsAndFilter();
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertEquals(2, $this->container['customFilterModel']->create(array(
            'project_id' => 1,
            'user_id' => 3,
            'name' => 'Shared filter',
            'filter' => 'status:open',
            'is_shared' => 1,
        )));

        $this->buildRequest('POST', 1, 2, array(
            'csrf_token' => $this->container['token']->getCSRFToken(),
            'name' => 'Updated filter',
            'filter' => 'status:closed',
            'append' => 0,
        ));

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');

        (new CustomFilterController($this->container))->update();

        $filter = $this->container['customFilterModel']->getById(2);
        $this->assertEquals(1, $filter['is_shared'], 'a member must not unshare a filter');
        $this->assertEquals('Updated filter', $filter['name']);
    }

    public function testSaveIgnoresSharingRequestedByPlainMember()
    {
        $this->createProjectsAndFilter();
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 3, Role::PROJECT_MEMBER));

        $this->buildRequest('POST', 1, 0, array(
            'csrf_token' => $this->container['token']->getCSRFToken(),
            'name' => 'New filter',
            'filter' => 'status:open',
            'is_shared' => 1,
            'append' => 0,
        ));

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');

        (new CustomFilterController($this->container))->save();

        $filter = $this->container['customFilterModel']->getById(2);
        $this->assertEquals('New filter', $filter['name']);
        $this->assertEquals(3, $filter['user_id']);
        $this->assertEquals(0, $filter['is_shared'], 'a member must not create a shared filter');
    }

    private function createProjectsAndFilter()
    {
        $this->assertEquals(2, $this->container['userModel']->create(array('username' => 'victim')));
        $this->assertEquals(3, $this->container['userModel']->create(array('username' => 'attacker')));
        $this->assertEquals(1, $this->container['projectModel']->create(array('name' => 'Victim project', 'is_private' => 1), 2, true));
        $this->assertEquals(2, $this->container['projectModel']->create(array('name' => 'Attacker project', 'is_private' => 1), 3, true));
        $this->assertEquals(1, $this->container['customFilterModel']->create(array(
            'project_id' => 1,
            'user_id' => 2,
            'name' => 'Victim filter',
            'filter' => 'status:open',
        )));

        $this->container['userSession']->initialize($this->container['userModel']->getById(3));
    }

    private function buildRequest($method, $projectId, $filterId, array $values = array())
    {
        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => $method),
            array('project_id' => $projectId, 'filter_id' => $filterId),
            $values,
            array(),
            array()
        );
    }
}
