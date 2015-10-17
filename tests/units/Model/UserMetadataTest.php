<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\User;
use Kanboard\Model\UserMetadata;

class UserMetadataTest extends Base
{
    public function testOperations()
    {
        $m = new UserMetadata($this->container);
        $u = new User($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'foobar')));

        $this->assertTrue($m->save(1, array('key1' => 'value1')));
        $this->assertTrue($m->save(1, array('key1' => 'value2')));
        $this->assertTrue($m->save(2, array('key1' => 'value1')));
        $this->assertTrue($m->save(2, array('key2' => 'value2')));

        $this->assertEquals('value2', $m->get(1, 'key1'));
        $this->assertEquals('value1', $m->get(2, 'key1'));
        $this->assertEquals('', $m->get(2, 'key3'));
        $this->assertEquals('default', $m->get(2, 'key3', 'default'));

        $this->assertTrue($m->exists(2, 'key1'));
        $this->assertFalse($m->exists(2, 'key3'));

        $this->assertEquals(array('key1' => 'value2'), $m->getAll(1));
        $this->assertEquals(array('key1' => 'value1', 'key2' => 'value2'), $m->getAll(2));
    }
}
