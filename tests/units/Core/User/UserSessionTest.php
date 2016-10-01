<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\User\UserSession;
use Kanboard\Core\Security\Role;

class UserSessionTest extends Base
{
    public function testInitialize()
    {
        $us = new UserSession($this->container);

        $user = array(
            'id' => '123',
            'username' => 'john',
            'password' => 'something',
            'twofactor_secret' => 'something else',
            'is_admin' => '1',
            'is_project_admin' => '0',
            'is_ldap_user' => '0',
            'twofactor_activated' => '0',
            'role' => Role::APP_MANAGER,
        );

        $us->initialize($user);

        $session = $this->container['sessionStorage']->getAll();

        $this->assertNotEmpty($session);
        $this->assertEquals(123, $session['user']['id']);
        $this->assertEquals('john', $session['user']['username']);
        $this->assertEquals(Role::APP_MANAGER, $session['user']['role']);
        $this->assertFalse($session['user']['is_ldap_user']);
        $this->assertFalse($session['user']['twofactor_activated']);
        $this->assertArrayNotHasKey('password', $session['user']);
        $this->assertArrayNotHasKey('twofactor_secret', $session['user']);
        $this->assertArrayNotHasKey('is_admin', $session['user']);
        $this->assertArrayNotHasKey('is_project_admin', $session['user']);

        $this->assertEquals('john', $us->getUsername());
    }

    public function testGetId()
    {
        $us = new UserSession($this->container);

        $this->assertEquals(0, $us->getId());

        $this->container['sessionStorage']->user = array('id' => 2);
        $this->assertEquals(2, $us->getId());

        $this->container['sessionStorage']->user = array('id' => '2');
        $this->assertEquals(2, $us->getId());
    }

    public function testIsLogged()
    {
        $us = new UserSession($this->container);

        $this->assertFalse($us->isLogged());

        $this->container['sessionStorage']->user = array();
        $this->assertFalse($us->isLogged());

        $this->container['sessionStorage']->user = array('id' => 1);
        $this->assertTrue($us->isLogged());
    }

    public function testIsAdmin()
    {
        $us = new UserSession($this->container);

        $this->assertFalse($us->isAdmin());

        $this->container['sessionStorage']->user = array('role' => Role::APP_ADMIN);
        $this->assertTrue($us->isAdmin());

        $this->container['sessionStorage']->user = array('role' => Role::APP_USER);
        $this->assertFalse($us->isAdmin());

        $this->container['sessionStorage']->user = array('role' => '');
        $this->assertFalse($us->isAdmin());
    }

    public function testCommentSorting()
    {
        $us = new UserSession($this->container);
        $this->assertEquals('ASC', $us->getCommentSorting());

        $us->setCommentSorting('DESC');
        $this->assertEquals('DESC', $us->getCommentSorting());
    }

    public function testBoardCollapseMode()
    {
        $us = new UserSession($this->container);
        $this->assertFalse($us->isBoardCollapsed(2));

        $us->setBoardDisplayMode(3, false);
        $this->assertFalse($us->isBoardCollapsed(3));

        $us->setBoardDisplayMode(3, true);
        $this->assertTrue($us->isBoardCollapsed(3));
    }

    public function testFilters()
    {
        $us = new UserSession($this->container);
        $this->assertEquals('status:open', $us->getFilters(1));

        $us->setFilters(1, 'assignee:me');
        $this->assertEquals('assignee:me', $us->getFilters(1));

        $this->assertEquals('status:open', $us->getFilters(2));

        $us->setFilters(2, 'assignee:bob');
        $this->assertEquals('assignee:bob', $us->getFilters(2));
    }

    public function testPostAuthentication()
    {
        $us = new UserSession($this->container);
        $this->assertFalse($us->isPostAuthenticationValidated());

        $this->container['sessionStorage']->postAuthenticationValidated = false;
        $this->assertFalse($us->isPostAuthenticationValidated());

        $us->validatePostAuthentication();
        $this->assertTrue($us->isPostAuthenticationValidated());

        $this->container['sessionStorage']->user = array();
        $this->assertFalse($us->hasPostAuthentication());

        $this->container['sessionStorage']->user = array('twofactor_activated' => false);
        $this->assertFalse($us->hasPostAuthentication());

        $this->container['sessionStorage']->user = array('twofactor_activated' => true);
        $this->assertTrue($us->hasPostAuthentication());

        $us->disablePostAuthentication();
        $this->assertFalse($us->hasPostAuthentication());
    }
}
