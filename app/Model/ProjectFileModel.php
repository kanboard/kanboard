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
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_has_files';

    /**
     * SQL foreign key
     *
     * @var string
     */
    const FOREIGN_KEY = 'project_id';

    /**
     * Path prefix
     *
     * @var string
     */
    const PATH_PREFIX = 'projects';

    /**
     * Events
     *
     * @var string
     */
    const EVENT_CREATE = 'project.file.create';
}
