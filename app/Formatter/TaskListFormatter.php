<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;

/**
 * Class TaskListFormatter
 *
 * @package Kanboard\Formatter
 * @author  Frederic Guillot
 */
class TaskListFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Apply formatter
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $tasks = $this->query->findAll();
        $taskIds = array_column($tasks, 'id');
        $tags = $this->taskTagModel->getTagsByTaskIds($taskIds);
        array_merge_relation($tasks, $tags, 'tags', 'id');

        return $tasks;
    }
}
