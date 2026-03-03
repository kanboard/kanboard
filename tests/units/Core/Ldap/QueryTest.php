<?php

namespace Kanboard\Core\Ldap;

use KanboardTests\units\Core\Ldap\QueryTest;

function ldap_search($link_identifier, $base_dn, $filter, array $attributes)
{
    return QueryTest::$functions->ldap_search($link_identifier, $base_dn, $filter, $attributes);
}

function ldap_get_entries($link_identifier, $result_identifier)
{
    return QueryTest::$functions->ldap_get_entries($link_identifier, $result_identifier);
}

namespace KanboardTests\units\Core\Ldap;

use Kanboard\Core\Ldap\Query;
use KanboardTests\units\Base;

class QueryFunctionsProxy
{
    public function ldap_search($link_identifier, $base_dn, $filter, array $attributes)
    {
    }

    public function ldap_get_entries($link_identifier, $result_identifier)
    {
    }
}

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class QueryTest extends Base
{
    public static $functions;
    private $client;

    protected function setUp(): void
    {
        parent::setup();

        if (! function_exists('ldap_connect') || ! function_exists('ldap_escape')) {
            $this->markTestSkipped('The PHP LDAP extension is required');
        }

        self::$functions = $this
            ->getMockBuilder(QueryFunctionsProxy::class)
            ->onlyMethods(array(
                'ldap_search',
                'ldap_get_entries',
            ))
            ->getMock();

        $this->client = $this
            ->getMockBuilder('\Kanboard\Core\Ldap\Client')
            ->onlyMethods(array(
                'getConnection',
            ))
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::$functions = null;
    }

    public function testExecuteQuerySuccessfully()
    {
        $entries = array(
            'count' => 1,
            0 => array(
                'count' => 2,
                'dn' => 'uid=my_user,ou=People,dc=kanboard,dc=local',
                'displayname' => array(
                    'count' => 1,
                    0 => 'My user',
                ),
                'mail' => array(
                    'count' => 2,
                    0 => 'user1@localhost',
                    1 => 'user2@localhost',
                ),
                0 => 'displayname',
                1 => 'mail',
            )
        );

        $this->client
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        self::$functions
            ->expects($this->once())
            ->method('ldap_search')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('uid=my_user'),
                $this->equalTo(array('displayname'))
            )
            ->willReturn('search_resource');

        self::$functions
            ->expects($this->once())
            ->method('ldap_get_entries')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('search_resource')
            )
            ->willReturn($entries);

        $query = new Query($this->client);
        $query->execute('ou=People,dc=kanboard,dc=local', 'uid=my_user', array('displayname'));
        $this->assertTrue($query->hasResult());

        $this->assertEquals('My user', $query->getEntries()->getFirstEntry()->getFirstValue('displayname'));
        $this->assertEquals('user1@localhost', $query->getEntries()->getFirstEntry()->getFirstValue('mail'));
        $this->assertEquals('', $query->getEntries()->getFirstEntry()->getFirstValue('not_found'));

        $this->assertEquals('uid=my_user,ou=People,dc=kanboard,dc=local', $query->getEntries()->getFirstEntry()->getDn());
        $this->assertEquals('', $query->getEntries()->getFirstEntry()->getFirstValue('missing'));
    }

    public function testExecuteQueryNotFound()
    {
        $this->client
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        self::$functions
            ->expects($this->once())
            ->method('ldap_search')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('uid=my_user'),
                $this->equalTo(array('displayname'))
            )
            ->willReturn('search_resource');

        self::$functions
            ->expects($this->once())
            ->method('ldap_get_entries')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('search_resource')
            )
            ->willReturn(array());

        $query = new Query($this->client);
        $query->execute('ou=People,dc=kanboard,dc=local', 'uid=my_user', array('displayname'));
        $this->assertFalse($query->hasResult());
    }

    public function testExecuteQueryFailed()
    {
        $this->client
            ->expects($this->once())
            ->method('getConnection')
            ->willReturn('my_ldap_resource');

        self::$functions
            ->expects($this->once())
            ->method('ldap_search')
            ->with(
                $this->equalTo('my_ldap_resource'),
                $this->equalTo('ou=People,dc=kanboard,dc=local'),
                $this->equalTo('uid=my_user'),
                $this->equalTo(array('displayname'))
            )
            ->willReturn(false);

        $query = new Query($this->client);
        $query->execute('ou=People,dc=kanboard,dc=local', 'uid=my_user', array('displayname'));
        $this->assertFalse($query->hasResult());
    }
}
