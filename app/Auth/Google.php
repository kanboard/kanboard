<?php

namespace Auth;

use Core\Request;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Uri\UriFactory;
use OAuth\ServiceFactory;
use OAuth\Common\Http\Exception\TokenResponseException;

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
     * Authenticate a Google user
     *
     * @access public
     * @param  string  $google_id   Google unique id
     * @return boolean
     */
    public function authenticate($google_id)
    {
        $user = $this->user->getByGoogleId($google_id);

        if ($user) {

            // Create the user session
            $this->user->updateSession($user);

            // Update login history
            $this->lastLogin->create(
                self::AUTH_NAME,
                $user['id'],
                Request::getIpAddress(),
                Request::getUserAgent()
            );

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
        return $this->user->update(array(
            'id' => $user_id,
            'google_id' => $profile['id'],
            'email' => $profile['email'],
            'name' => $profile['name'],
        ));
    }

    /**
     * Get the Google service instance
     *
     * @access public
     * @return \OAuth\OAuth2\Service\Google
     */
    public function getService()
    {
        $uriFactory = new UriFactory();
        $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
        $currentUri->setQuery('controller=user&action=google');

        $storage = new Session(false);

        $credentials = new Credentials(
            GOOGLE_CLIENT_ID,
            GOOGLE_CLIENT_SECRET,
            $currentUri->getAbsoluteUri()
        );

        $serviceFactory = new ServiceFactory();

        return $serviceFactory->createService(
            'google',
            $credentials,
            $storage,
            array('userinfo_email', 'userinfo_profile')
        );
    }

    /**
     * Get the authorization URL
     *
     * @access public
     * @return \OAuth\Common\Http\Uri\Uri
     */
    public function getAuthorizationUrl()
    {
        return $this->getService()->getAuthorizationUri();
    }

    /**
     * Get Google profile information from the API
     *
     * @access public
     * @param  string    $code   Google authorization code
     * @return bool|array
     */
    public function getGoogleProfile($code)
    {
        try {

            $googleService = $this->getService();
            $googleService->requestAccessToken($code);
            return json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
        }
        catch (TokenResponseException $e) {
            return false;
        }
    }
}
