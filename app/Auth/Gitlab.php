<?php

namespace Kanboard\Auth;

use Kanboard\Core\Base;
use Kanboard\Event\AuthEvent;

/**
 * Gitlab backend
 *
 * @package auth
 */
class Gitlab extends Base
{
    /**
     * Backend name
     *
     * @var string
     */
    const AUTH_NAME = 'Gitlab';

    /**
     * OAuth2 instance
     *
     * @access private
     * @var \Kanboard\Core\OAuth2
     */
    private $service;

    /**
     * Authenticate a Gitlab user
     *
     * @access public
     * @param  string  $gitlab_id   Gitlab user id
     * @return boolean
     */
    public function authenticate($gitlab_id)
    {
        $user = $this->user->getByGitlabId($gitlab_id);

        if (! empty($user)) {
            $this->userSession->refresh($user);
            $this->container['dispatcher']->dispatch('auth.success', new AuthEvent(self::AUTH_NAME, $user['id']));
            return true;
        }

        return false;
    }

    /**
     * Unlink a Gitlab account for a given user
     *
     * @access public
     * @param  integer   $user_id    User id
     * @return boolean
     */
    public function unlink($user_id)
    {
        return $this->user->update(array(
            'id' => $user_id,
            'gitlab_id' => '',
        ));
    }

    /**
     * Update the user table based on the Gitlab profile information
     *
     * @access public
     * @param  integer   $user_id    User id
     * @param  array     $profile    Gitlab profile
     * @return boolean
     */
    public function updateUser($user_id, array $profile)
    {
        $user = $this->user->getById($user_id);

        return $this->user->update(array(
            'id' => $user_id,
            'gitlab_id' => $profile['id'],
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
                GITLAB_CLIENT_ID,
                GITLAB_CLIENT_SECRET,
                $this->helper->url->to('oauth', 'gitlab', array(), '', true),
                GITLAB_OAUTH_AUTHORIZE_URL,
                GITLAB_OAUTH_TOKEN_URL,
                array()
            );
        }

        return $this->service;
    }

    /**
     * Get Gitlab profile
     *
     * @access public
     * @param  string  $code
     * @return array
     */
    public function getProfile($code)
    {
        $this->getService()->getAccessToken($code);

        return $this->httpClient->getJson(
            GITLAB_API_URL.'user',
            array($this->getService()->getAuthorizationHeader())
        );
    }
}
