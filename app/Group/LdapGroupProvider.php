<?php

namespace Kanboard\Group;

use Kanboard\Core\Group\GroupProviderInterface;

/**
 * LDAP Group Provider
 *
 * @package  group
 * @author   Frederic Guillot
 */
class LdapGroupProvider implements GroupProviderInterface
{
    /**
     * Group DN
     *
     * @access private
     * @var string
     */
    private $dn = '';

    /**
     * Group Name
     *
     * @access private
     * @var string
     */
    private $name = '';

    /**
     * Constructor
     *
     * @access public
     * @param  string $dn
     * @param  string $name
     */
    public function __construct($dn, $name)
    {
        $this->dn = $dn;
        $this->name = $name;
    }

    /**
     * Get internal id
     *
     * @access public
     * @return integer
     */
    public function getInternalId()
    {
        return '';
    }

    /**
     * Get external id
     *
     * @access public
     * @return string
     */
    public function getExternalId()
    {
        return $this->dn;
    }

    /**
     * Get group name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
