<?php

namespace Kanboard\Auth;

use Kanboard\Core\Base;
use Kanboard\Core\Security\OAuthAuthenticationProviderInterface;
use Kanboard\User\GithubUserProvider;

/**
 * Github Authentication Provider
 *
 * @package  auth
 * @author   Frederic Guillot
 */
class GithubAuth extends Base implements OAuthAuthenticationProviderInterface
{
    /**
     * User properties
     *
     * @access private
     * @var \Kanboard\User\GithubUserProvider
     */
    private $userInfo = null;

    /**
     * OAuth2 instance
     *
     * @access private
     * @var \Kanboard\Core\Http\OAuth2
     */
    private $service;

    /**
     * OAuth2 code
     *
     * @access private
     * @var string
     */
    private $code = '';

    /**
     * Get authentication provider name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'Github';
    }

    /**
     * Authenticate the user
     *
     * @access public
     * @return boolean
     */
    public function authenticate()
    {
        $profile = $this->getProfile();

        if (! empty($profile)) {
            $this->userInfo = new GithubUserProvider($profile);
            return true;
        }

        return false;
    }

    /**
     * Set Code
     *
     * @access public
     * @param  string  $code
     * @return GithubAuth
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get user object
     *
     * @access public
     * @return GithubUserProvider
     */
    public function getUser()
    {
        return $this->userInfo;
    }

    /**
     * Get configured OAuth2 service
     *
     * @access public
     * @return \Kanboard\Core\Http\OAuth2
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
     * @return array
     */
    public function getProfile()
    {
        $this->getService()->getAccessToken($this->code);

        return $this->httpClient->getJson(
            GITHUB_API_URL.'user',
            array($this->getService()->getAuthorizationHeader())
        );
    }

    /**
     * Unlink user
     *
     * @access public
     * @param  integer $userId
     * @return bool
     */
    public function unlink($userId)
    {
        return $this->user->update(array('id' => $userId, 'github_id' => ''));
    }
}
