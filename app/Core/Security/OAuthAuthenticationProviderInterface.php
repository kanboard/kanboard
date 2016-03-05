<?php

namespace Kanboard\Core\Security;

/**
 * OAuth2 Authentication Provider Interface
 *
 * @package  security
 * @author   Frederic Guillot
 */
interface OAuthAuthenticationProviderInterface extends AuthenticationProviderInterface
{
    /**
     * Get user object
     *
     * @access public
     * @return \Kanboard\Core\User\UserProviderInterface
     */
    public function getUser();

    /**
     * Unlink user
     *
     * @access public
     * @param  integer $userId
     * @return bool
     */
    public function unlink($userId);

    /**
     * Get configured OAuth2 service
     *
     * @access public
     * @return \Kanboard\Core\Http\OAuth2
     */
    public function getService();

    /**
     * Set OAuth2 code
     *
     * @access public
     * @param  string  $code
     * @return OAuthAuthenticationProviderInterface
     */
    public function setCode($code);
}
