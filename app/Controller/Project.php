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

        $this->response->html($this->template->layout('project/index', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->acl->getUserId()),
            'active_projects' => $active_projects,
            'inactive_projects' => $inactive_projects,
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

        $this->response->html($this->projectLayout('project/show', array(
            'project' => $project,
            'stats' => $this->project->getStats($project['id']),
            'webhook_token' => $this->config->get('webhook_token'),
            'title' => $project['name'],
        )));
    }

    /**
     * Task export
     *
     * @access public
     */
    public function exportTasks()
    {
        $project = $this->getProjectManagement();
        $from = $this->request->getStringParam('from');
        $to = $this->request->getStringParam('to');

        if ($from && $to) {
            $data = $this->taskExport->export($project['id'], $from, $to);
            $this->response->forceDownload('Tasks_'.date('Y_m_d_H_i').'.csv');
            $this->response->csv($data);
        }

        $this->response->html($this->projectLayout('project/export_tasks', array(
            'values' => array(
                'controller' => 'project',
                'action' => 'exportTasks',
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
     * Daily project summary export
     *
     * @access public
     */
    public function exportDailyProjectSummary()
    {
        $project = $this->getProjectManagement();
        $from = $this->request->getStringParam('from');
        $to = $this->request->getStringParam('to');

        if ($from && $to) {
            $data = $this->projectDailySummary->getAggregatedMetrics($project['id'], $from, $to);
            $this->response->forceDownload('Daily_Summary_'.date('Y_m_d_H_i').'.csv');
            $this->response->csv($data);
        }

        $this->response->html($this->projectLayout('project/export_daily_summary', array(
            'values' => array(
                'controller' => 'project',
                'action' => 'exportDailyProjectSummary',
                'project_id' => $project['id'],
                'from' => $from,
                'to' => $to,
            ),
            'errors' => array(),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'project' => $project,
            'title' => t('Daily project summary export')
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

        $this->response->html($this->projectLayout('project/share', array(
            'project' => $project,
            'title' => t('Public access'),
        )));
    }

    /**
     * Display a form to edit a project
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProjectManagement();

        $this->response->html($this->projectLayout('project/edit', array(
            'values' => empty($values) ? $project : $values,
            'errors' => $errors,
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
        $values = $this->request->getValues();
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

        $this->edit($values, $errors);
    }

    /**
     * Users list for the selected project
     *
     * @access public
     */
    public function users()
    {
        $project = $this->getProjectManagement();

        $this->response->html($this->projectLayout('project/users', array(
            'project' => $project,
            'users' => $this->projectPermission->getAllUsers($project['id']),
            'title' => t('Edit project access list')
        )));
    }

    /**
     * Allow everybody
     *
     * @access public
     */
    public function allowEverybody()
    {
        $project = $this->getProjectManagement();
        $values = $this->request->getValues() + array('is_everybody_allowed' => 0);
        list($valid,) = $this->projectPermission->validateProjectModification($values);

        if ($valid) {

            if ($this->project->update($values)) {
                $this->session->flash(t('Project updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->redirect('?controller=project&action=users&project_id='.$project['id']);
    }

    /**
     * Allow a specific user (admin only)
     *
     * @access public
     */
    public function allow()
    {
        $values = $this->request->getValues();
        list($valid,) = $this->projectPermission->validateUserModification($values);

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

        list($valid,) = $this->projectPermission->validateUserModification($values);

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

        $this->response->html($this->projectLayout('project/remove', array(
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

        $this->response->html($this->projectLayout('project/duplicate', array(
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

        $this->response->html($this->projectLayout('project/disable', array(
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

        $this->response->html($this->projectLayout('project/enable', array(
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

        $this->response->xml($this->template->load('project/feed', array(
            'events' => $this->projectActivity->getProject($project['id']),
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

        $this->response->html($this->template->layout('project/activity', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->acl->getUserId()),
            'events' => $this->projectActivity->getProject($project['id']),
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
        $direction = $this->request->getStringParam('direction', 'DESC');
        $order = $this->request->getStringParam('order', 'tasks.id');
        $offset = $this->request->getIntegerParam('offset', 0);
        $tasks = array();
        $nb_tasks = 0;
        $limit = 25;

        if ($search !== '') {
            $tasks = $this->taskPaginator->searchTasks($project['id'], $search, $offset, $limit, $order, $direction);
            $nb_tasks = $this->taskPaginator->countSearchTasks($project['id'], $search);
        }

        $this->response->html($this->template->layout('project/search', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->acl->getUserId()),
            'tasks' => $tasks,
            'nb_tasks' => $nb_tasks,
            'pagination' => array(
                'controller' => 'project',
                'action' => 'search',
                'params' => array('search' => $search, 'project_id' => $project['id']),
                'direction' => $direction,
                'order' => $order,
                'total' => $nb_tasks,
                'offset' => $offset,
                'limit' => $limit,
            ),
            'values' => array(
                'search' => $search,
                'controller' => 'project',
                'action' => 'search',
                'project_id' => $project['id'],
            ),
            'project' => $project,
            'columns' => $this->board->getColumnsList($project['id']),
            'categories' => $this->category->getList($project['id'], false),
            'title' => t('Search in the project "%s"', $project['name']).($nb_tasks > 0 ? ' ('.$nb_tasks.')' : '')
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
        $direction = $this->request->getStringParam('direction', 'DESC');
        $order = $this->request->getStringParam('order', 'tasks.date_completed');
        $offset = $this->request->getIntegerParam('offset', 0);
        $limit = 25;

        $tasks = $this->taskPaginator->closedTasks($project['id'], $offset, $limit, $order, $direction);
        $nb_tasks = $this->taskPaginator->countClosedTasks($project['id']);

        $this->response->html($this->template->layout('project/tasks', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->acl->getUserId()),
            'pagination' => array(
                'controller' => 'project',
                'action' => 'tasks',
                'params' => array('project_id' => $project['id']),
                'direction' => $direction,
                'order' => $order,
                'total' => $nb_tasks,
                'offset' => $offset,
                'limit' => $limit,
            ),
            'project' => $project,
            'columns' => $this->board->getColumnsList($project['id']),
            'categories' => $this->category->getList($project['id'], false),
            'tasks' => $tasks,
            'nb_tasks' => $nb_tasks,
            'title' => t('Completed tasks for "%s"', $project['name']).' ('.$nb_tasks.')'
        )));
    }

    /**
     * Display a form to create a new project
     *
     * @access public
     */
    public function create(array $values = array(), array $errors = array())
    {
        $is_private = $this->request->getIntegerParam('private', $this->acl->isRegularUser());

        $this->response->html($this->template->layout('project/new', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->acl->getUserId()),
            'values' => empty($values) ? array('is_private' => $is_private) : $values,
            'errors' => $errors,
            'title' => $is_private ? t('New private project') : t('New project'),
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

            $project_id = $this->project->create($values, $this->acl->getUserId(), true);

            if ($project_id) {
                $this->session->flash(t('Your project have been created successfully.'));
                $this->response->redirect('?controller=project&action=show&project_id='.$project_id);
            }
            else {
                $this->session->flashError(t('Unable to create your project.'));
            }
        }

        $this->create($values, $errors);
    }
}
