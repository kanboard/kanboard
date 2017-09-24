<?php

namespace Kanboard\Controller;

/**
 * Action Creation Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ActionCreationController extends BaseController
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
        $values['project_id'] = $project['id'];

        if (empty($values['action_name'])) {
            return $this->create();
        }

        return $this->response->html($this->template->render('action_creation/event', array(
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
        $values['project_id'] = $project['id'];

        if (empty($values['action_name']) || empty($values['event_name'])) {
            $this->create();
            return;
        }

        $action = $this->actionManager->getAction($values['action_name']);
        $action_params = $action->getActionRequiredParameters();

        if (empty($action_params)) {
            $this->doCreation($project, $values + array('params' => array()));
        }

        $projects_list = $this->projectUserRoleModel->getActiveProjectsByUser($this->userSession->getId());
        unset($projects_list[$project['id']]);

        $this->response->html($this->template->render('action_creation/params', array(
            'values' => $values,
            'action_params' => $action_params,
            'columns_list' => $this->columnModel->getList($project['id']),
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id']),
            'projects_list' => $projects_list,
            'colors_list' => $this->colorModel->getList(),
            'categories_list' => $this->categoryModel->getList($project['id']),
            'links_list' => $this->linkModel->getList(0, false),
            'priorities_list' => $this->projectTaskPriorityModel->getPriorities($project),
            'project' => $project,
            'available_actions' => $this->actionManager->getAvailableActions(),
            'swimlane_list' => $this->swimlaneModel->getList($project['id']),
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
        $values['project_id'] = $project['id'];
        list($valid, ) = $this->actionValidator->validateCreation($values);

        if ($valid) {
            if ($this->actionModel->create($values) !== false) {
                $this->flash->success(t('Your automatic action have been created successfully.'));
            } else {
                $this->flash->failure(t('Unable to create your automatic action.'));
            }
        }

        $this->response->redirect($this->helper->url->to('ActionController', 'index', array('project_id' => $project['id'])));
    }
}
