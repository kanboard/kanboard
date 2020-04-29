<?php

require_once __DIR__.'/../Base.php';

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
