<?php

namespace KanboardTests\units\Core\Ldap;

use KanboardTests\units\Base;
use Kanboard\Core\Ldap\Query;
use Kanboard\Core\Ldap\User;
use Kanboard\Core\Ldap\Entries;
use Kanboard\Core\Security\Role;
use Kanboard\Group\LdapGroupProvider;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class LdapUserTest extends Base
{
    private $query;
    private $client;
    private $user;
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
            ->setConstructorArgs(array(new Query($this->client)))
            ->onlyMethods(array(
                'find',
            ))
            ->getMock();

        $this->user = $this
            ->getMockBuilder('\Kanboard\Core\Ldap\User')
            ->setConstructorArgs(array($this->query, $this->group))
            ->onlyMethods(array(
                'getAttributeUsername',
                'getAttributeEmail',
                'getAttributeName',
                'getAttributeGroup',
                'getAttributePhoto',
                'getGroupUserFilter',
                'getGroupAdminDn',
                'getGroupManagerDn',
                'getBaseDn',
            ))
            ->getMock();
    }

    public function testGetUserWithNoGroupConfigured()
    {
        $entries = new Entries(array(
            'count' => 1,
            0 => array(
                'count' => 2,
                'dn' => 'uid=my_ldap_user,ou=People,dc=kanboard,dc=local',
                'displayname' => array(
                    'count' => 1,
                    0 => 'My LDAP user',
                ),
                'mail' => array(
                    'count' => 2,
                    0 => 'user1@localhost',
                    1 => 'user2@localhost',
                ),
                'samaccountname' => array(
                    'count' => 1,
                    0 => 'my_ldap_user',
                ),
                0 => 'displayname',
                1 => 'mail',
                2 => 'samaccountname',
            )
        ));

        $this->client
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->willReturn(true);

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn($entries);

        $this->user
            ->method('getAttributeUsername')
            ->willReturn('samaccountname');

        $this->user
            ->method('getAttributeName')
            ->willReturn('displayname');

        $this->user
            ->method('getAttributeEmail')
            ->willReturn('mail');

        $this->user
            ->method('getBaseDn')
            ->willReturn('ou=People,dc=kanboard,dc=local');

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Kanboard\User\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=People,dc=kanboard,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(null, $user->getRole());
        $this->assertSame('', $user->getPhoto());
        $this->assertEquals(array(), $user->getExternalGroupIds());
        $this->assertEquals(array('is_ldap_user' => 1), $user->getExtraAttributes());
    }

    public function testGetUserWithPhoto()
    {
        $entries = new Entries(array(
            'count' => 1,
            0 => array(
                'count' => 2,
                'dn' => 'uid=my_ldap_user,ou=People,dc=kanboard,dc=local',
                'displayname' => array(
                    'count' => 1,
                    0 => 'My LDAP user',
                ),
                'mail' => array(
                    'count' => 2,
                    0 => 'user1@localhost',
                    1 => 'user2@localhost',
                ),
                'samaccountname' => array(
                    'count' => 1,
                    0 => 'my_ldap_user',
                ),
                'jpegPhoto' => array(
                    'count' => 1,
                    0 => 'my photo',
                ),
                0 => 'displayname',
                1 => 'mail',
                2 => 'samaccountname',
            )
        ));

        $this->client
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->willReturn(true);

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn($entries);

        $this->user
            ->method('getAttributePhoto')
            ->willReturn('jpegPhoto');

        $this->user
            ->method('getBaseDn')
            ->willReturn('ou=People,dc=kanboard,dc=local');

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Kanboard\User\LdapUserProvider', $user);
        $this->assertEquals('my photo', $user->getPhoto());
    }

    public function testGetUserWithAdminRole()
    {
        $entries = new Entries(array(
            'count' => 1,
            0 => array(
                'count' => 2,
                'dn' => 'uid=my_ldap_user,ou=People,dc=kanboard,dc=local',
                'displayname' => array(
                    'count' => 1,
                    0 => 'My LDAP user',
                ),
                'mail' => array(
                    'count' => 2,
                    0 => 'user1@localhost',
                    1 => 'user2@localhost',
                ),
                'samaccountname' => array(
                    'count' => 1,
                    0 => 'my_ldap_user',
                ),
                'memberof' => array(
                    'count' => 3,
                    0 => 'CN=Kanboard-Users,CN=Users,DC=kanboard,DC=local',
                    1 => 'CN=Kanboard-Managers,CN=Users,DC=kanboard,DC=local',
                    2 => 'CN=Kanboard-Admins,CN=Users,DC=kanboard,DC=local',
                ),
                0 => 'displayname',
                1 => 'mail',
                2 => 'samaccountname',
                3 => 'memberof',
            )
        ));

        $this->client
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->willReturn(true);

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn($entries);

        $this->user
            ->method('getAttributeUsername')
            ->willReturn('samaccountname');

        $this->user
            ->method('getAttributeName')
            ->willReturn('displayname');

        $this->user
            ->method('getAttributeEmail')
            ->willReturn('mail');

        $this->user
            ->method('getAttributeGroup')
            ->willReturn('memberof');

        $this->user
            ->method('getGroupAdminDn')
            ->willReturn('CN=Kanboard-Admins,CN=Users,DC=kanboard,DC=local');

        $this->user
            ->method('getBaseDn')
            ->willReturn('ou=People,dc=kanboard,dc=local');

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Kanboard\User\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=People,dc=kanboard,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(Role::APP_ADMIN, $user->getRole());
        $this->assertEquals(
            array(
                'CN=Kanboard-Users,CN=Users,DC=kanboard,DC=local',
                'CN=Kanboard-Managers,CN=Users,DC=kanboard,DC=local',
                'CN=Kanboard-Admins,CN=Users,DC=kanboard,DC=local',
            ),
            $user->getExternalGroupIds()
        );
        $this->assertEquals(array('is_ldap_user' => 1), $user->getExtraAttributes());
    }

    public function testGetUserWithManagerRole()
    {
        $entries = new Entries(array(
            'count' => 1,
            0 => array(
                'count' => 2,
                'dn' => 'uid=my_ldap_user,ou=People,dc=kanboard,dc=local',
                'displayname' => array(
                    'count' => 1,
                    0 => 'My LDAP user',
                ),
                'mail' => array(
                    'count' => 2,
                    0 => 'user1@localhost',
                    1 => 'user2@localhost',
                ),
                'samaccountname' => array(
                    'count' => 1,
                    0 => 'my_ldap_user',
                ),
                'memberof' => array(
                    'count' => 2,
                    0 => 'CN=Kanboard-Users,CN=Users,DC=kanboard,DC=local',
                    1 => 'CN=Kanboard-Managers,CN=Users,DC=kanboard,DC=local',
                ),
                0 => 'displayname',
                1 => 'mail',
                2 => 'samaccountname',
                3 => 'memberof',
            )
        ));

        $this->client
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->willReturn(true);

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn($entries);

        $this->user
            ->method('getAttributeUsername')
            ->willReturn('samaccountname');

        $this->user
            ->method('getAttributeName')
            ->willReturn('displayname');

        $this->user
            ->method('getAttributeEmail')
            ->willReturn('mail');

        $this->user
            ->method('getAttributeGroup')
            ->willReturn('memberof');

        $this->user
            ->method('getGroupManagerDn')
            ->willReturn('CN=Kanboard-Managers,CN=Users,DC=kanboard,DC=local');

        $this->user
            ->method('getBaseDn')
            ->willReturn('ou=People,dc=kanboard,dc=local');

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Kanboard\User\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=People,dc=kanboard,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(Role::APP_MANAGER, $user->getRole());
        $this->assertEquals(array('CN=Kanboard-Users,CN=Users,DC=kanboard,DC=local', 'CN=Kanboard-Managers,CN=Users,DC=kanboard,DC=local'), $user->getExternalGroupIds());
        $this->assertEquals(array('is_ldap_user' => 1), $user->getExtraAttributes());
    }

    public function testGetUserNotFound()
    {
        $this->client
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->willReturn(false);

        $this->query
            ->expects($this->never())
            ->method('getEntries');

        $this->user
            ->method('getAttributeUsername')
            ->willReturn('samaccountname');

        $this->user
            ->method('getAttributeName')
            ->willReturn('displayname');

        $this->user
            ->method('getAttributeEmail')
            ->willReturn('mail');

        $this->user
            ->method('getBaseDn')
            ->willReturn('ou=People,dc=kanboard,dc=local');

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertEquals(null, $user);
    }

    public function testGetUserWithAdminRoleAndPosixGroups()
    {
        $entries = new Entries(array(
            'count' => 1,
            0 => array(
                'count' => 2,
                'dn' => 'uid=my_ldap_user,ou=Users,dc=kanboard,dc=local',
                'cn' => array(
                    'count' => 1,
                    0 => 'My LDAP user',
                ),
                'mail' => array(
                    'count' => 2,
                    0 => 'user1@localhost',
                    1 => 'user2@localhost',
                ),
                'uid' => array(
                    'count' => 1,
                    0 => 'my_ldap_user',
                ),
                0 => 'cn',
                1 => 'mail',
                2 => 'uid',
            )
        ));

        $groups = array(
            new LdapGroupProvider('CN=Kanboard Admins,OU=Groups,DC=kanboard,DC=local', 'Kanboard Admins')
        );

        $this->client
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('OU=Users,DC=kanboard,DC=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->willReturn(true);

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn($entries);

        $this->user
            ->method('getAttributeUsername')
            ->willReturn('uid');

        $this->user
            ->method('getAttributeName')
            ->willReturn('cn');

        $this->user
            ->method('getAttributeEmail')
            ->willReturn('mail');

        $this->user
            ->method('getAttributeGroup')
            ->willReturn('');

        $this->user
            ->method('getGroupUserFilter')
            ->willReturn('(&(objectClass=posixGroup)(memberUid=%s))');

        $this->user
            ->method('getGroupAdminDn')
            ->willReturn('cn=Kanboard Admins,ou=Groups,dc=kanboard,dc=local');

        $this->user
            ->method('getBaseDn')
            ->willReturn('OU=Users,DC=kanboard,DC=local');

        $this->group
            ->expects($this->once())
            ->method('find')
            ->with('(&(objectClass=posixGroup)(memberUid=my_ldap_user))')
            ->willReturn($groups);

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Kanboard\User\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=Users,dc=kanboard,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(array('CN=Kanboard Admins,OU=Groups,DC=kanboard,DC=local'), $user->getExternalGroupIds());
        $this->assertEquals(Role::APP_ADMIN, $user->getRole());
        $this->assertEquals(array('is_ldap_user' => 1), $user->getExtraAttributes());
    }

    public function testGetUserWithManagerRoleAndPosixGroups()
    {
        $entries = new Entries(array(
            'count' => 1,
            0 => array(
                'count' => 2,
                'dn' => 'uid=my_ldap_user,ou=Users,dc=kanboard,dc=local',
                'cn' => array(
                    'count' => 1,
                    0 => 'My LDAP user',
                ),
                'mail' => array(
                    'count' => 2,
                    0 => 'user1@localhost',
                    1 => 'user2@localhost',
                ),
                'uid' => array(
                    'count' => 1,
                    0 => 'my_ldap_user',
                ),
                0 => 'cn',
                1 => 'mail',
                2 => 'uid',
            )
        ));

        $groups = array(
            new LdapGroupProvider('CN=Kanboard Users,OU=Groups,DC=kanboard,DC=local', 'Kanboard Users'),
            new LdapGroupProvider('CN=Kanboard Managers,OU=Groups,DC=kanboard,DC=local', 'Kanboard Managers'),
        );

        $this->client
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('OU=Users,DC=kanboard,DC=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->willReturn(true);

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn($entries);

        $this->user
            ->method('getAttributeUsername')
            ->willReturn('uid');

        $this->user
            ->method('getAttributeName')
            ->willReturn('cn');

        $this->user
            ->method('getAttributeEmail')
            ->willReturn('mail');

        $this->user
            ->method('getAttributeGroup')
            ->willReturn('');

        $this->user
            ->method('getGroupUserFilter')
            ->willReturn('(&(objectClass=posixGroup)(memberUid=%s))');

        $this->user
            ->method('getGroupManagerDn')
            ->willReturn('cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local');

        $this->user
            ->method('getBaseDn')
            ->willReturn('OU=Users,DC=kanboard,DC=local');

        $this->group
            ->expects($this->once())
            ->method('find')
            ->with('(&(objectClass=posixGroup)(memberUid=my_ldap_user))')
            ->willReturn($groups);

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Kanboard\User\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=Users,dc=kanboard,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(
            array(
                'CN=Kanboard Users,OU=Groups,DC=kanboard,DC=local',
                'CN=Kanboard Managers,OU=Groups,DC=kanboard,DC=local',
            ),
            $user->getExternalGroupIds()
        );
        $this->assertEquals(Role::APP_MANAGER, $user->getRole());
        $this->assertEquals(array('is_ldap_user' => 1), $user->getExtraAttributes());
    }

    public function testGetUserWithUserRoleAndPosixGroups()
    {
        $entries = new Entries(array(
            'count' => 1,
            0 => array(
                'count' => 2,
                'dn' => 'uid=my_ldap_user,ou=Users,dc=kanboard,dc=local',
                'cn' => array(
                    'count' => 1,
                    0 => 'My LDAP user',
                ),
                'mail' => array(
                    'count' => 2,
                    0 => 'user1@localhost',
                    1 => 'user2@localhost',
                ),
                'uid' => array(
                    'count' => 1,
                    0 => 'my_ldap_user',
                ),
                0 => 'cn',
                1 => 'mail',
                2 => 'uid',
            )
        ));

        $groups = array(
            new LdapGroupProvider('CN=Kanboard Users,OU=Groups,DC=kanboard,DC=local', 'Kanboard Users'),
        );

        $this->client
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo('OU=Users,DC=kanboard,DC=local'),
                $this->equalTo('(uid=my_ldap_user)')
            );

        $this->query
            ->expects($this->once())
            ->method('hasResult')
            ->willReturn(true);

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->willReturn($entries);

        $this->user
            ->method('getAttributeUsername')
            ->willReturn('uid');

        $this->user
            ->method('getAttributeName')
            ->willReturn('cn');

        $this->user
            ->method('getAttributeEmail')
            ->willReturn('mail');

        $this->user
            ->method('getAttributeGroup')
            ->willReturn('');

        $this->user
            ->method('getGroupUserFilter')
            ->willReturn('(&(objectClass=posixGroup)(memberUid=%s))');

        $this->user
            ->method('getGroupManagerDn')
            ->willReturn('cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local');

        $this->user
            ->method('getBaseDn')
            ->willReturn('OU=Users,DC=kanboard,DC=local');

        $this->group
            ->expects($this->once())
            ->method('find')
            ->with('(&(objectClass=posixGroup)(memberUid=my_ldap_user))')
            ->willReturn($groups);

        $user = $this->user->find('(uid=my_ldap_user)');
        $this->assertInstanceOf('Kanboard\User\LdapUserProvider', $user);
        $this->assertEquals('uid=my_ldap_user,ou=Users,dc=kanboard,dc=local', $user->getDn());
        $this->assertEquals('my_ldap_user', $user->getUsername());
        $this->assertEquals('My LDAP user', $user->getName());
        $this->assertEquals('user1@localhost', $user->getEmail());
        $this->assertEquals(
            array(
                'CN=Kanboard Users,OU=Groups,DC=kanboard,DC=local',
            ),
            $user->getExternalGroupIds()
        );
        $this->assertEquals(Role::APP_USER, $user->getRole());
        $this->assertEquals(array('is_ldap_user' => 1), $user->getExtraAttributes());
    }

    public function testGetLdapUserPatternNotConfigured()
    {
        $this->expectException('\LogicException');

        $user = new User($this->query);
        $user->getLdapUserPattern('test');
    }

    public function testGetLdapUserWithMultiplePlaceholders()
    {
        $filter = '(|(&(objectClass=user)(mail=%s))(&(objectClass=user)(sAMAccountName=%s)))';
        $expected = '(|(&(objectClass=user)(mail=test))(&(objectClass=user)(sAMAccountName=test)))';

        $user = new User($this->query);
        $this->assertEquals($expected, $user->getLdapUserPattern('test', $filter));
    }

    public function testGetLdapUserWithOnePlaceholder()
    {
        $filter = '(sAMAccountName=%s)';
        $expected = '(sAMAccountName=test)';

        $user = new User($this->query);
        $this->assertEquals($expected, $user->getLdapUserPattern('test', $filter));
    }

    public function testGetLdapUserPatternWithSpecialCharacters()
    {
        $username = 'admin*';
        $filter = '(uid=%s)';
        $expected = '(uid=admin\2a)';

        $this->assertEquals($expected, $this->user->getLdapUserPattern($username, $filter));
    }

    public function testGetGroupUserFilter()
    {
        $user = new User($this->query);
        $this->assertSame('', $user->getGroupUserFilter());
    }

    public function testHasGroupUserFilterWithEmptyString()
    {
        $this->user
            ->method('getGroupUserFilter')
            ->willReturn('');

        $this->assertFalse($this->user->hasGroupUserFilter());
    }

    public function testHasGroupUserFilterWithNull()
    {
        $this->user
            ->method('getGroupUserFilter')
            ->willReturn(null);

        $this->assertFalse($this->user->hasGroupUserFilter());
    }

    public function testHasGroupUserFilterWithValue()
    {
        $this->user
            ->method('getGroupUserFilter')
            ->willReturn('foobar');

        $this->assertTrue($this->user->hasGroupUserFilter());
    }

    public function testHasGroupsConfigured()
    {
        $this->user
            ->method('getGroupAdminDn')
            ->willReturn('something');

        $this->user
            ->method('getGroupManagerDn')
            ->willReturn('something');

        $this->assertTrue($this->user->hasGroupsConfigured());
    }

    public function testHasGroupAdminDnConfigured()
    {
        $this->user
            ->method('getGroupAdminDn')
            ->willReturn('something');

        $this->user
            ->method('getGroupManagerDn')
            ->willReturn('');

        $this->assertTrue($this->user->hasGroupsConfigured());
    }

    public function testHasGroupManagerDnConfigured()
    {
        $this->user
            ->method('getGroupAdminDn')
            ->willReturn('');

        $this->user
            ->method('getGroupManagerDn')
            ->willReturn('something');

        $this->assertTrue($this->user->hasGroupsConfigured());
    }

    public function testHasGroupsNotConfigured()
    {
        $this->user
            ->method('getGroupAdminDn')
            ->willReturn('');

        $this->user
            ->method('getGroupManagerDn')
            ->willReturn('');

        $this->assertFalse($this->user->hasGroupsConfigured());
    }
}
