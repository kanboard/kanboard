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
            
            // Add public board URL if public access is enabled
            if (!empty($this->project['is_public']) && !empty($this->project['token'])) {
                $this->project['url']['public_board'] = $this->helper->url->to('BoardViewController', 'readonly', array('token' => $this->project['token']), '', true);
                $this->project['url']['rss_feed'] = $this->helper->url->to('FeedController', 'project', array('token' => $this->project['token']), '', true);
                $this->project['url']['ical_feed'] = $this->helper->url->to('ICalendarController', 'project', array('token' => $this->project['token']), '', true);
            }
        }

        return $this->project;
    }
}
