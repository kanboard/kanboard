<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\User\Avatar\LetterAvatarProvider;

class LetterAvatarProviderTest extends Base
{
    public function testGetBackgroundColor()
    {
        $provider = new LetterAvatarProvider($this->container);
        $rgb = $provider->getBackgroundColor('Test');
        $this->assertEquals(array(107, 83, 172), $rgb);
    }

    public function testIsActive()
    {
        $provider = new LetterAvatarProvider($this->container);
        $this->assertTrue($provider->isActive(array()));
    }

    public function testRenderWithFullName()
    {
        $provider = new LetterAvatarProvider($this->container);
        $user = array('id' => 123, 'name' => 'Kanboard Admin', 'username' => 'bob', 'email' => '');
        $expected = '<div class="avatar-letter" style="background-color: rgb(131, 224, 108)" title="Kanboard Admin">KA</div>';
        $this->assertEquals($expected, $provider->render($user, 48));
    }

    public function testRenderWithUsername()
    {
        $provider = new LetterAvatarProvider($this->container);
        $user = array('id' => 123, 'name' => '', 'username' => 'admin', 'email' => '');
        $expected = '<div class="avatar-letter" style="background-color: rgb(134, 45, 132)" title="admin">A</div>';
        $this->assertEquals($expected, $provider->render($user, 48));
    }
}
