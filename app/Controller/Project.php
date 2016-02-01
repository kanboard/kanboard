<?php

namespace Kanboard\Controller;

/**
 * Project controller (Settings + creation/edition)
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Project extends Base
{
    /**
     * List of projects
     *
     * @access public
     */
    public function index()
    {
        if ($this->userSession->isAdmin()) {
            $project_ids = $this->project->getAllIds();
        } else {
            $project_ids = $this->projectPermission->getActiveProjectIds($this->userSession->getId());
        }

        $nb_projects = count($project_ids);

        $paginator = $this->paginator
            ->setUrl('project', 'index')
            ->setMax(20)
            ->setOrder('name')
            ->setQuery($this->project->getQueryColumnStats($project_ids))
            ->calculate();

        $this->response->html($this->helper->layout->app('project/index', array(
            'paginator' => $paginator,
            'nb_projects' => $nb_projects,
            'title' => t('Projects').' ('.$nb_projects.')'
        )));
    }

    /**
     * Show the project information page
     *
     * @access public
     */
    public function show()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project/show', array(
            'project' => $project,
            'stats' => $this->project->getTaskStats($project['id']),
            'title' => $project['name'],
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
        $switch = $this->request->getStringParam('switch');

        if ($switch === 'enable' || $switch === 'disable') {
            $this->checkCSRFParam();

            if ($this->project->{$switch.'PublicAccess'}($project['id'])) {
                $this->flash->success(t('Project updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update this project.'));
            }

            $this->response->redirect($this->helper->url->to('project', 'share', array('project_id' => $project['id'])));
        }

        $this->response->html($this->helper->layout->project('project/share', array(
            'project' => $project,
            'title' => t('Public access'),
        )));
    }

    /**
     * Integrations page
     *
     * @access public
     */
    public function integrations()
    {
        $project = $this->getProject();

        if ($this->request->isPost()) {
            $this->projectMetadata->save($project['id'], $this->request->getValues());
            $this->flash->success(t('Project updated successfully.'));
            $this->response->redirect($this->helper->url->to('project', 'integrations', array('project_id' => $project['id'])));
        }

        $this->response->html($this->helper->layout->project('project/integrations', array(
            'project' => $project,
            'title' => t('Integrations'),
            'webhook_token' => $this->config->get('webhook_token'),
            'values' => $this->projectMetadata->getAll($project['id']),
            'errors' => array(),
        )));
    }

    /**
     * Display project notifications
     *
     * @access public
     */
    public function notifications()
    {
        $project = $this->getProject();

        if ($this->request->isPost()) {
            $values = $this->request->getValues();
            $this->projectNotification->saveSettings($project['id'], $values);
            $this->flash->success(t('Project updated successfully.'));
            $this->response->redirect($this->helper->url->to('project', 'notifications', array('project_id' => $project['id'])));
        }

        $this->response->html($this->helper->layout->project('project/notifications', array(
            'notifications' => $this->projectNotification->readSettings($project['id']),
            'types' => $this->projectNotificationType->getTypes(),
            'project' => $project,
            'title' => t('Notifications'),
        )));
    }

    /**
     * Remove a project
     *
     * @access public
     */
    public function remove()
    {
        $project = $this->getProject();

        if ($this->request->getStringParam('remove') === 'yes') {
            $this->checkCSRFParam();

            if ($this->project->remove($project['id'])) {
                $this->flash->success(t('Project removed successfully.'));
            } else {
                $this->flash->failure(t('Unable to remove this project.'));
            }

            $this->response->redirect($this->helper->url->to('project', 'index'));
        }

        $this->response->html($this->helper->layout->project('project/remove', array(
            'project' => $project,
            'title' => t('Remove project')
        )));
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

        if ($this->request->getStringParam('duplicate') === 'yes') {
            $project_id = $this->projectDuplication->duplicate($project['id'], array_keys($this->request->getValues()), $this->userSession->getId());

            if ($project_id !== false) {
                $this->flash->success(t('Project cloned successfully.'));
            } else {
                $this->flash->failure(t('Unable to clone this project.'));
            }

            $this->response->redirect($this->helper->url->to('project', 'show', array('project_id' => $project_id)));
        }

        $this->response->html($this->helper->layout->project('project/duplicate', array(
            'project' => $project,
            'title' => t('Clone this project')
        )));
    }

    /**
     * Disable a project
     *
     * @access public
     */
    public function disable()
    {
        $project = $this->getProject();

        if ($this->request->getStringParam('disable') === 'yes') {
            $this->checkCSRFParam();

            if ($this->project->disable($project['id'])) {
                $this->flash->success(t('Project disabled successfully.'));
            } else {
                $this->flash->failure(t('Unable to disable this project.'));
            }

            $this->response->redirect($this->helper->url->to('project', 'show', array('project_id' => $project['id'])));
        }

        $this->response->html($this->helper->layout->project('project/disable', array(
            'project' => $project,
            'title' => t('Project activation')
        )));
    }

    /**
     * Enable a project
     *
     * @access public
     */
    public function enable()
    {
        $project = $this->getProject();

        if ($this->request->getStringParam('enable') === 'yes') {
            $this->checkCSRFParam();

            if ($this->project->enable($project['id'])) {
                $this->flash->success(t('Project activated successfully.'));
            } else {
                $this->flash->failure(t('Unable to activate this project.'));
            }

            $this->response->redirect($this->helper->url->to('project', 'show', array('project_id' => $project['id'])));
        }

        $this->response->html($this->helper->layout->project('project/enable', array(
            'project' => $project,
            'title' => t('Project activation')
        )));
    }
}
