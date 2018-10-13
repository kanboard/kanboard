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
    const EVENT_DESTROY = 'project.file.destroy';

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
     * Fire file creation event
     *
     * @access protected
     * @param  integer $file_id
     */
    protected function fireCreationEvent($file_id)
    {
        $this->queueManager->push($this->projectFileEventJob->withParams($file_id, self::EVENT_CREATE));
    }

    /**
     * Fire file destruction event
     *
     * @access protected
     * @param  integer $file_id
     */
    protected function fireDestructionEvent($file_id)
    {
        $this->queueManager->push($this->projectFileEventJob->withParams($file_id, self::EVENT_DESTROY));
    }
}
