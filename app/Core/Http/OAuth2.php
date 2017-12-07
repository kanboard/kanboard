<?php

namespace Kanboard\Core\Http;

use Kanboard\Core\Base;

/**
 * OAuth2 Client
 *
 * @package  http
 * @author   Frederic Guillot
 */
class OAuth2 extends Base
{
    protected $clientId;
    protected $secret;
    protected $callbackUrl;
    protected $authUrl;
    protected $tokenUrl;
    protected $scopes;
    protected $tokenType;
    protected $accessToken;

    /**
     * Create OAuth2 service
     *
     * @access public
     * @param  string  $clientId
     * @param  string  $secret
     * @param  string  $callbackUrl
     * @param  string  $authUrl
     * @param  string  $tokenUrl
     * @param  array   $scopes
     * @return OAuth2
     */
    public function createService($clientId, $secret, $callbackUrl, $authUrl, $tokenUrl, array $scopes)
    {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->callbackUrl = $callbackUrl;
        $this->authUrl = $authUrl;
        $this->tokenUrl = $tokenUrl;
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * Generate OAuth2 state and return the token value
     *
     * @access public
     * @return string
     */
    public function getState()
    {
        if (! session_exists('oauthState')) {
            session_set('oauthState', $this->token->getToken());
        }

        return session_get('oauthState');
    }

    /**
     * Check the validity of the state (CSRF token)
     *
     * @access public
     * @param  string $state
     * @return bool
     */
    public function isValidateState($state)
    {
        return $state === $this->getState();
    }

    /**
     * Get authorization url
     *
     * @access public
     * @return string
     */
    public function getAuthorizationUrl()
    {
        $params = array(
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callbackUrl,
            'scope' => implode(' ', $this->scopes),
            'state' => $this->getState(),
        );

        return $this->authUrl.'?'.http_build_query($params);
    }

    /**
     * Get authorization header
     *
     * @access public
     * @return string
     */
    public function getAuthorizationHeader()
    {
        if (strtolower($this->tokenType) === 'bearer') {
            return 'Authorization: Bearer '.$this->accessToken;
        }

        return '';
    }

    /**
     * Get access token
     *
     * @access public
     * @param  string  $code
     * @return string
     */
    public function getAccessToken($code)
    {
        if (empty($this->accessToken) && ! empty($code)) {
            $params = array(
                'code' => $code,
                'client_id' => $this->clientId,
                'client_secret' => $this->secret,
                'redirect_uri' => $this->callbackUrl,
                'grant_type' => 'authorization_code',
                'state' => $this->getState(),
            );

            $response = json_decode($this->httpClient->postForm($this->tokenUrl, $params, array('Accept: application/json')), true);

            $this->tokenType = isset($response['token_type']) ? $response['token_type'] : '';
            $this->accessToken = isset($response['access_token']) ? $response['access_token'] : '';
        }

        return $this->accessToken;
    }

    /**
     * Set access token
     *
     * @access public
     * @param  string  $token
     * @param  string  $type
     * @return $this
     */
    public function setAccessToken($token, $type = 'bearer')
    {
        $this->accessToken = $token;
        $this->tokenType = $type;
        return $this;
    }
}
