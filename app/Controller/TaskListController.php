<?php

namespace Kanboard\Controller;

use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Model\TaskModel;

/**
 * Task List Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class TaskListController extends BaseController
{
    /**
     * Show list view for projects
     *
     * @access public
     */
    public function show()
    {
        $project = $this->getProject();
        $search = $this->helper->projectHeader->getSearchQuery($project);

        if ($this->request->getIntegerParam('show_subtasks')) {
            $this->sessionStorage->subtaskListToggle = true;
        } elseif ($this->request->getIntegerParam('hide_subtasks')) {
            $this->sessionStorage->subtaskListToggle = false;
        }

        if ($this->userSession->hasSubtaskListActivated()) {
            $formatter = $this->taskListSubtaskFormatter;
        } else {
            $formatter = $this->taskListFormatter;
        }

        $paginator = $this->paginator
            ->setUrl('TaskListController', 'show', array('project_id' => $project['id']))
            ->setMax(30)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setDirection('DESC')
            ->setFormatter($formatter)
            ->setQuery($this->taskLexer
                ->build($search)
                ->withFilter(new TaskProjectFilter($project['id']))
                ->getQuery()
            )
            ->calculate();

        $this->response->html($this->helper->layout->app('task_list/listing', array(
            'project'     => $project,
            'title'       => $project['name'],
            'description' => $this->helper->projectHeader->getDescription($project),
            'paginator'   => $paginator,
        )));
    }
}
