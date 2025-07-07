<?php

namespace KanboardTests\units\Group;

use KanboardTests\units\Base;
use Kanboard\Group\LdapBackendGroupProvider;

class LdapBackendGroupProviderTest extends Base
{
    public function testGetLdapGroupPattern()
    {
        $this->expectException('LogicException', 'LDAP group filter empty, check the parameter LDAP_GROUP_FILTER');

        $backend = new LdapBackendGroupProvider($this->container);
        $backend->getLdapGroupPattern('test');
    }
}
