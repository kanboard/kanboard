<?php

namespace Kanboard\Core\Ldap;

use LogicException;
use Kanboard\Core\Security\Role;
use Kanboard\User\LdapUserProvider;

/**
 * LDAP User Finder
 *
 * @package ldap
 * @author  Frederic Guillot
 */
class User
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
     * Get user profile
     *
     * @static
     * @access public
     * @param  Client    $client
     * @param  string    $username
     * @return LdapUserProvider
     */
    public static function getUser(Client $client, $username)
    {
        $self = new static(new Query($client));
        return $self->find($self->getLdapUserPattern($username));
    }

    /**
     * Find user
     *
     * @access public
     * @param  string    $query
     * @return null|LdapUserProvider
     */
    public function find($query)
    {
        $this->query->execute($this->getBasDn(), $query, $this->getAttributes());
        $user = null;

        if ($this->query->hasResult()) {
            $user = $this->build();
        }

        return $user;
    }

    /**
     * Build user profile
     *
     * @access protected
     * @return LdapUserProvider
     */
    protected function build()
    {
        $entry = $this->query->getEntries()->getFirstEntry();
        $role = Role::APP_USER;

        if ($entry->hasValue($this->getAttributeGroup(), $this->getGroupAdminDn())) {
            $role = Role::APP_ADMIN;
        } elseif ($entry->hasValue($this->getAttributeGroup(), $this->getGroupManagerDn())) {
            $role = Role::APP_MANAGER;
        }

        return new LdapUserProvider(
            $entry->getDn(),
            $entry->getFirstValue($this->getAttributeUsername()),
            $entry->getFirstValue($this->getAttributeName()),
            $entry->getFirstValue($this->getAttributeEmail()),
            $role,
            $entry->getAll($this->getAttributeGroup())
        );
    }

    /**
     * Ge the list of attributes to fetch when reading the LDAP user entry
     *
     * Must returns array with index that start at 0 otherwise ldap_search returns a warning "Array initialization wrong"
     *
     * @access public
     * @return array
     */
    public function getAttributes()
    {
        return array_values(array_filter(array(
            $this->getAttributeUsername(),
            $this->getAttributeName(),
            $this->getAttributeEmail(),
            $this->getAttributeGroup(),
        )));
    }

    /**
     * Get LDAP account id attribute
     *
     * @access public
     * @return string
     */
    public function getAttributeUsername()
    {
        if (! LDAP_USER_ATTRIBUTE_USERNAME) {
            throw new LogicException('LDAP username attribute empty, check the parameter LDAP_USER_ATTRIBUTE_USERNAME');
        }

        return LDAP_USER_ATTRIBUTE_USERNAME;
    }

    /**
     * Get LDAP user name attribute
     *
     * @access public
     * @return string
     */
    public function getAttributeName()
    {
        if (! LDAP_USER_ATTRIBUTE_FULLNAME) {
            throw new LogicException('LDAP full name attribute empty, check the parameter LDAP_USER_ATTRIBUTE_FULLNAME');
        }

        return LDAP_USER_ATTRIBUTE_FULLNAME;
    }

    /**
     * Get LDAP account email attribute
     *
     * @access public
     * @return string
     */
    public function getAttributeEmail()
    {
        if (! LDAP_USER_ATTRIBUTE_EMAIL) {
            throw new LogicException('LDAP email attribute empty, check the parameter LDAP_USER_ATTRIBUTE_EMAIL');
        }

        return LDAP_USER_ATTRIBUTE_EMAIL;
    }

    /**
     * Get LDAP account memberof attribute
     *
     * @access public
     * @return string
     */
    public function getAttributeGroup()
    {
        return LDAP_USER_ATTRIBUTE_GROUPS;
    }

    /**
     * Get LDAP admin group DN
     *
     * @access public
     * @return string
     */
    public function getGroupAdminDn()
    {
        return LDAP_GROUP_ADMIN_DN;
    }

    /**
     * Get LDAP application manager group DN
     *
     * @access public
     * @return string
     */
    public function getGroupManagerDn()
    {
        return LDAP_GROUP_MANAGER_DN;
    }

    /**
     * Get LDAP user base DN
     *
     * @access public
     * @return string
     */
    public function getBasDn()
    {
        if (! LDAP_USER_BASE_DN) {
            throw new LogicException('LDAP user base DN empty, check the parameter LDAP_USER_BASE_DN');
        }

        return LDAP_USER_BASE_DN;
    }

    /**
     * Get LDAP user pattern
     *
     * @access public
     * @param  string  $username
     * @param  string  $filter
     * @return string
     */
    public function getLdapUserPattern($username, $filter = LDAP_USER_FILTER)
    {
        if (! $filter) {
            throw new LogicException('LDAP user filter empty, check the parameter LDAP_USER_FILTER');
        }

        return str_replace('%s', $username, $filter);
    }
}
