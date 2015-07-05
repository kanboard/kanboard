<?php

require_once __DIR__.'/Base.php';

use Helper\User;

class UserHelperTest extends Base
{
    public function testInitials()
    {
        $h = new User($this->container);

        $this->assertEquals('CN', $h->getInitials('chuck norris'));
        $this->assertEquals('A', $h->getInitials('admin'));
    }
}
