<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\UserAjaxController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class UserAjaxControllerTest extends Base
{
    public function testMentionWithProjectId()
    {
        $this->createProject();

        $this->assertSame(array('alice'), $this->getMentionedUsernames('1'));
    }

    public function testMentionWithNonCanonicalProjectId()
    {
        $this->createProject();

        // These values skip the project access check, the project must not be resolved from them
        foreach (array('1.0', ' 1', '1e0', '1abc') as $project_id) {
            $this->assertSame(array(), $this->getMentionedUsernames($project_id), $project_id);
        }
    }

    private function getMentionedUsernames($project_id)
    {
        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'GET'),
            array('project_id' => $project_id, 'search' => 'a')
        );

        $result = new \ArrayObject();

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('json'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('json')
            ->willReturnCallback(function (array $data) use ($result) {
                $result->exchangeArray($data);
            });

        $controller = new UserAjaxController($this->container);
        $controller->mention();

        return array_column($result->getArrayCopy(), 'value');
    }

    private function createProject()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'alice')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Private team project')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
    }
}
