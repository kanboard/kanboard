<?php

namespace Controller;

use Model\Task as TaskModel;

/**
 * Project controller
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
        $projects = $this->project->getAll($this->acl->isRegularUser());
        $nb_projects = count($projects);
        $active_projects = array();
        $inactive_projects = array();

        foreach ($projects as $project) {
            if ($project['is_active'] == 1) {
                $active_projects[] = $project;
            }
            else {
                $inactive_projects[] = $project;
            }
        }

        $this->response->html($this->template->layout('project_index', array(
            'active_projects' => $active_projects,
            'inactive_projects' => $inactive_projects,
            'nb_projects' => $nb_projects,
            'menu' => 'projects',
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

        $this->response->html($this->projectLayout('project_show', array(
            'project' => $project,
            'stats' => $this->project->getStats($project['id']),
            'title' => $project['name'],
        )));
    }

    /**
     * Task export
     *
     * @access public
     */
    public function export()
    {
        $project = $this->getProjectManagement();
        $from = $this->request->getStringParam('from');
        $to = $this->request->getStringParam('to');

        if ($from && $to) {
            $data = $this->taskExport->export($project['id'], $from, $to);
            $this->response->forceDownload('Export_'.date('Y_m_d_H_i_S').'.csv');
            $this->response->csv($data);
        }

        $this->response->html($this->projectLayout('project_export', array(
            'values' => array(
                'controller' => 'project',
                'action' => 'export',
                'project_id' => $project['id'],
                'from' => $from,
                'to' => $to,
            ),
            'errors' => array(),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'project' => $project,
            'title' => t('Tasks Export')
        )));
    }

    /**
     * Public access management
     *
     * @access public
     */
    public function share()
    {
        $project = $this->getProjectManagement();
        $switch = $this->request->getStringParam('switch');

        if ($switch === 'enable' || $switch === 'disable') {

            $this->checkCSRFParam();

            if ($this->project->{$switch.'PublicAccess'}($project['id'])) {
                $this->session->flash(t('Project updated successfully.'));
            } else {
                $this->session->flashError(t('Unable to update this project.'));
            }

            $this->response->redirect('?controller=project&action=share&project_id='.$project['id']);
        }

        $this->response->html($this->projectLayout('project_share', array(
            'project' => $project,
            'title' => t('Public access'),
        )));
    }

    /**
     * Display a form to edit a project
     *
     * @access public
     */
    public function edit()
    {
        $project = $this->getProjectManagement();

        $this->response->html($this->projectLayout('project_edit', array(
            'errors' => array(),
            'values' => $project,
            'project' => $project,
            'title' => t('Edit project')
        )));
    }

    /**
     * Validate and update a project
     *
     * @access public
     */
    public function update()
    {
        $project = $this->getProjectManagement();
        $values = $this->request->getValues() + array('is_active' => 0);
        list($valid, $errors) = $this->project->validateModification($values);

        if ($valid) {

            if ($this->project->update($values)) {
                $this->session->flash(t('Project updated successfully.'));
                $this->response->redirect('?controller=project&action=edit&project_id='.$project['id']);
            }
            else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->html($this->projectLayout('project_edit', array(
            'errors' => $errors,
            'values' => $values,
            'project' => $project,
            'title' => t('Edit Project')
        )));
    }

    /**
     * Users list for the selected project
     *
     * @access public
     */
    public function users()
    {
        $project = $this->getProjectManagement();

        $this->response->html($this->projectLayout('project_users', array(
            'project' => $project,
            'users' => $this->projectPermission->getAllUsers($project['id']),
            'title' => t('Edit project access list')
        )));
    }

    /**
     * Allow a specific user (admin only)
     *
     * @access public
     */
    public function allow()
    {
        $values = $this->request->getValues();
        list($valid,) = $this->projectPermission->validateModification($values);

        if ($valid) {

            if ($this->projectPermission->allowUser($values['project_id'], $values['user_id'])) {
                $this->session->flash(t('Project updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->redirect('?controller=project&action=users&project_id='.$values['project_id']);
    }

    /**
     * Revoke user access (admin only)
     *
     * @access public
     */
    public function revoke()
    {
        $this->checkCSRFParam();

        $values = array(
            'project_id' => $this->request->getIntegerParam('project_id'),
            'user_id' => $this->request->getIntegerParam('user_id'),
        );

        list($valid,) = $this->projectPermission->validateModification($values);

        if ($valid) {

            if ($this->projectPermission->revokeUser($values['project_id'], $values['user_id'])) {
                $this->session->flash(t('Project updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->redirect('?controller=project&action=users&project_id='.$values['project_id']);
    }

    /**
     * Remove a project
     *
     * @access public
     */
    public function remove()
    {
        $project = $this->getProjectManagement();

        if ($this->request->getStringParam('remove') === 'yes') {

            $this->checkCSRFParam();

            if ($this->project->remove($project['id'])) {
                $this->session->flash(t('Project removed successfully.'));
            } else {
                $this->session->flashError(t('Unable to remove this project.'));
            }

            $this->response->redirect('?controller=project');
        }

        $this->response->html($this->projectLayout('project_remove', array(
            'project' => $project,
            'title' => t('Remove project')
        )));
    }

    /**
     * Duplicate a project
     *
     * @author Antonio Rabelo
     * @access public
     */
    public function duplicate()
    {
        $project = $this->getProjectManagement();

        if ($this->request->getStringParam('duplicate') === 'yes') {

            $this->checkCSRFParam();

            if ($this->project->duplicate($project['id'])) {
                $this->session->flash(t('Project cloned successfully.'));
            } else {
                $this->session->flashError(t('Unable to clone this project.'));
            }

            $this->response->redirect('?controller=project');
        }

        $this->response->html($this->projectLayout('project_duplicate', array(
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
        $project = $this->getProjectManagement();

        if ($this->request->getStringParam('disable') === 'yes') {

            $this->checkCSRFParam();

            if ($this->project->disable($project['id'])) {
                $this->session->flash(t('Project disabled successfully.'));
            } else {
                $this->session->flashError(t('Unable to disable this project.'));
            }

            $this->response->redirect('?controller=project&action=show&project_id='.$project['id']);
        }

        $this->response->html($this->projectLayout('project_disable', array(
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
        $project = $this->getProjectManagement();

        if ($this->request->getStringParam('enable') === 'yes') {

            $this->checkCSRFParam();

            if ($this->project->enable($project['id'])) {
                $this->session->flash(t('Project activated successfully.'));
            } else {
                $this->session->flashError(t('Unable to activate this project.'));
            }

            $this->response->redirect('?controller=project&action=show&project_id='.$project['id']);
        }

        $this->response->html($this->projectLayout('project_enable', array(
            'project' => $project,
            'title' => t('Project activation')
        )));
    }

    /**
     * RSS feed for a project (public)
     *
     * @access public
     */
    public function feed()
    {
        $token = $this->request->getStringParam('token');
        $project = $this->project->getByToken($token);

        // Token verification
        if (! $project) {
            $this->forbidden(true);
        }

        $this->response->xml($this->template->load('project_feed', array(
            'events' => $this->project->getActivity($project['id']),
            'project' => $project,
        )));
    }

    /**
     * Activity page for a project
     *
     * @access public
     */
    public function activity()
    {
        $project = $this->getProject();

        $this->response->html($this->template->layout('project_activity', array(
            'events' => $this->project->getActivity($project['id']),
            'menu' => 'projects',
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
        $tasks = array();
        $nb_tasks = 0;

        if ($search !== '') {

            $filters = array(
                array('column' => 'project_id', 'operator' => 'eq', 'value' => $project['id']),
                'or' => array(
                    array('column' => 'title', 'operator' => 'like', 'value' => '%'.$search.'%'),
                    //array('column' => 'description', 'operator' => 'like', 'value' => '%'.$search.'%'),
                )
            );

            $tasks = $this->task->find($filters);
            $nb_tasks = count($tasks);
        }

        $this->response->html($this->template->layout('project_search', array(
            'tasks' => $tasks,
            'nb_tasks' => $nb_tasks,
            'values' => array(
                'search' => $search,
                'controller' => 'project',
                'action' => 'search',
                'project_id' => $project['id'],
            ),
            'project' => $project,
            'columns' => $this->board->getColumnsList($project['id']),
            'categories' => $this->category->getList($project['id'], false),
            'title' => $project['name'].($nb_tasks > 0 ? ' ('.$nb_tasks.')' : '')
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

        $filters = array(
            array('column' => 'project_id', 'operator' => 'eq', 'value' => $project['id']),
            array('column' => 'is_active', 'operator' => 'eq', 'value' => TaskModel::STATUS_CLOSED),
        );

        $tasks = $this->task->find($filters);
        $nb_tasks = count($tasks);

        $this->response->html($this->template->layout('project_tasks', array(
            'project' => $project,
            'columns' => $this->board->getColumnsList($project['id']),
            'categories' => $this->category->getList($project['id'], false),
            'tasks' => $tasks,
            'nb_tasks' => $nb_tasks,
            'title' => $project['name'].' ('.$nb_tasks.')'
        )));
    }

    /**
     * Display a form to create a new project
     *
     * @access public
     */
    public function create()
    {
        $this->response->html($this->template->layout('project_new', array(
            'errors' => array(),
            'values' => array(
                'is_private' => $this->request->getIntegerParam('private', $this->acl->isRegularUser()),
            ),
            'title' => t('New project')
        )));
    }

    /**
     * Validate and save a new project
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->project->validateCreation($values);

        if ($valid) {

            if ($this->project->create($values, $this->acl->getUserId())) {
                $this->session->flash(t('Your project have been created successfully.'));
                $this->response->redirect('?controller=project');
            }
            else {
                $this->session->flashError(t('Unable to create your project.'));
            }
        }

        $this->response->html($this->template->layout('project_new', array(
            'errors' => $errors,
            'values' => $values,
            'title' => t('New Project')
        )));
    }
}
