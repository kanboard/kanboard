<?php

namespace Kanboard\Controller;

/**
 * Class ProjectViewController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ProjectViewController extends BaseController
{
    /**
     * Show the project information page
     *
     * @access public
     */
    public function show()
    {
        $project = $this->getProject();
        $columns = $this->columnModel->getAllWithTaskCount($project['id']);

        $this->response->html($this->helper->layout->project('project_view/show', array(
            'project' => $project,
            'columns' => $columns,
            'title'   => $project['name'],
        )));
    }

    /**
     * Public access management
     *
     * @access public
     */
    public function share()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_view/share', array(
            'project' => $project,
            'title' => t('Public access'),
        )));
    }

    /**
     * Change project sharing
     *
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function updateSharing()
    {
        $project = $this->getProject();
        $this->checkCSRFParam();
        $switch = $this->request->getStringParam('switch');

        if ($this->projectModel->{$switch.'PublicAccess'}($project['id'])) {
            $this->flash->success(t('Project updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectViewController', 'share', array('project_id' => $project['id'])));
    }

    /**
     * Integrations page
     *
     * @access public
     */
    public function integrations()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_view/integrations', array(
            'project' => $project,
            'title' => t('Integrations'),
            'webhook_token' => $this->configModel->get('webhook_token'),
            'values' => $this->projectMetadataModel->getAll($project['id']),
            'errors' => array(),
        )));
    }

    /**
     * Update integrations
     *
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function updateIntegrations()
    {
        $project = $this->getProject();

        $this->projectMetadataModel->save($project['id'], $this->request->getValues());
        $this->flash->success(t('Project updated successfully.'));
        $this->response->redirect($this->helper->url->to('ProjectViewController', 'integrations', array('project_id' => $project['id'])));
    }

    /**
     * Display project notifications
     *
     * @access public
     */
    public function notifications()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_view/notifications', array(
            'notifications' => $this->projectNotificationModel->readSettings($project['id']),
            'types' => $this->projectNotificationTypeModel->getTypes(),
            'project' => $project,
            'title' => t('Notifications'),
        )));
    }

    /**
     * Update notifications
     *
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function updateNotifications()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        $this->projectNotificationModel->saveSettings($project['id'], $values);
        $this->flash->success(t('Project updated successfully.'));
        $this->response->redirect($this->helper->url->to('ProjectViewController', 'notifications', array('project_id' => $project['id'])));
    }

    /**
     * Duplicate a project
     *
     * @author Antonio Rabelo
     * @author Michael Lüpkes
     * @access public
     */
    public function duplicate()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_view/duplicate', array(
            'project' => $project,
            'title' => t('Clone this project')
        )));
    }

    /**
     * Do project duplication
     */
    public function doDuplication()
    {
        $project = $this->getProject();
        $project_id = $this->projectDuplicationModel->duplicate($project['id'], array_keys($this->request->getValues()), $this->userSession->getId());

        if ($project_id !== false) {
            $this->flash->success(t('Project cloned successfully.'));
        } else {
            $this->flash->failure(t('Unable to clone this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectViewController', 'show', array('project_id' => $project_id)));
    }
}
