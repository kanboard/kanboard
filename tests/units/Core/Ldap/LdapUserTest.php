<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Ldap\Query;
use Kanboard\Core\Ldap\User;
use Kanboard\Core\Ldap\Entries;
use Kanboard\Core\Security\Role;
use Kanboard\Group\LdapGroupProvider;

class LdapUserTest extends Base
{
    private $query;
    private $client;
    private $user;
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
            ->setConstructorArgs(array(new Query($this->client)))
            ->setMethods(array(
                'find',
            ))
            ->getMock();

        $this->user = $this
            ->getMockBuilder('\Kanboard\Core\Ldap\User')
            ->setConstructorArgs(array($this->query, $this->group))
            ->setMethods(array(
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
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

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
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('samaccountname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('displayname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getBaseDn')
            ->will($this->returnValue('ou=People,dc=kanboard,dc=local'));

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
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

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
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributePhoto')
            ->will($this->returnValue('jpegPhoto'));

        $this->user
            ->expects($this->any())
            ->method('getBaseDn')
            ->will($this->returnValue('ou=People,dc=kanboard,dc=local'));

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
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

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
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('samaccountname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('displayname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeGroup')
            ->will($this->returnValue('memberof'));

        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue('CN=Kanboard-Admins,CN=Users,DC=kanboard,DC=local'));

        $this->user
            ->expects($this->any())
            ->method('getBaseDn')
            ->will($this->returnValue('ou=People,dc=kanboard,dc=local'));

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
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

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
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('samaccountname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('displayname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeGroup')
            ->will($this->returnValue('memberof'));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue('CN=Kanboard-Managers,CN=Users,DC=kanboard,DC=local'));

        $this->user
            ->expects($this->any())
            ->method('getBaseDn')
            ->will($this->returnValue('ou=People,dc=kanboard,dc=local'));

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
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

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
            ->will($this->returnValue(false));

        $this->query
            ->expects($this->never())
            ->method('getEntries');

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('samaccountname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('displayname'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getBaseDn')
            ->will($this->returnValue('ou=People,dc=kanboard,dc=local'));

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
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

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
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('uid'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('cn'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeGroup')
            ->will($this->returnValue(''));

        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue('(&(objectClass=posixGroup)(memberUid=%s))'));

        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue('cn=Kanboard Admins,ou=Groups,dc=kanboard,dc=local'));

        $this->user
            ->expects($this->any())
            ->method('getBaseDn')
            ->will($this->returnValue('OU=Users,DC=kanboard,DC=local'));

        $this->group
            ->expects($this->once())
            ->method('find')
            ->with('(&(objectClass=posixGroup)(memberUid=my_ldap_user))')
            ->will($this->returnValue($groups));

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
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

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
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('uid'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('cn'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeGroup')
            ->will($this->returnValue(''));

        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue('(&(objectClass=posixGroup)(memberUid=%s))'));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue('cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local'));

        $this->user
            ->expects($this->any())
            ->method('getBaseDn')
            ->will($this->returnValue('OU=Users,DC=kanboard,DC=local'));

        $this->group
            ->expects($this->once())
            ->method('find')
            ->with('(&(objectClass=posixGroup)(memberUid=my_ldap_user))')
            ->will($this->returnValue($groups));

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
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('my_ldap_resource'));

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
            ->will($this->returnValue(true));

        $this->query
            ->expects($this->once())
            ->method('getEntries')
            ->will($this->returnValue($entries));

        $this->user
            ->expects($this->any())
            ->method('getAttributeUsername')
            ->will($this->returnValue('uid'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeName')
            ->will($this->returnValue('cn'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeEmail')
            ->will($this->returnValue('mail'));

        $this->user
            ->expects($this->any())
            ->method('getAttributeGroup')
            ->will($this->returnValue(''));

        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue('(&(objectClass=posixGroup)(memberUid=%s))'));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue('cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local'));

        $this->user
            ->expects($this->any())
            ->method('getBaseDn')
            ->will($this->returnValue('OU=Users,DC=kanboard,DC=local'));

        $this->group
            ->expects($this->once())
            ->method('find')
            ->with('(&(objectClass=posixGroup)(memberUid=my_ldap_user))')
            ->will($this->returnValue($groups));

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

    public function testGetBaseDnNotConfigured()
    {
        $this->expectException('\LogicException');

        $user = new User($this->query);
        $user->getBaseDn();
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

    public function testGetGroupUserFilter()
    {
        $user = new User($this->query);
        $this->assertSame('', $user->getGroupUserFilter());
    }

    public function testHasGroupUserFilterWithEmptyString()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue(''));

        $this->assertFalse($this->user->hasGroupUserFilter());
    }

    public function testHasGroupUserFilterWithNull()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue(null));

        $this->assertFalse($this->user->hasGroupUserFilter());
    }

    public function testHasGroupUserFilterWithValue()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupUserFilter')
            ->will($this->returnValue('foobar'));

        $this->assertTrue($this->user->hasGroupUserFilter());
    }

    public function testHasGroupsConfigured()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue('something'));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue('something'));

        $this->assertTrue($this->user->hasGroupsConfigured());
    }

    public function testHasGroupAdminDnConfigured()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue('something'));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue(''));

        $this->assertTrue($this->user->hasGroupsConfigured());
    }

    public function testHasGroupManagerDnConfigured()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue(''));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue('something'));

        $this->assertTrue($this->user->hasGroupsConfigured());
    }

    public function testHasGroupsNotConfigured()
    {
        $this->user
            ->expects($this->any())
            ->method('getGroupAdminDn')
            ->will($this->returnValue(''));

        $this->user
            ->expects($this->any())
            ->method('getGroupManagerDn')
            ->will($this->returnValue(''));

        $this->assertFalse($this->user->hasGroupsConfigured());
    }
}
