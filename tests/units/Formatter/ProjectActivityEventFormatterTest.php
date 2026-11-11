<?php

namespace KanboardTests\units\Formatter;

use KanboardTests\units\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Formatter\ProjectActivityEventFormatter;

class ProjectActivityEventFormatterTest extends Base
{
    private function isVisible($role, $visibility)
    {
        $this->container['userSession'] = $this
            ->getMockBuilder('\Kanboard\Core\User\UserSession')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('getRole', 'isLogged'))
            ->getMock();

        $this->container['userSession']->method('getRole')->willReturn($role);
        $this->container['userSession']->method('isLogged')->willReturn($role !== '');

        $formatter = new ProjectActivityEventFormatter($this->container);
        $method = new \ReflectionMethod(ProjectActivityEventFormatter::class, 'isCommentVisible');
        $method->setAccessible(true);

        return $method->invoke($formatter, array('id' => 1, 'comment' => 'body', 'visibility' => $visibility));
    }

    public function testAdminOnlyCommentHiddenFromProjectUser()
    {
        $this->assertFalse($this->isVisible(Role::APP_USER, Role::APP_ADMIN));
    }

    public function testAdminOnlyCommentHiddenFromManager()
    {
        $this->assertFalse($this->isVisible(Role::APP_MANAGER, Role::APP_ADMIN));
    }

    public function testAdminOnlyCommentVisibleToAdmin()
    {
        $this->assertTrue($this->isVisible(Role::APP_ADMIN, Role::APP_ADMIN));
    }

    public function testRestrictedCommentHiddenWhenNoSession()
    {
        // Token authenticated feeds have no application role
        $this->assertFalse($this->isVisible('', Role::APP_ADMIN));
        $this->assertFalse($this->isVisible('', Role::APP_MANAGER));
    }

    public function testPublicCommentVisibleToEveryone()
    {
        $this->assertTrue($this->isVisible(Role::APP_USER, Role::APP_USER));
        $this->assertTrue($this->isVisible('', Role::APP_USER));
    }

    public function testManagerCommentVisibleToManagersOnly()
    {
        $this->assertTrue($this->isVisible(Role::APP_MANAGER, Role::APP_MANAGER));
        $this->assertFalse($this->isVisible(Role::APP_USER, Role::APP_MANAGER));
        $this->assertFalse($this->isVisible('', Role::APP_MANAGER));
    }
}
