<?php

namespace Kanboard\Group;

use Kanboard\Core\Group\GroupProviderInterface;

/**
 * Database Group Provider
 *
 * @package  group
 * @author   Frederic Guillot
 */
class DatabaseGroupProvider implements GroupProviderInterface
{
    /**
     * Group properties
     *
     * @access private
     * @var array
     */
    private $group = array();

    /**
     * Constructor
     *
     * @access public
     * @param  array $group
     */
    public function __construct(array $group)
    {
        $this->group = $group;
    }

    /**
     * Get internal id
     *
     * @access public
     * @return integer
     */
    public function getInternalId()
    {
        return (int) $this->group['id'];
    }

    /**
     * Get external id
     *
     * @access public
     * @return string
     */
    public function getExternalId()
    {
        return '';
    }

    /**
     * Get group name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->group['name'];
    }
}
