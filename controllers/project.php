<?php

namespace Controller;

class Project extends Base
{
    // List of completed tasks for a given project
    public function tasks()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $project = $this->project->get($project_id);

        if (! $project) {
            $this->session->flashError(t('Project not found.'));
            $this->response->redirect('?controller=project');
        }

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
        $projects = $this->project->getAll(true);
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
        $this->checkPermissions();

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
        $this->checkPermissions();

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
        $this->checkPermissions();

        $project = $this->project->get($this->request->getIntegerParam('project_id'));

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
        $this->checkPermissions();

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
        $this->checkPermissions();

        $this->response->html($this->template->layout('project_remove', array(
            'project' => $this->project->get($this->request->getIntegerParam('project_id')),
            'menu' => 'projects',
            'title' => t('Remove project')
        )));
    }

    // Remove a project
    public function remove()
    {
        $this->checkPermissions();

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
        $this->checkPermissions();

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
        $this->checkPermissions();

        $project_id = $this->request->getIntegerParam('project_id');

        if ($project_id && $this->project->disable($project_id)) {
            $this->session->flash(t('Project disabled successfully.'));
        } else {
            $this->session->flashError(t('Unable to disable this project.'));
        }

        $this->response->redirect('?controller=project');
    }
}
