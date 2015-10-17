<?php

namespace Kanboard\Model;

/**
 * Project Metadata
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectMetadata extends Metadata
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_has_metadata';

    /**
     * Define the entity key
     *
     * @access protected
     * @return string
     */
    protected function getEntityKey()
    {
        return 'project_id';
    }
}
