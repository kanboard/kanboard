<?php

namespace Kanboard\Controller;

/**
 * Automatic actions management
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

        $this->response->html($this->projectLayout('action/index', array(
            'values' => array('project_id' => $project['id']),
            'project' => $project,
            'actions' => $actions,
            'available_actions' => $this->actionManager->getAvailableActions(),
            'available_events' => $this->eventManager->getAll(),
            'available_params' => $this->actionManager->getAvailableParameters($actions),
            'columns_list' => $this->board->getColumnsList($project['id']),
            'users_list' => $this->projectUserRole->getAssignableUsersList($project['id']),
            'projects_list' => $this->projectUserRole->getProjectsByUser($this->userSession->getId()),
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($project['id']),
            'links_list' => $this->link->getList(0, false),
            'title' => t('Automatic actions')
        )));
    }

    /**
     * Choose the event according to the action (step 2)
     *
     * @access public
     */
    public function event()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        if (empty($values['action_name']) || empty($values['project_id'])) {
            $this->response->redirect($this->helper->url->to('action', 'index', array('project_id' => $project['id'])));
        }

        $this->response->html($this->projectLayout('action/event', array(
            'values' => $values,
            'project' => $project,
            'events' => $this->actionManager->getCompatibleEvents($values['action_name']),
            'title' => t('Automatic actions')
        )));
    }

    /**
     * Define action parameters (step 3)
     *
     * @access public
     */
    public function params()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        if (empty($values['action_name']) || empty($values['project_id']) || empty($values['event_name'])) {
            $this->response->redirect($this->helper->url->to('action', 'index', array('project_id' => $project['id'])));
        }

        $action = $this->actionManager->getAction($values['action_name']);
        $action_params = $action->getActionRequiredParameters();

        if (empty($action_params)) {
            $this->doCreation($project, $values + array('params' => array()));
        }

        $projects_list = $this->projectUserRole->getActiveProjectsByUser($this->userSession->getId());
        unset($projects_list[$project['id']]);

        $this->response->html($this->projectLayout('action/params', array(
            'values' => $values,
            'action_params' => $action_params,
            'columns_list' => $this->board->getColumnsList($project['id']),
            'users_list' => $this->projectUserRole->getAssignableUsersList($project['id']),
            'projects_list' => $projects_list,
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($project['id']),
            'links_list' => $this->link->getList(0, false),
            'project' => $project,
            'title' => t('Automatic actions')
        )));
    }

    /**
     * Create a new action (last step)
     *
     * @access public
     */
    public function create()
    {
        $this->doCreation($this->getProject(), $this->request->getValues());
    }

    /**
     * Save the action
     *
     * @access private
     * @param  array     $project   Project properties
     * @param  array     $values    Form values
     */
    private function doCreation(array $project, array $values)
    {
        list($valid, ) = $this->actionValidator->validateCreation($values);

        if ($valid) {
            if ($this->action->create($values) !== false) {
                $this->flash->success(t('Your automatic action have been created successfully.'));
            } else {
                $this->flash->failure(t('Unable to create your automatic action.'));
            }
        }

        $this->response->redirect($this->helper->url->to('action', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Confirmation dialog before removing an action
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();

        $this->response->html($this->projectLayout('action/remove', array(
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
