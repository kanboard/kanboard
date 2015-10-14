<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\OAuth2;

class OAuth2Test extends Base
{
    public function testAuthUrl()
    {
        $oauth = new OAuth2($this->container);
        $oauth->createService('A', 'B', 'C', 'D', 'E', array('f', 'g'));
        $this->assertEquals('D?response_type=code&client_id=A&redirect_uri=C&scope=f+g', $oauth->getAuthorizationUrl());
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
        $oauth->createService('A', 'B', 'C', 'D', 'E', array('f', 'g'));
        $oauth->getAccessToken('something');

        $data = $this->container['httpClient']->getData();
        $this->assertEquals('something', $data['code']);
        $this->assertEquals('A', $data['client_id']);
        $this->assertEquals('B', $data['client_secret']);
        $this->assertEquals('C', $data['redirect_uri']);
        $this->assertEquals('authorization_code', $data['grant_type']);

        $this->assertEquals('E', $this->container['httpClient']->getUrl());
    }
}
