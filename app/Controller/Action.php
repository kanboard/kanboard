<?php

namespace Kanboard\Controller;

/**
 * Automatic Actions
 *
 * @package controller
 * @author  Frederic Guillot
 */
class Action extends Base
{
    /**
     * List of automatic actions for a given project
     *
     * @access public
     */
    public function index()
    {
        $project = $this->getProject();
        $actions = $this->action->getAllByProject($project['id']);

        $this->response->html($this->helper->layout->project('action/index', array(
            'values' => array('project_id' => $project['id']),
            'project' => $project,
            'actions' => $actions,
            'available_actions' => $this->actionManager->getAvailableActions(),
            'available_events' => $this->eventManager->getAll(),
            'available_params' => $this->actionManager->getAvailableParameters($actions),
            'columns_list' => $this->column->getList($project['id']),
            'users_list' => $this->projectUserRole->getAssignableUsersList($project['id']),
            'projects_list' => $this->projectUserRole->getProjectsByUser($this->userSession->getId()),
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($project['id']),
            'links_list' => $this->link->getList(0, false),
            'title' => t('Automatic actions')
        )));
    }

    /**
     * Confirmation dialog before removing an action
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('action/remove', array(
            'action' => $this->action->getById($this->request->getIntegerParam('action_id')),
            'available_events' => $this->eventManager->getAll(),
            'available_actions' => $this->actionManager->getAvailableActions(),
            'project' => $project,
            'title' => t('Remove an action')
        )));
    }

    /**
     * Remove an action
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $action = $this->action->getById($this->request->getIntegerParam('action_id'));

        if (! empty($action) && $this->action->remove($action['id'])) {
            $this->flash->success(t('Action removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this action.'));
        }

        $this->response->redirect($this->helper->url->to('action', 'index', array('project_id' => $project['id'])));
    }
}
