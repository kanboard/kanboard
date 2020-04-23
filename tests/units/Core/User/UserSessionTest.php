<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\User\UserSession;
use Kanboard\Core\Security\Role;

class UserSessionTest extends Base
{
    public function testInitialize()
    {
        $userSession = new UserSession($this->container);
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
            'filter' => 'status:close',
        );

        $userSession->initialize($user);

        $this->assertNotEmpty($_SESSION);
        $this->assertEquals(123, $_SESSION['user']['id']);
        $this->assertEquals('john', $_SESSION['user']['username']);
        $this->assertEquals(Role::APP_MANAGER, $_SESSION['user']['role']);
        $this->assertEquals('status:close', $_SESSION['user']['filter']);
        $this->assertFalse($_SESSION['user']['is_ldap_user']);
        $this->assertFalse($_SESSION['user']['twofactor_activated']);
        $this->assertArrayNotHasKey('password', $_SESSION['user']);
        $this->assertArrayNotHasKey('twofactor_secret', $_SESSION['user']);
        $this->assertArrayNotHasKey('is_admin', $_SESSION['user']);
        $this->assertArrayNotHasKey('is_project_admin', $_SESSION['user']);

        $this->assertEquals('john', $userSession->getUsername());
    }

    public function testGetId()
    {
        $userSession = new UserSession($this->container);

        $this->assertEquals(0, $userSession->getId());

        $_SESSION['user'] = array('id' => 2);
        $this->assertEquals(2, $userSession->getId());

        $_SESSION['user'] = array('id' => '2');
        $this->assertEquals(2, $userSession->getId());
    }

    public function testIsLogged()
    {
        $userSession = new UserSession($this->container);
        $this->assertFalse($userSession->isLogged());

        $_SESSION['user'] = array();
        $this->assertFalse($userSession->isLogged());

        $_SESSION['user'] = array('id' => 1);
        $this->assertTrue($userSession->isLogged());
    }

    public function testIsAdmin()
    {
        $userSession = new UserSession($this->container);
        $this->assertFalse($userSession->isAdmin());

        $_SESSION['user'] = array('role' => Role::APP_ADMIN);
        $this->assertTrue($userSession->isAdmin());

        $_SESSION['user'] = array('role' => Role::APP_USER);
        $this->assertFalse($userSession->isAdmin());

        $_SESSION['user'] = array('role' => '');
        $this->assertFalse($userSession->isAdmin());
    }

    public function testFilters()
    {
        $userSession = new UserSession($this->container);
        $this->assertEquals('status:open', $userSession->getFilters(1));

        $_SESSION['user'] = array('filter' => 'status:open');
        $this->assertEquals('status:open', $userSession->getFilters(1));

        $userSession->setFilters(1, 'assignee:me');
        $this->assertEquals('assignee:me', $userSession->getFilters(1));

        $this->assertEquals('status:open', $userSession->getFilters(2));

        $userSession->setFilters(2, 'assignee:bob');
        $this->assertEquals('assignee:bob', $userSession->getFilters(2));
    }

    public function testListOrder()
    {
        $userSession = new UserSession($this->container);
        list($order, $direction) = $userSession->getListOrder(1);
        $this->assertEquals('tasks.id', $order);
        $this->assertEquals('DESC', $direction);

        $userSession->setListOrder(1, 'tasks.priority', 'ASC');
        list($order, $direction) = $userSession->getListOrder(1);
        $this->assertEquals('tasks.priority', $order);
        $this->assertEquals('ASC', $direction);

        list($order, $direction) = $userSession->getListOrder(2);
        $this->assertEquals('tasks.id', $order);
        $this->assertEquals('DESC', $direction);

        $userSession->setListOrder(2, 'tasks.is_active', 'DESC');
        list($order, $direction) = $userSession->getListOrder(2);
        $this->assertEquals('tasks.is_active', $order);
        $this->assertEquals('DESC', $direction);
    }

    public function testPostAuthentication()
    {
        $userSession = new UserSession($this->container);
        $this->assertFalse($userSession->isPostAuthenticationValidated());

        $_SESSION['postAuthenticationValidated'] = false;
        $this->assertFalse($userSession->isPostAuthenticationValidated());

        $userSession->validatePostAuthentication();
        $this->assertTrue($userSession->isPostAuthenticationValidated());

        $_SESSION['user'] = array();
        $this->assertFalse($userSession->hasPostAuthentication());

        $_SESSION['user'] = array('twofactor_activated' => false);
        $this->assertFalse($userSession->hasPostAuthentication());

        $_SESSION['user'] = array('twofactor_activated' => true);
        $this->assertTrue($userSession->hasPostAuthentication());

        $userSession->disablePostAuthentication();
        $this->assertFalse($userSession->hasPostAuthentication());
    }
}
