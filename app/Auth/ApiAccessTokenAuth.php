<?php

namespace Kanboard\Auth;

use Kanboard\Core\Base;
use Kanboard\Core\Security\PasswordAuthenticationProviderInterface;
use Kanboard\Model\UserModel;
use Kanboard\User\DatabaseUserProvider;

/**
 * API Access Token Authentication Provider
 *
 * @package  Kanboard\Auth
 * @author   Frederic Guillot
 */
class ApiAccessTokenAuth extends Base implements PasswordAuthenticationProviderInterface
{
    /**
     * User properties
     *
     * @access protected
     * @var array
     */
    protected $userInfo = array();

    /**
     * Username
     *
     * @access protected
     * @var string
     */
    protected $username = '';

    /**
     * Password
     *
     * @access protected
     * @var string
     */
    protected $password = '';

    /**
     * Get authentication provider name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'API Access Token';
    }

    /**
     * Authenticate the user
     *
     * @access public
     * @return boolean
     */
    public function authenticate()
    {
        if (! isset($this->sessionStorage->scope) ||  $this->sessionStorage->scope !== 'API') {
            $this->logger->debug(__METHOD__.': Authentication provider skipped because invalid scope');
            return false;
        }

        $user = $this->db
            ->table(UserModel::TABLE)
            ->columns('id', 'password')
            ->eq('username', $this->username)
            ->eq('api_access_token', $this->password)
            ->notNull('api_access_token')
            ->eq('is_active', 1)
            ->findOne();

        if (! empty($user)) {
            $this->userInfo = $user;
            return true;
        }

        return false;
    }

    /**
     * Get user object
     *
     * @access public
     * @return \Kanboard\User\DatabaseUserProvider
     */
    public function getUser()
    {
        if (empty($this->userInfo)) {
            return null;
        }

        return new DatabaseUserProvider($this->userInfo);
    }

    /**
     * Set username
     *
     * @access public
     * @param  string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Set password
     *
     * @access public
     * @param  string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}
