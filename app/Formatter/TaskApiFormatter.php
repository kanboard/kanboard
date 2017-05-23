<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;

/**
 * Class TaskApiFormatter
 *
 * @package Kanboard\Formatter
 */
class TaskApiFormatter extends BaseFormatter implements FormatterInterface
{
    protected $task = null;

    public function withTask($task)
    {
        $this->task = $task;
        return $this;
    }

    /**
     * Apply formatter
     *
     * @access public
     * @return mixed
     */
    public function format()
    {
        if (! empty($this->task)) {
            $this->task['url'] = $this->helper->url->to('TaskViewController', 'show', array('task_id' => $this->task['id'], 'project_id' => $this->task['project_id']), '', true);
            $this->task['color'] = $this->colorModel->getColorProperties($this->task['color_id']);
        }

        return $this->task;
    }
}
