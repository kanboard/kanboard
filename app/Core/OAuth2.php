<?php

namespace Core;

/**
 * OAuth2 client
 *
 * @package  core
 * @author   Frederic Guillot
 */
class OAuth2 extends Base
{
    private $clientId;
    private $secret;
    private $callbackUrl;
    private $authUrl;
    private $tokenUrl;
    private $scopes;
    private $tokenType;
    private $accessToken;

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

    public function getAuthorizationUrl()
    {
        $params = array(
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callbackUrl,
            'scope' => implode(' ', $this->scopes),
        );

        return $this->authUrl.'?'.http_build_query($params);
    }

    public function getAuthorizationHeader()
    {
        if ($this->tokenType === 'Bearer') {
            return 'Authorization: Bearer '.$this->accessToken;
        }

        return '';
    }

    public function getAccessToken($code)
    {
        if (empty($this->accessToken) && ! empty($code)) {

            $params = array(
                'code' => $code,
                'client_id' => $this->clientId,
                'client_secret' => $this->secret,
                'redirect_uri' => $this->callbackUrl,
                'grant_type' => 'authorization_code',
            );

            $response = json_decode($this->httpClient->postForm($this->tokenUrl, $params), true);

            $this->tokenType = isset($response['token_type']) ? $response['token_type'] : '';
            $this->accessToken = isset($response['access_token']) ? $response['access_token'] : '';
        }

        return $this->accessToken;
    }
}
