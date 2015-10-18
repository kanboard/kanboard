<?php

namespace Kanboard\Model;

/**
 * User Metadata
 *
 * @package  model
 * @author   Frederic Guillot
 */
class UserMetadata extends Metadata
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'user_has_metadata';

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
