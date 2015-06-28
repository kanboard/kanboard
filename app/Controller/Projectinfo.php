<?php

namespace Controller;

/**
 * Project Info controller (ActivityStream + completed tasks)
 *
 * @package controller
 * @author  Frederic Guillot
 */
class Projectinfo extends Base
{
    /**
     * Activity page for a project
     *
     * @access public
     */
    public function activity()
    {
        $project = $this->getProject();

        $this->response->html($this->template->layout('projectinfo/activity', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
            'events' => $this->projectActivity->getProject($project['id']),
            'project' => $project,
            'title' => t('%s\'s activity', $project['name'])
        )));
    }

    /**
     * Task search for a given project
     *
     * @access public
     */
    public function search()
    {
        $project = $this->getProject();
        $search = $this->request->getStringParam('search');
        $nb_tasks = 0;

        $paginator = $this->paginator
                ->setUrl('projectinfo', 'search', array('search' => $search, 'project_id' => $project['id']))
                ->setMax(30)
                ->setOrder('tasks.id')
                ->setDirection('DESC');

        if ($search !== '') {

            // $paginator
            //     ->setQuery($this->taskFinder->getSearchQuery($project['id'], $search))
            //     ->calculate();

            $paginator->setQuery($this->taskFilter->search($search)->filterByProject($project['id'])->getQuery())->calculate();

            $nb_tasks = $paginator->getTotal();
        }

        $this->response->html($this->template->layout('projectinfo/search', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
            'values' => array(
                'search' => $search,
                'controller' => 'projectinfo',
                'action' => 'search',
                'project_id' => $project['id'],
            ),
            'paginator' => $paginator,
            'project' => $project,
            'columns' => $this->board->getColumnsList($project['id']),
            'categories' => $this->category->getList($project['id'], false),
            'title' => t('Search in the project "%s"', $project['name']).($nb_tasks > 0 ? ' ('.$nb_tasks.')' : '')
        )));
    }

    /**
     * List of completed tasks for a given project
     *
     * @access public
     */
    public function tasks()
    {
        $project = $this->getProject();
        $paginator = $this->paginator
                ->setUrl('projectinfo', 'tasks', array('project_id' => $project['id']))
                ->setMax(30)
                ->setOrder('tasks.id')
                ->setDirection('DESC')
                ->setQuery($this->taskFinder->getClosedTaskQuery($project['id']))
                ->calculate();

        $this->response->html($this->template->layout('projectinfo/tasks', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
            'project' => $project,
            'columns' => $this->board->getColumnsList($project['id']),
            'categories' => $this->category->getList($project['id'], false),
            'paginator' => $paginator,
            'title' => t('Completed tasks for "%s"', $project['name']).' ('.$paginator->getTotal().')'
        )));
    }
}
