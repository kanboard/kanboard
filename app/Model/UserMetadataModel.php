<?php

namespace Kanboard\Model;

/**
 * User Metadata
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class UserMetadataModel extends MetadataModel
{
    /**
     * Get the table
     *
     * @abstract
     * @access protected
     * @return string
     */
    protected function getTable()
    {
        return 'user_has_metadata';
    }

    /**
     * Define the entity key
     *
     * @access protected
     * @return string
     */
    protected function getEntityKey()
    {
        return 'user_id';
    }
}
