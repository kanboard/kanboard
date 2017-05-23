<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;

/**
 * Class ProjectsApiFormatter
 *
 * @package Kanboard\Formatter
 */
class ProjectsApiFormatter extends BaseFormatter implements FormatterInterface
{
    protected $projects = array();

    public function withProjects($projects)
    {
        $this->projects = $projects;
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
        if (! empty($this->projects)) {
            foreach ($this->projects as &$project) {
                $project = $this->projectApiFormatter->withProject($project)->format();
            }
        }

        return $this->projects;
    }
}
