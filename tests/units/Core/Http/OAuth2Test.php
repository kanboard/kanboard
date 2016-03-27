<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Http\OAuth2;

class OAuth2Test extends Base
{
    public function testAuthUrl()
    {
        $oauth = new OAuth2($this->container);
        $oauth->createService('A', 'B', 'C', 'D', 'E', array('f', 'g'));
        $state = $oauth->getState();
        $this->assertEquals('D?response_type=code&client_id=A&redirect_uri=C&scope=f+g&state='.$state, $oauth->getAuthorizationUrl());
    }

    public function testAuthHeader()
    {
        $oauth = new OAuth2($this->container);
        $oauth->createService('A', 'B', 'C', 'D', 'E', array('f', 'g'));

        $oauth->setAccessToken('foobar', 'BeaRer');
        $this->assertEquals('Authorization: Bearer foobar', $oauth->getAuthorizationHeader());

        $oauth->setAccessToken('foobar', 'unknown');
        $this->assertEquals('', $oauth->getAuthorizationHeader());
    }

    public function testAccessToken()
    {
        $oauth = new OAuth2($this->container);

        $params = array(
            'code' => 'something',
            'client_id' => 'A',
            'client_secret' => 'B',
            'redirect_uri' => 'C',
            'grant_type' => 'authorization_code',
            'state' => $oauth->getState(),
        );

        $response = json_encode(array(
            'token_type' => 'bearer',
            'access_token' => 'plop',
        ));

        $this->container['httpClient']
            ->expects($this->once())
            ->method('postForm')
            ->with('E', $params, array('Accept: application/json'))
            ->will($this->returnValue($response));

        $oauth->createService('A', 'B', 'C', 'D', 'E', array('f', 'g'));
        $oauth->getAccessToken('something');
    }
}
