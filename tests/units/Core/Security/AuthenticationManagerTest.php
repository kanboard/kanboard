<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Http\Request;
use Kanboard\Core\Security\AuthenticationManager;
use Kanboard\Auth\DatabaseAuth;
use Kanboard\Auth\TotpAuth;
use Kanboard\Auth\ReverseProxyAuth;

class AuthenticationManagerTest extends Base
{
    public function testRegister()
    {
        $authManager = new AuthenticationManager($this->container);
        $authManager->register(new DatabaseAuth($this->container));
        $provider = $authManager->getProvider('Database');

        $this->assertInstanceOf('Kanboard\Core\Security\AuthenticationProviderInterface', $provider);
    }

    public function testGetProviderNotFound()
    {
        $authManager = new AuthenticationManager($this->container);
        $this->expectException('LogicException');
        $authManager->getProvider('Dababase');
    }

    public function testGetPostProviderNotFound()
    {
        $authManager = new AuthenticationManager($this->container);
        $this->expectException('LogicException');
        $authManager->getPostAuthenticationProvider();
    }

    public function testGetPostProvider()
    {
        $authManager = new AuthenticationManager($this->container);
        $authManager->register(new TotpAuth($this->container));
        $provider = $authManager->getPostAuthenticationProvider();

        $this->assertInstanceOf('Kanboard\Core\Security\PostAuthenticationProviderInterface', $provider);
    }

    public function testCheckSessionWhenNobodyIsLogged()
    {
        $authManager = new AuthenticationManager($this->container);
        $authManager->register(new DatabaseAuth($this->container));

        $this->assertFalse($this->container['userSession']->isLogged());
        $this->assertTrue($authManager->checkCurrentSession());
    }

    public function testCheckSessionWhenSomeoneIsLogged()
    {
        $authManager = new AuthenticationManager($this->container);
        $authManager->register(new DatabaseAuth($this->container));

        $_SESSION['user'] = array('id' => 1, 'username' => 'test', 'role' => 'app-admin');

        $this->assertTrue($this->container['userSession']->isLogged());
        $this->assertTrue($authManager->checkCurrentSession());
    }

    public function testCheckSessionWhenNotValid()
    {
        $authManager = new AuthenticationManager($this->container);
        $authManager->register(new DatabaseAuth($this->container));

        $_SESSION['user'] = array('id' => 42, 'username' => 'test', 'role' => 'app-admin');

        $this->assertTrue($this->container['userSession']->isLogged());
        $this->assertFalse($authManager->checkCurrentSession());
        $this->assertFalse($this->container['userSession']->isLogged());
    }

    public function testPreAuthenticationSuccessful()
    {
        $this->container['request'] = new Request($this->container, array(REVERSE_PROXY_USER_HEADER => 'admin'));
        $this->container['dispatcher']->addListener(AuthenticationManager::EVENT_SUCCESS, array($this, 'onSuccess'));
        $this->container['dispatcher']->addListener(AuthenticationManager::EVENT_FAILURE, array($this, 'onFailure'));

        $authManager = new AuthenticationManager($this->container);
        $authManager->register(new ReverseProxyAuth($this->container));

        $this->assertTrue($authManager->preAuthentication());

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(AuthenticationManager::EVENT_SUCCESS.'.AuthenticationManagerTest::onSuccess', $called);
        $this->assertArrayNotHasKey(AuthenticationManager::EVENT_FAILURE.'.AuthenticationManagerTest::onFailure', $called);
    }

    public function testPreAuthenticationFailed()
    {
        $this->container['request'] = new Request($this->container, array(REVERSE_PROXY_USER_HEADER => ''));
        $this->container['dispatcher']->addListener(AuthenticationManager::EVENT_SUCCESS, array($this, 'onSuccess'));
        $this->container['dispatcher']->addListener(AuthenticationManager::EVENT_FAILURE, array($this, 'onFailure'));

        $authManager = new AuthenticationManager($this->container);
        $authManager->register(new ReverseProxyAuth($this->container));

        $this->assertFalse($authManager->preAuthentication());

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayNotHasKey(AuthenticationManager::EVENT_SUCCESS.'.AuthenticationManagerTest::onSuccess', $called);
        $this->assertArrayNotHasKey(AuthenticationManager::EVENT_FAILURE.'.AuthenticationManagerTest::onFailure', $called);
    }

    public function testPasswordAuthenticationSuccessful()
    {
        $this->container['dispatcher']->addListener(AuthenticationManager::EVENT_SUCCESS, array($this, 'onSuccess'));
        $this->container['dispatcher']->addListener(AuthenticationManager::EVENT_FAILURE, array($this, 'onFailure'));

        $authManager = new AuthenticationManager($this->container);
        $authManager->register(new DatabaseAuth($this->container));

        $this->assertTrue($authManager->passwordAuthentication('admin', 'admin'));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(AuthenticationManager::EVENT_SUCCESS.'.AuthenticationManagerTest::onSuccess', $called);
        $this->assertArrayNotHasKey(AuthenticationManager::EVENT_FAILURE.'.AuthenticationManagerTest::onFailure', $called);
    }

    public function testPasswordAuthenticationFailed()
    {
        $this->container['dispatcher']->addListener(AuthenticationManager::EVENT_SUCCESS, array($this, 'onSuccess'));
        $this->container['dispatcher']->addListener(AuthenticationManager::EVENT_FAILURE, array($this, 'onFailure'));

        $authManager = new AuthenticationManager($this->container);
        $authManager->register(new DatabaseAuth($this->container));

        $this->assertFalse($authManager->passwordAuthentication('admin', 'wrong password'));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayNotHasKey(AuthenticationManager::EVENT_SUCCESS.'.AuthenticationManagerTest::onSuccess', $called);
        $this->assertArrayHasKey(AuthenticationManager::EVENT_FAILURE.'.AuthenticationManagerTest::onFailure', $called);
    }

    public function onSuccess($event)
    {
        $this->assertInstanceOf('Kanboard\Event\AuthSuccessEvent', $event);
        $this->assertTrue(in_array($event->getAuthType(), array('Database', 'ReverseProxy')));
    }

    public function onFailure($event)
    {
        $this->assertInstanceOf('Kanboard\Event\AuthFailureEvent', $event);
        $this->assertEquals('admin', $event->getUsername());
    }
}
