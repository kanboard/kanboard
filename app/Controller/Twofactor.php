<?php

namespace Kanboard\Controller;

use Otp\Otp;
use Otp\GoogleAuthenticator;
use Base32\Base32;

/**
 * Two Factor Auth controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Twofactor extends User
{
    /**
     * Only the current user can access to 2FA settings
     *
     * @access private
     */
    private function checkCurrentUser(array $user)
    {
        if ($user['id'] != $this->userSession->getId()) {
            $this->forbidden();
        }
    }

    /**
     * Index
     *
     * @access public
     */
    public function index()
    {
        $user = $this->getUser();
        $this->checkCurrentUser($user);

        $label = $user['email'] ?: $user['username'];

        $this->response->html($this->layout('twofactor/index', array(
            'user' => $user,
            'qrcode_url' => $user['twofactor_activated'] == 1 ? GoogleAuthenticator::getQrCodeUrl('totp', $label, $user['twofactor_secret']) : '',
            'key_url' => $user['twofactor_activated'] == 1 ? GoogleAuthenticator::getKeyUri('totp', $label, $user['twofactor_secret']) : '',
        )));
    }

    /**
     * Enable/disable 2FA
     *
     * @access public
     */
    public function save()
    {
        $user = $this->getUser();
        $this->checkCurrentUser($user);

        $values = $this->request->getValues();

        if (isset($values['twofactor_activated']) && $values['twofactor_activated'] == 1) {
            $this->user->update(array(
                'id' => $user['id'],
                'twofactor_activated' => 1,
                'twofactor_secret' => GoogleAuthenticator::generateRandom(),
            ));
        } else {
            $this->user->update(array(
                'id' => $user['id'],
                'twofactor_activated' => 0,
                'twofactor_secret' => '',
            ));
        }

        // Allow the user to test or disable the feature
        $_SESSION['user']['twofactor_activated'] = false;

        $this->session->flash(t('User updated successfully.'));
        $this->response->redirect($this->helper->url->to('twofactor', 'index', array('user_id' => $user['id'])));
    }

    /**
     * Test 2FA
     *
     * @access public
     */
    public function test()
    {
        $user = $this->getUser();
        $this->checkCurrentUser($user);

        $otp = new Otp;
        $values = $this->request->getValues();

        if (! empty($values['code']) && $otp->checkTotp(Base32::decode($user['twofactor_secret']), $values['code'])) {
            $this->session->flash(t('The two factor authentication code is valid.'));
        } else {
            $this->session->flashError(t('The two factor authentication code is not valid.'));
        }

        $this->response->redirect($this->helper->url->to('twofactor', 'index', array('user_id' => $user['id'])));
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

        $otp = new Otp;
        $values = $this->request->getValues();

        if (! empty($values['code']) && $otp->checkTotp(Base32::decode($user['twofactor_secret']), $values['code'])) {
            $this->session['2fa_validated'] = true;
            $this->session->flash(t('The two factor authentication code is valid.'));
            $this->response->redirect($this->helper->url->to('app', 'index'));
        } else {
            $this->session->flashError(t('The two factor authentication code is not valid.'));
            $this->response->redirect($this->helper->url->to('twofactor', 'code'));
        }
    }

    /**
     * Ask the 2FA code
     *
     * @access public
     */
    public function code()
    {
        $this->response->html($this->template->layout('twofactor/check', array(
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

            $this->user->update(array(
                'id' => $user['id'],
                'twofactor_activated' => 0,
                'twofactor_secret' => '',
            ));

            $this->response->redirect($this->helper->url->to('user', 'show', array('user_id' => $user['id'])));
        }

        $this->response->html($this->layout('twofactor/disable', array(
            'user' => $user,
        )));
    }
}
