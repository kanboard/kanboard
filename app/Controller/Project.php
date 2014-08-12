<?php

namespace Controller;

use Model\Task as TaskModel;
use Core\Translator;

/**
 * Project controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Project extends Base
{

	/**
	 * Clone Project
	 *
	 * @author Antonio Rabelo
	 * @access public
	 */
	public function duplicate()
	{
		$this->checkCSRFParam();
		$project_id = $this->request->getIntegerParam('project_id');

		if ($project_id && $this->project->duplicate($project_id)) {
			$this->session->flash(t('Project cloned successfully.'));
		} else {
			$this->session->flashError(t('Unable to clone this project.'));
		}

		$this->response->redirect('?controller=project');
	}

    /**
     * Task export
     *
     * @access public
     */
    public function export()
    {
        $project = $this->getProject();
        $from = $this->request->getStringParam('from');
        $to = $this->request->getStringParam('to');

        if ($from && $to) {
            Translator::disableEscaping();
            $data = $this->task->export($project['id'], $from, $to);
            $this->response->forceDownload('Export_'.date('Y_m_d_H_i_S').'.csv');
            $this->response->csv($data);
        }

        $this->response->html($this->template->layout('project_export', array(
            'values' => array(
                'controller' => 'project',
                'action' => 'export',
                'project_id' => $project['id'],
                'from' => $from,
                'to' => $to,
            ),
            'errors' => array(),
            'menu' => 'projects',
            'project' => $project,
            'title' => t('Tasks Export')
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
            'menu' => 'projects',
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
            'menu' => 'projects',
            'project' => $project,
            'columns' => $this->board->getColumnsList($project['id']),
            'categories' => $this->category->getList($project['id'], false),
            'tasks' => $tasks,
            'nb_tasks' => $nb_tasks,
            'title' => $project['name'].' ('.$nb_tasks.')'
        )));
    }

    /**
     * List of projects
     *
     * @access public
     */
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

    /**
     * Display a form to create a new project
     *
     * @access public
     */
    public function create()
    {
        $this->response->html($this->template->layout('project_new', array(
            'errors' => array(),
            'values' => array(),
            'menu' => 'projects',
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

    /**
     * Display a form to edit a project
     *
     * @access public
     */
    public function edit()
    {
        $project = $this->getProject();

        $this->response->html($this->template->layout('project_edit', array(
            'errors' => array(),
            'values' => $project,
            'menu' => 'projects',
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

    /**
     * Confirmation dialog before to remove a project
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();

        $this->response->html($this->template->layout('project_remove', array(
            'project' => $project,
            'menu' => 'projects',
            'title' => t('Remove project')
        )));
    }

    /**
     * Remove a project
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $project_id = $this->request->getIntegerParam('project_id');

        if ($project_id && $this->project->remove($project_id)) {
            $this->session->flash(t('Project removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this project.'));
        }

        $this->response->redirect('?controller=project');
    }

    /**
     * Enable a project
     *
     * @access public
     */
    public function enable()
    {
        $this->checkCSRFParam();
        $project_id = $this->request->getIntegerParam('project_id');

        if ($project_id && $this->project->enable($project_id)) {
            $this->session->flash(t('Project activated successfully.'));
        } else {
            $this->session->flashError(t('Unable to activate this project.'));
        }

        $this->response->redirect('?controller=project');
    }

    /**
     * Disable a project
     *
     * @access public
     */
    public function disable()
    {
        $this->checkCSRFParam();
        $project_id = $this->request->getIntegerParam('project_id');

        if ($project_id && $this->project->disable($project_id)) {
            $this->session->flash(t('Project disabled successfully.'));
        } else {
            $this->session->flashError(t('Unable to disable this project.'));
        }

        $this->response->redirect('?controller=project');
    }

    /**
     * Users list for the selected project
     *
     * @access public
     */
    public function users()
    {
        $project = $this->getProject();

        $this->response->html($this->template->layout('project_users', array(
            'project' => $project,
            'users' => $this->project->getAllUsers($project['id']),
            'menu' => 'projects',
            'title' => t('Edit project access list')
        )));
    }

    /**
     * Allow a specific user for the selected project
     *
     * @access public
     */
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

    /**
     * Revoke user access
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
