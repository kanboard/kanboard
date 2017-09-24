<?php

namespace Kanboard\Controller;

/**
 * Automatic Actions Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ActionController extends BaseController
{
    /**
     * List of automatic actions for a given project
     *
     * @access public
     */
    public function index()
    {
        $project = $this->getProject();
        $actions = $this->actionModel->getAllByProject($project['id']);

        $this->response->html($this->helper->layout->project('action/index', array(
            'values' => array('project_id' => $project['id']),
            'project' => $project,
            'actions' => $actions,
            'available_actions' => $this->actionManager->getAvailableActions(),
            'available_events' => $this->eventManager->getAll(),
            'available_params' => $this->actionManager->getAvailableParameters($actions),
            'columns_list' => $this->columnModel->getList($project['id']),
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id']),
            'projects_list' => $this->projectUserRoleModel->getProjectsByUser($this->userSession->getId()),
            'colors_list' => $this->colorModel->getList(),
            'categories_list' => $this->categoryModel->getList($project['id']),
            'links_list' => $this->linkModel->getList(0, false),
            'swimlane_list' => $this->swimlaneModel->getList($project['id']),
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
        $action = $this->getAction($project);

        $this->response->html($this->helper->layout->project('action/remove', array(
            'action' => $action,
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
        $action = $this->getAction($project);

        if (! empty($action) && $this->actionModel->remove($action['id'])) {
            $this->flash->success(t('Action removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this action.'));
        }

        $this->response->redirect($this->helper->url->to('ActionController', 'index', array('project_id' => $project['id'])));
    }
}
