<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\TwoFactorController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Totp;
use Kanboard\Subscriber\AuthSubscriber;
use KanboardTests\units\Base;

class TwoFactorControllerTest extends Base
{
    private const SECRET = 'JBSWY3DPEHPK3PXP';
    private const INVALID_CODE = 'invalid';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setRequest(array());
        $this->container['dispatcher']->addSubscriber(new AuthSubscriber($this->container));

        $this->container['userModel']->update(array(
            'id' => 1,
            'twofactor_activated' => 1,
            'twofactor_secret' => self::SECRET,
        ));

        $this->container['userSession']->initialize($this->container['userModel']->getById(1));
    }

    public function testCheckWithValidCode()
    {
        $this->container['userLockingModel']->incrementFailedLogin('admin');
        $this->container['userLockingModel']->incrementFailedLogin('admin');

        $this->expectRedirect('DashboardController', 'show');
        $this->submit(array('code' => $this->getValidCode()));

        $this->assertTrue($this->container['userSession']->isPostAuthenticationValidated());
        $this->assertSame(0, $this->container['userLockingModel']->getFailedLogin('admin'));
    }

    public function testCheckWithInvalidCode()
    {
        $this->expectRedirect('TwoFactorController', 'code');
        $this->submit(array('code' => self::INVALID_CODE));

        $this->assertFalse($this->container['userSession']->isPostAuthenticationValidated());
        $this->assertSame(1, $this->container['userLockingModel']->getFailedLogin('admin'));
        $this->assertFalse($this->container['userLockingModel']->isLocked('admin'));
    }

    public function testCheckLocksAccountAfterTooManyInvalidCodes()
    {
        $this->expectRedirect('TwoFactorController', 'code', BRUTEFORCE_LOCKDOWN + 1);

        for ($i = 0; $i < BRUTEFORCE_LOCKDOWN; $i++) {
            $this->submit(array('code' => self::INVALID_CODE));
        }

        $this->assertFalse($this->container['userLockingModel']->isLocked('admin'));

        $this->submit(array('code' => self::INVALID_CODE));

        $this->assertTrue($this->container['userLockingModel']->isLocked('admin'));
        $this->assertFalse($this->container['userSession']->isPostAuthenticationValidated());
    }

    public function testCheckRefusesValidCodeWhenAccountIsLocked()
    {
        $this->container['userLockingModel']->lock('admin');

        $this->expectRedirect('TwoFactorController', 'code');
        $this->submit(array('code' => $this->getValidCode()));

        $this->assertFalse($this->container['userSession']->isPostAuthenticationValidated());
        $this->assertTrue($this->container['userLockingModel']->isLocked('admin'));
        $this->assertSame(0, $this->container['userLockingModel']->getFailedLogin('admin'));
        $this->assertSame(
            t('Your account is locked for %d minutes', BRUTEFORCE_LOCKDOWN_DURATION),
            $this->container['flash']->getMessage('failure')
        );
    }

    public function testCheckWithoutCsrfTokenIsNotCounted()
    {
        $this->expectRedirect('TwoFactorController', 'code', 2);

        $this->submit(array('code' => self::INVALID_CODE), false);
        $this->assertSame(0, $this->container['userLockingModel']->getFailedLogin('admin'));

        $this->submit(array('code' => $this->getValidCode()), false);
        $this->assertFalse($this->container['userSession']->isPostAuthenticationValidated());
        $this->assertSame(0, $this->container['userLockingModel']->getFailedLogin('admin'));
    }

    public function testCheckWithEmptyCodeIsNotCounted()
    {
        $this->expectRedirect('TwoFactorController', 'code');
        $this->submit(array('code' => ''));

        $this->assertFalse($this->container['userSession']->isPostAuthenticationValidated());
        $this->assertSame(0, $this->container['userLockingModel']->getFailedLogin('admin'));
    }

    public function testPasswordAuthenticationDoesNotResetFailedLoginWhileCodeIsPending()
    {
        $this->expectRedirect('TwoFactorController', 'code', 3);

        for ($i = 0; $i < 3; $i++) {
            $this->submit(array('code' => self::INVALID_CODE));
        }

        $this->assertSame(3, $this->container['userLockingModel']->getFailedLogin('admin'));

        // Logging in again with the password must not give a fresh set of attempts
        $this->assertTrue($this->container['authenticationManager']->passwordAuthentication('admin', 'admin'));
        $this->assertFalse($this->container['userSession']->isPostAuthenticationValidated());
        $this->assertSame(3, $this->container['userLockingModel']->getFailedLogin('admin'));

        $this->expectRedirect('DashboardController', 'show');
        $this->submit(array('code' => $this->getValidCode()));

        $this->assertTrue($this->container['userSession']->isPostAuthenticationValidated());
        $this->assertSame(0, $this->container['userLockingModel']->getFailedLogin('admin'));
    }

    private function getValidCode()
    {
        $totp = new Totp();

        return $totp->hotp($totp->base32Decode(self::SECRET), intdiv(time(), Totp::PERIOD));
    }

    private function setRequest(array $post)
    {
        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST', 'REMOTE_ADDR' => '192.0.2.1'),
            array('user_id' => 1),
            $post,
            array(),
            array()
        );
    }

    private function expectRedirect($controller, $action, $times = 1)
    {
        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->exactly($times))
            ->method('redirect')
            ->with($this->container['helper']->url->to($controller, $action));
    }

    private function submit(array $values, $withCsrfToken = true)
    {
        if ($withCsrfToken) {
            $values['csrf_token'] = $this->container['token']->getCSRFToken();
        }

        $this->setRequest($values);

        $controller = new TwoFactorController($this->container);
        $controller->check();
    }
}
