<?php

namespace KanboardTests\units\Core\Ldap;

use KanboardTests\units\Base;
use Kanboard\Core\Ldap\Group;
use Kanboard\Core\Ldap\Entries;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class LdapGroupTest extends Base
{
    private $query;
    private $client;
    private $group;

    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists('ldap_connect') || ! function_exists('ldap_escape')) {
            $this->markTestSkipped('The PHP LDAP extension is required');
        }

        $this->client = $this
            ->getMockBuilder('\Kanboard\Core\Ldap\Client')
            ->onlyMethods(array(
                'getConnection',
            ))
            ->getMock();

        $this->query = $this
            ->getMockBuilder('\Kanboard\Core\Ldap\Query')
            ->setConstructorArgs(array($this->client))
            ->onlyMethods(array(
                'execute',
                'hasResult',
                'getEntries',
            ))
            ->getMock();

        $this->group = $this
            ->getMockBuilder('\Kanboard\Core\Ldap\Group')
            ->setConstructorArgs(array($this->query))
            ->onlyMethods(array(
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
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

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
            ->willReturn(true);

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn($entries);

        $this->group
            ->method('getAttributeName')
            ->willReturn('cn');

        $this->group
            ->method('getBaseDn')
            ->willReturn('CN=Users,DC=kanboard,DC=local');

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
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

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
            ->willReturn(false);

        $this->query
            ->expects($this->never())
            ->method('getEntries');

        $this->group
            ->method('getAttributeName')
            ->willReturn('cn');

        $this->group
            ->method('getBaseDn')
            ->willReturn('CN=Users,DC=kanboard,DC=local');

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
