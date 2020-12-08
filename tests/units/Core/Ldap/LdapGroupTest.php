<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Ldap\Group;
use Kanboard\Core\Ldap\Entries;

class LdapGroupTest extends Base
{
    private $query;
    private $client;
    private $group;

    public function setUp()
    {
        parent::setUp();

        $this->client = $this
            ->getMockBuilder('\Kanboard\Core\Ldap\Client')
            ->setMethods(array(
                'getConnection',
            ))
            ->getMock();

        $this->query = $this
            ->getMockBuilder('\Kanboard\Core\Ldap\Query')
            ->setConstructorArgs(array($this->client))
            ->setMethods(array(
                'execute',
                'hasResult',
                'getEntries',
            ))
            ->getMock();

        $this->group = $this
            ->getMockBuilder('\Kanboard\Core\Ldap\Group')
            ->setConstructorArgs(array($this->query))
            ->setMethods(array(
                'getAttributeName',
                'getBaseDn',
            ))
            ->getMock();
    }

    public function testGetGroups()
    {
        $entries = new Entries(array(
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
        ));

        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('CN=Users,DC=kanboard,DC=local'),
                $this->equalTo('(&(objectClass=group)(sAMAccountName=Kanboard*))')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->group
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('cn'));

        $this->group
            ->expects($this->any())
            ->method('getBaseDn')
            ->will($this->returnValue('CN=Users,DC=kanboard,DC=local'));

        $groups = $this->group->find('(&(objectClass=group)(sAMAccountName=Kanboard*))');
        $this->assertCount(2, $groups);
        $this->assertInstanceOf('Kanboard\Group\LdapGroupProvider', $groups[0]);
        $this->assertInstanceOf('Kanboard\Group\LdapGroupProvider', $groups[1]);
        $this->assertEquals('Kanboard Other Group', $groups[0]->getName());
        $this->assertEquals('Kanboard Users', $groups[1]->getName());
        $this->assertEquals('CN=Kanboard Other Group,CN=Users,DC=kanboard,DC=local', $groups[0]->getExternalId());
        $this->assertEquals('CN=Kanboard Users,CN=Users,DC=kanboard,DC=local', $groups[1]->getExternalId());
    }

    public function testGetGroupsWithNoResult()
    {
        $entries = new Entries(array());

        $this->client
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('CN=Users,DC=kanboard,DC=local'),
                $this->equalTo('(&(objectClass=group)(sAMAccountName=Kanboard*))')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->will($this->returnValue(false));

        $this->query
            ->expects($this->never())
            ->method('getEntries');

        $this->group
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('cn'));

        $this->group
            ->expects($this->any())
            ->method('getBaseDn')
            ->will($this->returnValue('CN=Users,DC=kanboard,DC=local'));

        $groups = $this->group->find('(&(objectClass=group)(sAMAccountName=Kanboard*))');
        $this->assertCount(0, $groups);
    }

    public function testGetBaseDnNotConfigured()
    {
        $this->expectException('\LogicException');

        $group = new Group($this->query);
        $group->getBaseDn();
    }
}
