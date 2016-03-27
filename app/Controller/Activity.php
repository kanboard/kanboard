<?php

namespace Kanboard\Controller;

/**
 * Activity stream
 *
 * @package controller
 * @author  Frederic Guillot
 */
class Activity extends Base
{
    /**
     * Get activity pagination by task
     *
     * @access private
     * @param  integer  $task_id
     * @param  string   $action
     * @param  integer  $max
     */
    private function getActivityPaginatorByTask($task_id, $action='task', $max=50)
    {
        $events =  $this->paginator
            ->setUrl('activity', $action, array('pagination' => 'activity', 'task_id' => $task_id))
            ->setMax($max)
            ->setOrder('id')
            ->setDirection('desc')
            ->setQuery($this->projectActivity->getTask($task_id))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'activity');

        return $events;
    }

    /**
     * Get activity pagination by project
     *
     * @access private
     * @param  integer  $project_id
     * @param  string   $action
     * @param  integer  $max
     */
    private function getActivityPaginatorByProject($project_id, $action='project', $max=50)
    {
        $events =  $this->paginator
            ->setUrl('activity', $action, array('pagination' => 'activity', 'project_id' => $project_id))
            ->setMax($max)
            ->setOrder('id')
            ->setDirection('desc')
            ->setQuery($this->projectActivity->getProject($project_id))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'activity');

        return $events;
    }

    /**
     * Activity page for a project
     *
     * @access public
     */
    public function project()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->app('activity/project', array(
            'events' => $this->getActivityPaginatorByProject($project['id']),
            'project' => $project,
            'title' => t('%s\'s activity', $project['name'])
        )));
    }

    /**
     * Display task activities
     *
     * @access public
     */
    public function task()
    {
        $task = $this->getTask();

        $this->response->html($this->helper->layout->task('activity/task', array(
            'title' => $task['title'],
            'task' => $task,
            'events' => $this->getActivityPaginatorByTask($task['id']),
            'project' => $this->project->getById($task['project_id']),
        )));
    }
}
