<?php

namespace Kanboard\Model;

/**
 * Task File Model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskFile extends File
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'task_has_files';

    /**
     * SQL foreign key
     *
     * @var string
     */
    const FOREIGN_KEY = 'task_id';

    /**
     * Path prefix
     *
     * @var string
     */
    const PATH_PREFIX = 'tasks';

    /**
     * Events
     *
     * @var string
     */
    const EVENT_CREATE = 'task.file.create';

    /**
     * Handle screenshot upload
     *
     * @access public
     * @param  integer  $task_id      Task id
     * @param  string   $blob         Base64 encoded image
     * @return bool|integer
     */
    public function uploadScreenshot($task_id, $blob)
    {
        $original_filename = e('Screenshot taken %s', $this->helper->dt->datetime(time())).'.png';
        return $this->uploadContent($task_id, $original_filename, $blob);
    }
}
