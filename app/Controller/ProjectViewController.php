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
        $this->checkCSRFForm();

        $project = $this->getProject();
        $values = $this->request->getRawFormValues();

        $project_id = $this->projectDuplicationModel->duplicate($project['id'], array_keys($values), $this->userSession->getId());

        if ($project_id !== false) {
            $this->flash->success(t('Project cloned successfully.'));
        } else {
            $this->flash->failure(t('Unable to clone this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectViewController', 'show', array('project_id' => $project_id)));
    }

    /**
     * Import another project's tasks into the currently opened project.
     *
     * @return void
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function importTasks()
    {
        $project = $this->getProject();

        // Fetch list of projects to copy tasks from.
        // Remove current project from the list of the user's projects.
        $otherProjects = array_filter(
            $this->projectUserRoleModel->getActiveProjectsByUser($this->getUser()['id']),
            static function ($projectId) use ($project) {
                return (int) $project['id'] !== $projectId;
            },
            ARRAY_FILTER_USE_KEY
        );

        $this->response->html($this->helper->layout->project('project_view/importTasks', array(
            'project' => $project,
            'title' => t('Import Tasks'),
            'projects' => $otherProjects,
        )));
    }

    /**
     * Handle a form submission to copy tasks of a project.
     *
     * @return void
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function doTasksImport()
    {
        $this->checkCSRFForm();

        $project = $this->getProject();
        $srcProjectId = $this->request->getRawFormValues()['projects'] ?? null;

        if (empty($srcProjectId)) {
            $this->response->redirect($this->helper->url->to('ProjectViewController', 'importTasks', array('project_id' => $project['id'])));
            return;
        }

        if ($this->projectTaskDuplicationModel->duplicate($srcProjectId, $project['id'])) {
            $this->flash->success(t('Tasks copied successfully.'));
        } else {
            $this->flash->failure(t('Unable to copy tasks.'));
            $this->response->redirect($this->helper->url->to('ProjectViewController', 'importTasks', array('project_id' => $project['id'])));
            return;
        }

        $this->response->redirect($this->helper->url->to('ProjectViewController', 'show', array('project_id' => $project['id'])));
    }
}
