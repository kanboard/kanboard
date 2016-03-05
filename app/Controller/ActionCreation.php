<?php

namespace Kanboard\Controller;

/**
 * Action Creation
 *
 * @package controller
 * @author  Frederic Guillot
 */
class ActionCreation extends Base
{
    /**
     * Show the form (step 1)
     *
     * @access public
     */
    public function create()
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('action_creation/create', array(
            'project' => $project,
            'values' => array('project_id' => $project['id']),
            'available_actions' => $this->actionManager->getAvailableActions(),
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
            return $this->create();
        }

        $this->response->html($this->template->render('action_creation/event', array(
            'values' => $values,
            'project' => $project,
            'available_actions' => $this->actionManager->getAvailableActions(),
            'events' => $this->actionManager->getCompatibleEvents($values['action_name']),
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
            return $this->create();
        }

        $action = $this->actionManager->getAction($values['action_name']);
        $action_params = $action->getActionRequiredParameters();

        if (empty($action_params)) {
            $this->doCreation($project, $values + array('params' => array()));
        }

        $projects_list = $this->projectUserRole->getActiveProjectsByUser($this->userSession->getId());
        unset($projects_list[$project['id']]);

        $this->response->html($this->template->render('action_creation/params', array(
            'values' => $values,
            'action_params' => $action_params,
            'columns_list' => $this->column->getList($project['id']),
            'users_list' => $this->projectUserRole->getAssignableUsersList($project['id']),
            'projects_list' => $projects_list,
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($project['id']),
            'links_list' => $this->link->getList(0, false),
            'project' => $project,
            'available_actions' => $this->actionManager->getAvailableActions(),
            'events' => $this->actionManager->getCompatibleEvents($values['action_name']),
        )));
    }

    /**
     * Save the action (last step)
     *
     * @access public
     */
    public function save()
    {
        $this->doCreation($this->getProject(), $this->request->getValues());
    }

    /**
     * Common method to save the action
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
}
