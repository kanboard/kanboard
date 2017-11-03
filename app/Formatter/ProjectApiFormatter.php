<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;

/**
 * Class ProjectApiFormatter
 *
 * @package Kanboard\Formatter
 */
class ProjectApiFormatter extends BaseFormatter implements FormatterInterface
{
    protected $project = null;

    public function withProject($project)
    {
        $this->project = $project;
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
        if (! empty($this->project)) {
            $this->project['url'] = array(
                'board' => $this->helper->url->to('BoardViewController', 'show', array('project_id' => $this->project['id']), '', true),
                'list' => $this->helper->url->to('TaskListController', 'show', array('project_id' => $this->project['id']), '', true),
            );
        }

        return $this->project;
    }
}
