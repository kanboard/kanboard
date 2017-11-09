<?php

namespace Kanboard\Core\Group;

/**
 * Group Provider Interface
 *
 * @package  Kanboard\Core\Group
 * @author   Frederic Guillot
 */
interface GroupProviderInterface
{
    /**
     * Get internal id
     *
     * You must return 0 if the group come from an external backend
     *
     * @access public
     * @return integer
     */
    public function getInternalId();

    /**
     * Get external id
     *
     * You must return a unique id if the group come from an external provider
     *
     * @access public
     * @return string
     */
    public function getExternalId();

    /**
     * Get group name
     *
     * @access public
     * @return string
     */
    public function getName();
}
