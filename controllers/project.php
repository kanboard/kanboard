<?php

namespace Controller;

require_once __DIR__.'/Base.php';

class Project extends Base
{
    // Display access forbidden page
    public function forbidden()
    {
        $this->response->html($this->template->layout('project_forbidden', array(
            'menu' => 'projects',
            'title' => t('Access Forbidden')
        )));
    }

    // List of completed tasks for a given project
    public function tasks()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $project = $this->project->getById($project_id);

        if (! $project) {
            $this->session->flashError(t('Project not found.'));
            $this->response->redirect('?controller=project');
        }

        $this->checkProjectPermissions($project['id']);

        $tasks = $this->task->getAllByProjectId($project_id, array(0));
        $nb_tasks = count($tasks);

        $this->response->html($this->template->layout('project_tasks', array(
            'menu' => 'projects',
            'project' => $project,
            'tasks' => $tasks,
            'nb_tasks' => $nb_tasks,
            'title' => $project['name'].' ('.$nb_tasks.')'
        )));
    }

    // List of projects
    public function index()
    {
        $projects = $this->project->getAll(true, $this->acl->isRegularUser());
        $nb_projects = count($projects);

        $this->response->html($this->template->layout('project_index', array(
            'projects' => $projects,
            'nb_projects' => $nb_projects,
            'menu' => 'projects',
            'title' => t('Projects').' ('.$nb_projects.')'
        )));
    }

    // Display a form to create a new project
    public function create()
    {
        $this->response->html($this->template->layout('project_new', array(
            'errors' => array(),
            'values' => array(),
            'menu' => 'projects',
            'title' => t('New project')
        )));
    }

    // Validate and save a new project
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->project->validateCreation($values);

        if ($valid) {

            if ($this->project->create($values)) {
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
            'menu' => 'projects',
            'title' => t('New Project')
        )));
    }

    // Display a form to edit a project
    public function edit()
    {
        $project = $this->project->getById($this->request->getIntegerParam('project_id'));

        if (! $project) {
            $this->session->flashError(t('Project not found.'));
            $this->response->redirect('?controller=project');
        }

        $this->response->html($this->template->layout('project_edit', array(
            'errors' => array(),
            'values' => $project,
            'menu' => 'projects',
            'title' => t('Edit project')
        )));
    }

    // Validate and update a project
    public function update()
    {
        $values = $this->request->getValues() + array('is_active' => 0);
        list($valid, $errors) = $this->project->validateModification($values);

        if ($valid) {

            if ($this->project->update($values)) {
                $this->session->flash(t('Project updated successfully.'));
                $this->response->redirect('?controller=project');
            }
            else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->html($this->template->layout('project_edit', array(
            'errors' => $errors,
            'values' => $values,
            'menu' => 'projects',
            'title' => t('Edit Project')
        )));
    }

    // Confirmation dialog before to remove a project
    public function confirm()
    {
        $project = $this->project->getById($this->request->getIntegerParam('project_id'));

        if (! $project) {
            $this->session->flashError(t('Project not found.'));
            $this->response->redirect('?controller=project');
        }

        $this->response->html($this->template->layout('project_remove', array(
            'project' => $project,
            'menu' => 'projects',
            'title' => t('Remove project')
        )));
    }

    // Remove a project
    public function remove()
    {
        $project_id = $this->request->getIntegerParam('project_id');

        if ($project_id && $this->project->remove($project_id)) {
            $this->session->flash(t('Project removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this project.'));
        }

        $this->response->redirect('?controller=project');
    }

    // Enable a project
    public function enable()
    {
        $project_id = $this->request->getIntegerParam('project_id');

        if ($project_id && $this->project->enable($project_id)) {
            $this->session->flash(t('Project activated successfully.'));
        } else {
            $this->session->flashError(t('Unable to activate this project.'));
        }

        $this->response->redirect('?controller=project');
    }

    // Disable a project
    public function disable()
    {
        $project_id = $this->request->getIntegerParam('project_id');

        if ($project_id && $this->project->disable($project_id)) {
            $this->session->flash(t('Project disabled successfully.'));
        } else {
            $this->session->flashError(t('Unable to disable this project.'));
        }

        $this->response->redirect('?controller=project');
    }

    // Users list for the selected project
    public function users()
    {
        $project = $this->project->getById($this->request->getIntegerParam('project_id'));

        if (! $project) {
            $this->session->flashError(t('Project not found.'));
            $this->response->redirect('?controller=project');
        }

        $this->response->html($this->template->layout('project_users', array(
            'project' => $project,
            'users' => $this->project->getAllUsers($project['id']),
            'menu' => 'projects',
            'title' => t('Edit project access list')
        )));
    }

    // Allow a specific user for the selected project
    public function allow()
    {
        $values = $this->request->getValues();
        list($valid,) = $this->project->validateUserAccess($values);

        if ($valid) {

            if ($this->project->allowUser($values['project_id'], $values['user_id'])) {
                $this->session->flash(t('Project updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->redirect('?controller=project&action=users&project_id='.$values['project_id']);
    }

    // Revoke user access
    public function revoke()
    {
        $values = array(
            'project_id' => $this->request->getIntegerParam('project_id'),
            'user_id' => $this->request->getIntegerParam('user_id'),
        );

        list($valid,) = $this->project->validateUserAccess($values);

        if ($valid) {

            if ($this->project->revokeUser($values['project_id'], $values['user_id'])) {
                $this->session->flash(t('Project updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->redirect('?controller=project&action=users&project_id='.$values['project_id']);
    }
}
