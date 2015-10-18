<?php

namespace Kanboard\Auth;

use Kanboard\Core\Base;
use Kanboard\Event\AuthEvent;

/**
 * Google backend
 *
 * @package  auth
 * @author   Frederic Guillot
 */
class Google extends Base
{
    /**
     * Backend name
     *
     * @var string
     */
    const AUTH_NAME = 'Google';

    /**
     * OAuth2 instance
     *
     * @access private
     * @var \Kanboard\Core\OAuth2
     */
    private $service;

    /**
     * Authenticate a Google user
     *
     * @access public
     * @param  string  $google_id   Google unique id
     * @return boolean
     */
    public function authenticate($google_id)
    {
        $user = $this->user->getByGoogleId($google_id);

        if (! empty($user)) {
            $this->userSession->refresh($user);
            $this->container['dispatcher']->dispatch('auth.success', new AuthEvent(self::AUTH_NAME, $user['id']));
            return true;
        }

        return false;
    }

    /**
     * Unlink a Google account for a given user
     *
     * @access public
     * @param  integer   $user_id    User id
     * @return boolean
     */
    public function unlink($user_id)
    {
        return $this->user->update(array(
            'id' => $user_id,
            'google_id' => '',
        ));
    }

    /**
     * Update the user table based on the Google profile information
     *
     * @access public
     * @param  integer   $user_id    User id
     * @param  array     $profile    Google profile
     * @return boolean
     */
    public function updateUser($user_id, array $profile)
    {
        $user = $this->user->getById($user_id);

        return $this->user->update(array(
            'id' => $user_id,
            'google_id' => $profile['id'],
            'email' => empty($user['email']) ? $profile['email'] : $user['email'],
            'name' => empty($user['name']) ? $profile['name'] : $user['name'],
        ));
    }

    /**
     * Get OAuth2 configured service
     *
     * @access public
     * @return KanboardCore\OAuth2
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->service = $this->oauth->createService(
                GOOGLE_CLIENT_ID,
                GOOGLE_CLIENT_SECRET,
                $this->helper->url->to('oauth', 'google', array(), '', true),
                'https://accounts.google.com/o/oauth2/auth',
                'https://accounts.google.com/o/oauth2/token',
                array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile')
            );
        }

        return $this->service;
    }

    /**
     * Get Google profile
     *
     * @access public
     * @param  string  $code
     * @return array
     */
    public function getProfile($code)
    {
        $this->getService()->getAccessToken($code);

        return $this->httpClient->getJson(
            'https://www.googleapis.com/oauth2/v1/userinfo',
            array($this->getService()->getAuthorizationHeader())
        );
    }
}
