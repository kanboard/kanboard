<?php

namespace Kanboard\Model;

/**
 * Project File Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectFileModel extends FileModel
{
    /**
     * Table name
     *
     * @var string
     */
    const TABLE = 'project_has_files';

    /**
     * Events
     *
     * @var string
     */
    const EVENT_CREATE = 'project.file.create';

    /**
     * Get the table
     *
     * @abstract
     * @access protected
     * @return string
     */
    protected function getTable()
    {
        return self::TABLE;
    }

    /**
     * Define the foreign key
     *
     * @abstract
     * @access protected
     * @return string
     */
    protected function getForeignKey()
    {
        return 'project_id';
    }

    /**
     * Define the path prefix
     *
     * @abstract
     * @access protected
     * @return string
     */
    protected function getPathPrefix()
    {
        return 'projects';
    }

    /**
     * Get event name
     *
     * @abstract
     * @access protected
     * @return string
     */
    protected function getEventName()
    {
        return self::EVENT_CREATE;
    }
}
