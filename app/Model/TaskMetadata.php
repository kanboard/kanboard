<?php

namespace Kanboard\Model;

/**
 * Task Metadata
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskMetadata extends Metadata
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'task_has_metadata';

    /**
     * Define the entity key
     *
     * @access protected
     * @return string
     */
    protected function getEntityKey()
    {
        return 'task_id';
    }
}
