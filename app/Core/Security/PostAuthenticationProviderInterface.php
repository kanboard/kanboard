<?php

namespace Kanboard\Core\Security;

/**
 * Post Authentication Provider Interface
 *
 * @package  security
 * @author   Frederic Guillot
 */
interface PostAuthenticationProviderInterface extends AuthenticationProviderInterface
{
    /**
     * Called only one time before to prompt the user for pin code
     *
     * @access public
     */
    public function beforeCode();

    /**
     * Set user pin-code
     *
     * @access public
     * @param  string $code
     */
    public function setCode($code);

    /**
     * Generate secret if necessary
     *
     * @access public
     * @return string
     */
    public function generateSecret();

    /**
     * Set secret token (fetched from user profile)
     *
     * @access public
     * @param  string  $secret
     */
    public function setSecret($secret);

    /**
     * Get secret token (will be saved in user profile)
     *
     * @access public
     * @return string
     */
    public function getSecret();

    /**
     * Get key url (empty if no url can be provided)
     *
     * @access public
     * @param  string $label
     * @return string
     */
    public function getKeyUrl($label);
}
