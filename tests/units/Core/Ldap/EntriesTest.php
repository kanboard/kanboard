<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Ldap\Entries;

class EntriesTest extends Base
{
    private $entries = array(
        'count' => 2,
        0 => array(
            'cn' => array(
                'count' => 1,
                0 => 'Kanboard Other Group',
            ),
            0 => 'cn',
            'count' => 1,
            'dn' => 'CN=Kanboard Other Group,CN=Users,DC=kanboard,DC=local',
        ),
        1 => array(
            'cn' => array(
                'count' => 1,
                0 => 'Kanboard Users',
            ),
            0 => 'cn',
            'count' => 1,
            'dn' => 'CN=Kanboard Users,CN=Users,DC=kanboard,DC=local',
        ),
    );

    public function testGetAll()
    {
        $entries = new Entries(array());
        $this->assertEmpty($entries->getAll());

        $entries = new Entries($this->entries);
        $result = $entries->getAll();
        $this->assertCount(2, $result);
        $this->assertInstanceOf('Kanboard\Core\Ldap\Entry', $result[0]);
        $this->assertEquals('CN=Kanboard Users,CN=Users,DC=kanboard,DC=local', $result[1]->getDn());
        $this->assertEquals('Kanboard Users', $result[1]->getFirstValue('cn'));
    }

    public function testGetFirst()
    {
        $entries = new Entries(array());
        $this->assertEquals('', $entries->getFirstEntry()->getDn());

        $entries = new Entries($this->entries);
        $result = $entries->getFirstEntry();
        $this->assertInstanceOf('Kanboard\Core\Ldap\Entry', $result);
        $this->assertEquals('CN=Kanboard Other Group,CN=Users,DC=kanboard,DC=local', $result->getDn());
        $this->assertEquals('Kanboard Other Group', $result->getFirstValue('cn'));
    }
}
