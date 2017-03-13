<?php

namespace Kanboard\Controller;

/**
 * Class ProjectStatusController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ProjectStatusController extends BaseController
{
    /**
     * Enable a project (confirmation dialog box)
     */
    public function confirmEnable()
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('project_status/enable', array(
            'project' => $project,
        )));
    }

    /**
     * Enable the project
     */
    public function enable()
    {
        $project = $this->getProject();
        $this->checkCSRFParam();

        if ($this->projectModel->enable($project['id'])) {
            $this->flash->success(t('Project activated successfully.'));
        } else {
            $this->flash->failure(t('Unable to activate this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectViewController', 'show', array('project_id' => $project['id'])), true);
    }

    /**
     * Disable a project (confirmation dialog box)
     */
    public function confirmDisable()
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('project_status/disable', array(
            'project' => $project,
        )));
    }

    /**
     * Disable a project
     */
    public function disable()
    {
        $project = $this->getProject();
        $this->checkCSRFParam();

        if ($this->projectModel->disable($project['id'])) {
            $this->flash->success(t('Project disabled successfully.'));
        } else {
            $this->flash->failure(t('Unable to disable this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectViewController', 'show', array('project_id' => $project['id'])), true);
    }

    /**
     * Remove a project (confirmation dialog box)
     */
    public function confirmRemove()
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('project_status/remove', array(
            'project' => $project,
            'title' => t('Remove project')
        )));
    }

    /**
     * Remove a project
     */
    public function remove()
    {
        $project = $this->getProject();
        $this->checkCSRFParam();

        if ($this->projectModel->remove($project['id'])) {
            $this->flash->success(t('Project removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectListController', 'show'));
    }
}
