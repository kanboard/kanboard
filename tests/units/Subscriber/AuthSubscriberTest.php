<?php

namespace KanboardTests\units\Subscriber;

use Kanboard\Core\Http\Request;
use Kanboard\Event\AuthFailureEvent;
use Kanboard\Event\AuthSuccessEvent;
use Kanboard\Subscriber\AuthSubscriber;
use KanboardTests\units\Base;

class AuthSubscriberTest extends Base
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container['request'] = new Request(
            $this->container,
            array('REMOTE_ADDR' => '192.0.2.1'),
            array(),
            array(),
            array(),
            array()
        );
    }

    public function testAfterLoginResetsFailedLoginWithoutTwoFactor()
    {
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $this->container['userLockingModel']->lock('admin');
        $this->container['userSession']->initialize($this->container['userModel']->getById(1));

        $subscriber = new AuthSubscriber($this->container);
        $subscriber->afterLogin(new AuthSuccessEvent('Database'));

        $this->assertSame(0, $this->container['userLockingModel']->getFailedLogin('admin'));
        $this->assertFalse($this->container['userLockingModel']->isLocked('admin'));
    }

    public function testAfterLoginKeepsFailedLoginWhileTwoFactorIsPending()
    {
        $this->enableTwoFactor();
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $this->container['userLockingModel']->lock('admin');
        $this->container['userSession']->initialize($this->container['userModel']->getById(1));

        $subscriber = new AuthSubscriber($this->container);
        $subscriber->afterLogin(new AuthSuccessEvent('Database'));

        $this->assertSame(2, $this->container['userLockingModel']->getFailedLogin('admin'));
        $this->assertTrue($this->container['userLockingModel']->isLocked('admin'));
        $this->assertFalse($this->container['userSession']->isPostAuthenticationValidated());
    }

    public function testAfterLoginResetsFailedLoginWithRememberMe()
    {
        $this->enableTwoFactor();
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $this->container['userSession']->initialize($this->container['userModel']->getById(1));

        $subscriber = new AuthSubscriber($this->container);
        $subscriber->afterLogin(new AuthSuccessEvent('RememberMe'));

        $this->assertSame(0, $this->container['userLockingModel']->getFailedLogin('admin'));
        $this->assertTrue($this->container['userSession']->isPostAuthenticationValidated());
    }

    public function testOnLoginFailureLocksAccountAfterTooManyAttempts()
    {
        $subscriber = new AuthSubscriber($this->container);

        for ($i = 0; $i < BRUTEFORCE_LOCKDOWN; $i++) {
            $subscriber->onLoginFailure(new AuthFailureEvent('admin'));
        }

        $this->assertSame(BRUTEFORCE_LOCKDOWN, $this->container['userLockingModel']->getFailedLogin('admin'));
        $this->assertFalse($this->container['userLockingModel']->isLocked('admin'));

        $subscriber->onLoginFailure(new AuthFailureEvent('admin'));

        $this->assertTrue($this->container['userLockingModel']->isLocked('admin'));
    }

    private function enableTwoFactor()
    {
        $this->container['userModel']->update(array(
            'id' => 1,
            'twofactor_activated' => 1,
            'twofactor_secret' => 'JBSWY3DPEHPK3PXP',
        ));
    }
}
