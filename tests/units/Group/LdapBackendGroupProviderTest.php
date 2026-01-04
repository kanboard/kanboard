<?php

namespace KanboardTests\units\Group;

use KanboardTests\units\Base;
use Kanboard\Group\LdapBackendGroupProvider;

class LdapBackendGroupProviderTest extends Base
{
    public function testGetLdapGroupPatternWithEmptyFilter()
    {
        $this->expectException('LogicException', 'LDAP group filter is empty. Please configure the LDAP_GROUP_FILTER parameter in your configuration file');

        $backend = new LdapBackendGroupProvider($this->container);
        $backend->getLdapGroupPattern('test');
    }

    public function testGetLdapGroupPatternWithUserInput()
    {
        $backend = new LdapBackendGroupProvider($this->container);
        $input = 'mygroup';
        $filter = '(&(objectClass=group)(cn=%s))';
        $expected = '(&(objectClass=group)(cn=mygroup))';

        $this->assertEquals($expected, $backend->getLdapGroupPattern($input, $filter));
    }

    public function testGetLdapGroupPatternWithSpecialCharacters()
    {
        $backend = new LdapBackendGroupProvider($this->container);
        $input = 'group*';
        $filter = '(&(objectClass=group)(cn=%s))';
        $expected = '(&(objectClass=group)(cn=group\2a))';

        $this->assertEquals($expected, $backend->getLdapGroupPattern($input, $filter));
    }
}
