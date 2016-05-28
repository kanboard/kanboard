<?php

namespace Kanboard\Model;

/**
 * Task Metadata
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TaskMetadataModel extends MetadataModel
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
        return 'task_has_metadata';
    }

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
