<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use PHPQRCode;

/**
 * Two Factor Auth controller
 *
 * @package  Kanboard/Controller
 * @author   Frederic Guillot
 */
class TwoFactorController extends UserViewController
{
    /**
     * Only the current user can access to 2FA settings
     *
     * @access private
     * @param  array $user
     * @throws AccessForbiddenException
     */
    private function checkCurrentUser(array $user)
    {
        if ($user['id'] != $this->userSession->getId()) {
            throw new AccessForbiddenException();
        }
    }

    /**
     * Show form to disable/enable 2FA
     *
     * @access public
     */
    public function index()
    {
        $user = $this->getUser();
        $this->checkCurrentUser($user);
        unset($this->sessionStorage->twoFactorSecret);

        $this->response->html($this->helper->layout->user('twofactor/index', array(
            'user' => $user,
            'provider' => $this->authenticationManager->getPostAuthenticationProvider()->getName(),
        )));
    }

    /**
     * Show page with secret and test form
     *
     * @access public
     */
    public function show()
    {
        $user = $this->getUser();
        $this->checkCurrentUser($user);

        $label = $user['email'] ?: $user['username'];
        $provider = $this->authenticationManager->getPostAuthenticationProvider();

        if (! isset($this->sessionStorage->twoFactorSecret)) {
            $provider->generateSecret();
            $provider->beforeCode();
            $this->sessionStorage->twoFactorSecret = $provider->getSecret();
        } else {
            $provider->setSecret($this->sessionStorage->twoFactorSecret);
        }

        $this->response->html($this->helper->layout->user('twofactor/show', array(
            'user'    => $user,
            'secret'  => $this->sessionStorage->twoFactorSecret,
            'key_url' => $provider->getKeyUrl($label),
        )));
    }

    /**
     * Test code and save secret
     *
     * @access public
     */
    public function test()
    {
        $user = $this->getUser();
        $this->checkCurrentUser($user);

        $values = $this->request->getValues();

        $provider = $this->authenticationManager->getPostAuthenticationProvider();
        $provider->setCode(empty($values['code']) ? '' : $values['code']);
        $provider->setSecret($this->sessionStorage->twoFactorSecret);

        if ($provider->authenticate()) {
            $this->flash->success(t('The two factor authentication code is valid.'));

            $this->userModel->update(array(
                'id' => $user['id'],
                'twofactor_activated' => 1,
                'twofactor_secret' => $this->authenticationManager->getPostAuthenticationProvider()->getSecret(),
            ));

            unset($this->sessionStorage->twoFactorSecret);
            $this->userSession->disablePostAuthentication();

            $this->response->redirect($this->helper->url->to('TwoFactorController', 'index', array('user_id' => $user['id'])), true);
        } else {
            $this->flash->failure(t('The two factor authentication code is not valid.'));

            if ($this->request->isAjax()) {
                $this->show();
            } else {
                $this->response->redirect($this->helper->url->to('TwoFactorController', 'show', array('user_id' => $user['id'])));
            }
        }
    }

    /**
     * Disable 2FA for the current user
     *
     * @access public
     */
    public function deactivate()
    {
        $user = $this->getUser();
        $this->checkCurrentUser($user);

        $this->userModel->update(array(
            'id' => $user['id'],
            'twofactor_activated' => 0,
            'twofactor_secret' => '',
        ));

        // Allow the user to test or disable the feature
        $this->userSession->disablePostAuthentication();

        $this->flash->success(t('User updated successfully.'));
        $this->response->redirect($this->helper->url->to('TwoFactorController', 'index', array('user_id' => $user['id'])), true);
    }

    /**
     * Check 2FA
     *
     * @access public
     */
    public function check()
    {
        $user = $this->getUser();
        $this->checkCurrentUser($user);

        $values = $this->request->getValues();

        $provider = $this->authenticationManager->getPostAuthenticationProvider();
        $provider->setCode(empty($values['code']) ? '' : $values['code']);
        $provider->setSecret($user['twofactor_secret']);

        if ($provider->authenticate()) {
            $this->userSession->validatePostAuthentication();
            $this->flash->success(t('The two factor authentication code is valid.'));
            $this->response->redirect($this->helper->url->to('DashboardController', 'show'));
        } else {
            $this->flash->failure(t('The two factor authentication code is not valid.'));
            $this->response->redirect($this->helper->url->to('TwoFactorController', 'code'));
        }
    }

    /**
     * Ask the 2FA code
     *
     * @access public
     */
    public function code()
    {
        if (! isset($this->sessionStorage->twoFactorBeforeCodeCalled)) {
            $provider = $this->authenticationManager->getPostAuthenticationProvider();
            $provider->beforeCode();
            $this->sessionStorage->twoFactorBeforeCodeCalled = true;
        }

        $this->response->html($this->helper->layout->app('twofactor/check', array(
            'title' => t('Check two factor authentication code'),
        )));
    }

    /**
     * Disable 2FA for a user
     *
     * @access public
     */
    public function disable()
    {
        $user = $this->getUser();

        if ($this->request->getStringParam('disable') === 'yes') {
            $this->checkCSRFParam();

            $this->userModel->update(array(
                'id' => $user['id'],
                'twofactor_activated' => 0,
                'twofactor_secret' => '',
            ));

            $this->response->redirect($this->helper->url->to('UserViewController', 'show', array('user_id' => $user['id'])), true);
        } else {
            $this->response->html($this->helper->layout->user('twofactor/disable', array(
                'user' => $user,
            )));
        }
    }

    /**
     * Render QR Code image
     */
    public function qrcode()
    {
        if (isset($this->sessionStorage->twoFactorSecret)) {
            $user = $this->getUser();
            $provider = $this->authenticationManager->getPostAuthenticationProvider();
            $provider->setSecret($this->sessionStorage->twoFactorSecret);
            $url = $provider->getKeyUrl($user['email'] ?: $user['username']);

            if (! empty($url)) {
                PHPQRCode\QRcode::png($url, false, 'L', 6, 0);
            }
        }
    }
}
