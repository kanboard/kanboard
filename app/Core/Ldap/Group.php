<?php

namespace Kanboard\Core\Ldap;

use LogicException;
use Kanboard\Group\LdapGroupProvider;

/**
 * LDAP Group Finder
 *
 * @package ldap
 * @author  Frederic Guillot
 */
class Group
{
    /**
     * Query
     *
     * @access protected
     * @var Query
     */
    protected $query;

    /**
     * Constructor
     *
     * @access public
     * @param  Query   $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Get groups
     *
     * @static
     * @access public
     * @param  Client    $client
     * @param  string    $query
     * @return LdapGroupProvider[]
     */
    public static function getGroups(Client $client, $query)
    {
        $self = new static(new Query($client));
        return $self->find($query);
    }

    /**
     * Find groups
     *
     * @access public
     * @param  string    $query
     * @return array
     */
    public function find($query)
    {
        $this->query->execute($this->getBaseDn(), $query, $this->getAttributes());
        $groups = array();

        if ($this->query->hasResult()) {
            $groups = $this->build();
        }

        return $groups;
    }

    /**
     * Build groups list
     *
     * @access protected
     * @return array
     */
    protected function build()
    {
        $groups = array();

        foreach ($this->query->getEntries()->getAll() as $entry) {
            $groups[] = new LdapGroupProvider($entry->getDn(), $entry->getFirstValue($this->getAttributeName()));
        }

        return $groups;
    }

    /**
     * Ge the list of attributes to fetch when reading the LDAP group entry
     *
     * Must returns array with index that start at 0 otherwise ldap_search returns a warning "Array initialization wrong"
     *
     * @access public
     * @return array
     */
    public function getAttributes()
    {
        return array_values(array_filter(array(
            $this->getAttributeName(),
        )));
    }

    /**
     * Get LDAP group name attribute
     *
     * @access public
     * @return string
     */
    public function getAttributeName()
    {
        if (! LDAP_GROUP_ATTRIBUTE_NAME) {
            throw new LogicException('LDAP full name attribute empty, check the parameter LDAP_GROUP_ATTRIBUTE_NAME');
        }

        return strtolower(LDAP_GROUP_ATTRIBUTE_NAME);
    }

    /**
     * Get LDAP group base DN
     *
     * @access public
     * @return string
     */
    public function getBaseDn()
    {
        if (! LDAP_GROUP_BASE_DN) {
            throw new LogicException('LDAP group base DN empty, check the parameter LDAP_GROUP_BASE_DN');
        }

        return LDAP_GROUP_BASE_DN;
    }
}
