<?php

namespace Kanboard\Auth;

use Otp\Otp;
use Otp\GoogleAuthenticator;
use Base32\Base32;
use Kanboard\Core\Base;
use Kanboard\Core\Security\PostAuthenticationProviderInterface;

/**
 * TOTP Authentication Provider
 *
 * @package  Kanboard\Auth
 * @author   Frederic Guillot
 */
class TotpAuth extends Base implements PostAuthenticationProviderInterface
{
    /**
     * User pin code
     *
     * @access protected
     * @var string
     */
    protected $code = '';

    /**
     * Private key
     *
     * @access protected
     * @var string
     */
    protected $secret = '';

    /**
     * Get authentication provider name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return t('Time-based One-time Password Algorithm');
    }

    /**
     * Authenticate the user
     *
     * @access public
     * @return boolean
     */
    public function authenticate()
    {
        $otp = new Otp;
        return $otp->checkTotp(Base32::decode($this->secret), $this->code);
    }

    /**
     * Called before to prompt the user
     *
     * @access public
     */
    public function beforeCode()
    {

    }

    /**
     * Set validation code
     *
     * @access public
     * @param  string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Generate secret
     *
     * @access public
     * @return string
     */
    public function generateSecret()
    {
        $this->secret = GoogleAuthenticator::generateRandom();
        return $this->secret;
    }

    /**
     * Set secret token
     *
     * @access public
     * @param  string  $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Get secret token
     *
     * @access public
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Get QR code url
     *
     * @access public
     * @param  string $label
     * @return string
     */
    public function getQrCodeUrl($label)
    {
        if (empty($this->secret)) {
            return '';
        }

        $options = array('issuer' => TOTP_ISSUER);
        return GoogleAuthenticator::getQrCodeUrl('totp', $label, $this->secret, null, $options);
    }

    /**
     * Get key url (empty if no url can be provided)
     *
     * @access public
     * @param  string $label
     * @return string
     */
    public function getKeyUrl($label)
    {
        if (empty($this->secret)) {
            return '';
        }

        $options = array('issuer' => TOTP_ISSUER);
        return GoogleAuthenticator::getKeyUri('totp', $label, $this->secret, null, $options);
    }
}
