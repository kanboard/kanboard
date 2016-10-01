<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\LastLoginModel;

class LastLoginTest extends Base
{
    public function testCreate()
    {
        $lastLoginModel = new LastLoginModel($this->container);

        $this->assertTrue($lastLoginModel->create('Test1', 1, '127.0.0.1', 'My browser'));
        $this->assertTrue($lastLoginModel->create('Test2', 1, '127.0.0.1', str_repeat('Too long', 50)));
        $this->assertTrue($lastLoginModel->create('Test3', 1, '2001:0db8:0000:0000:0000:ff00:0042:8329', 'My Ipv6 browser'));

        $connections = $lastLoginModel->getAll(1);
        $this->assertCount(3, $connections);

        $this->assertEquals('Test3', $connections[0]['auth_type']);
        $this->assertEquals('2001:0db8:0000:0000:0000:ff00:0042:8329', $connections[0]['ip']);

        $this->assertEquals('Test2', $connections[1]['auth_type']);
        $this->assertEquals('127.0.0.1', $connections[1]['ip']);

        $this->assertEquals('Test1', $connections[2]['auth_type']);
        $this->assertEquals('127.0.0.1', $connections[2]['ip']);
    }

    public function testCleanup()
    {
        $lastLoginModel = new LastLoginModel($this->container);

        for ($i = 0; $i < $lastLoginModel::NB_LOGINS + 5; $i++) {
            $this->assertTrue($lastLoginModel->create('Test' . $i, 1, '127.0.0.1', 'My browser'));
        }

        $connections = $lastLoginModel->getAll(1);
        $this->assertCount(10, $connections);
        $this->assertEquals('Test14', $connections[0]['auth_type']);
        $this->assertEquals('Test5', $connections[9]['auth_type']);
    }
}
