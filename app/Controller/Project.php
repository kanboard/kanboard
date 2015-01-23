<?php

namespace Controller;

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
        $projects = $this->project->getAll(! $this->userSession->isAdmin());
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
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
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
     * Integrations page
     *
     * @access public
     */
    public function integration()
    {
        $project = $this->getProject();

        $this->response->html($this->projectLayout('project/integrations', array(
            'project' => $project,
            'title' => t('Integrations'),
            'webhook_token' => $this->config->get('webhook_token'),
        )));
    }

    /**
     * Display a form to edit a project
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

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
        $project = $this->getProject();
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
        $project = $this->getProject();

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
        $project = $this->getProject();
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

            if ($this->projectPermission->addMember($values['project_id'], $values['user_id'])) {
                $this->session->flash(t('Project updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->redirect('?controller=project&action=users&project_id='.$values['project_id']);
    }

    /**
     * Change the role of a project member
     *
     * @access public
     */
    public function role()
    {
        $this->checkCSRFParam();

        $values = array(
            'project_id' => $this->request->getIntegerParam('project_id'),
            'user_id' => $this->request->getIntegerParam('user_id'),
            'is_owner' => $this->request->getIntegerParam('is_owner'),
        );

        list($valid,) = $this->projectPermission->validateUserModification($values);

        if ($valid) {

            if ($this->projectPermission->changeRole($values['project_id'], $values['user_id'], $values['is_owner'])) {
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

            if ($this->projectPermission->revokeMember($values['project_id'], $values['user_id'])) {
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
        $project = $this->getProject();

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
        $project = $this->getProject();

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
        $project = $this->getProject();

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
        $project = $this->getProject();

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

        $this->response->xml($this->template->render('project/feed', array(
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
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
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
        $nb_tasks = 0;

        $paginator = $this->paginator
                ->setUrl('project', 'search', array('search' => $search, 'project_id' => $project['id']))
                ->setMax(30)
                ->setOrder('tasks.id')
                ->setDirection('DESC');

        if ($search !== '') {

            $paginator
                ->setQuery($this->taskFinder->getSearchQuery($project['id'], $search))
                ->calculate();

            $nb_tasks = $paginator->getTotal();
        }

        $this->response->html($this->template->layout('project/search', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
            'values' => array(
                'search' => $search,
                'controller' => 'project',
                'action' => 'search',
                'project_id' => $project['id'],
            ),
            'paginator' => $paginator,
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
        $paginator = $this->paginator
                ->setUrl('project', 'tasks', array('project_id' => $project['id']))
                ->setMax(30)
                ->setOrder('tasks.id')
                ->setDirection('DESC')
                ->setQuery($this->taskFinder->getClosedTaskQuery($project['id']))
                ->calculate();

        $this->response->html($this->template->layout('project/tasks', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
            'project' => $project,
            'columns' => $this->board->getColumnsList($project['id']),
            'categories' => $this->category->getList($project['id'], false),
            'paginator' => $paginator,
            'title' => t('Completed tasks for "%s"', $project['name']).' ('.$paginator->getTotal().')'
        )));
    }

    /**
     * Display a form to create a new project
     *
     * @access public
     */
    public function create(array $values = array(), array $errors = array())
    {
        $is_private = $this->request->getIntegerParam('private', $this->userSession->isAdmin() ? 0 : 1);

        $this->response->html($this->template->layout('project/new', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
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

            $project_id = $this->project->create($values, $this->userSession->getId(), true);

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
