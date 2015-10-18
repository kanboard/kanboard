<?php

namespace Kanboard\Auth;

use Kanboard\Core\Base;
use Kanboard\Event\AuthEvent;

/**
 * Github backend
 *
 * @package auth
 */
class Github extends Base
{
    /**
     * Backend name
     *
     * @var string
     */
    const AUTH_NAME = 'Github';

    /**
     * OAuth2 instance
     *
     * @access private
     * @var \Kanboard\Core\OAuth2
     */
    private $service;

    /**
     * Authenticate a Github user
     *
     * @access public
     * @param  string  $github_id   Github user id
     * @return boolean
     */
    public function authenticate($github_id)
    {
        $user = $this->user->getByGithubId($github_id);

        if (! empty($user)) {
            $this->userSession->refresh($user);
            $this->container['dispatcher']->dispatch('auth.success', new AuthEvent(self::AUTH_NAME, $user['id']));
            return true;
        }

        return false;
    }

    /**
     * Unlink a Github account for a given user
     *
     * @access public
     * @param  integer   $user_id    User id
     * @return boolean
     */
    public function unlink($user_id)
    {
        return $this->user->update(array(
            'id' => $user_id,
            'github_id' => '',
        ));
    }

    /**
     * Update the user table based on the Github profile information
     *
     * @access public
     * @param  integer   $user_id    User id
     * @param  array     $profile    Github profile
     * @return boolean
     */
    public function updateUser($user_id, array $profile)
    {
        $user = $this->user->getById($user_id);

        return $this->user->update(array(
            'id' => $user_id,
            'github_id' => $profile['id'],
            'email' => empty($user['email']) ? $profile['email'] : $user['email'],
            'name' => empty($user['name']) ? $profile['name'] : $user['name'],
        ));
    }

    /**
     * Get OAuth2 configured service
     *
     * @access public
     * @return Kanboard\Core\OAuth2
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->service = $this->oauth->createService(
                GITHUB_CLIENT_ID,
                GITHUB_CLIENT_SECRET,
                $this->helper->url->to('oauth', 'github', array(), '', true),
                GITHUB_OAUTH_AUTHORIZE_URL,
                GITHUB_OAUTH_TOKEN_URL,
                array()
            );
        }

        return $this->service;
    }

    /**
     * Get Github profile
     *
     * @access public
     * @param  string  $code
     * @return array
     */
    public function getProfile($code)
    {
        $this->getService()->getAccessToken($code);

        return $this->httpClient->getJson(
            GITHUB_API_URL.'user',
            array($this->getService()->getAuthorizationHeader())
        );
    }
}
