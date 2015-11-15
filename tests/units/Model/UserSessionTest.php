<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\UserSession;

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
        );

        $us->initialize($user);

        $session = $this->container['sessionStorage']->getAll();

        $this->assertNotEmpty($session);
        $this->assertEquals(123, $session['user']['id']);
        $this->assertEquals('john', $session['user']['username']);
        $this->assertTrue($session['user']['is_admin']);
        $this->assertFalse($session['user']['is_project_admin']);
        $this->assertFalse($session['user']['is_ldap_user']);
        $this->assertFalse($session['user']['twofactor_activated']);
        $this->assertArrayNotHasKey('password', $session['user']);
        $this->assertArrayNotHasKey('twofactor_secret', $session['user']);

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

        $this->container['sessionStorage']->user = array('is_admin' => '1');
        $this->assertFalse($us->isAdmin());

        $this->container['sessionStorage']->user = array('is_admin' => '2');
        $this->assertFalse($us->isAdmin());

        $this->container['sessionStorage']->user = array('is_admin' => false);
        $this->assertFalse($us->isAdmin());

        $this->container['sessionStorage']->user = array('is_admin' => true);
        $this->assertTrue($us->isAdmin());
    }

    public function testIsProjectAdmin()
    {
        $us = new UserSession($this->container);

        $this->assertFalse($us->isProjectAdmin());

        $this->container['sessionStorage']->user = array('is_project_admin' => false);
        $this->assertFalse($us->isProjectAdmin());

        $this->container['sessionStorage']->user = array('is_project_admin' => true);
        $this->assertTrue($us->isProjectAdmin());
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

    public function test2FA()
    {
        $us = new UserSession($this->container);

        $this->assertFalse($us->check2FA());

        $this->container['sessionStorage']->postAuth = array('validated' => false);
        $this->assertFalse($us->check2FA());

        $this->container['sessionStorage']->postAuth = array('validated' => true);
        $this->assertTrue($us->check2FA());

        $this->container['sessionStorage']->user = array();
        $this->assertFalse($us->has2FA());

        $this->container['sessionStorage']->user = array('twofactor_activated' => false);
        $this->assertFalse($us->has2FA());

        $this->container['sessionStorage']->user = array('twofactor_activated' => true);
        $this->assertTrue($us->has2FA());

        $us->disable2FA();
        $this->assertFalse($us->has2FA());
    }
}
