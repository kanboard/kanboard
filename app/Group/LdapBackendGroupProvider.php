<?php

namespace Kanboard\Group;

use LogicException;
use Kanboard\Core\Base;
use Kanboard\Core\Group\GroupBackendProviderInterface;
use Kanboard\Core\Ldap\Client as LdapClient;
use Kanboard\Core\Ldap\ClientException as LdapException;
use Kanboard\Core\Ldap\Group as LdapGroup;

/**
 * LDAP Backend Group Provider
 *
 * @package  group
 * @author   Frederic Guillot
 */
class LdapBackendGroupProvider extends Base implements GroupBackendProviderInterface
{
    /**
     * Find a group from a search query
     *
     * @access public
     * @param  string $input
     * @return LdapGroupProvider[]
     */
    public function find($input)
    {
        try {
            $ldap = LdapClient::connect();
            return LdapGroup::getGroups($ldap, $this->getLdapGroupPattern($input));

        } catch (LdapException $e) {
            $this->logger->error($e->getMessage());
            return array();
        }
    }

    /**
     * Get LDAP group pattern
     *
     * @access public
     * @param  string $input
     * @param  string $filter
     * @return string
     */
    public function getLdapGroupPattern($input, $filter = LDAP_GROUP_FILTER)
    {
        if ($filter === '') {
            throw new LogicException('LDAP group filter is empty. Please configure the LDAP_GROUP_FILTER parameter in your configuration file');
        }

        $escapedInput = ldap_escape($input, '', LDAP_ESCAPE_FILTER);
        return sprintf($filter, $escapedInput);
    }
}
