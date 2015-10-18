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
            $project_ids = $this->projectPermission->getMemberProjectIds($this->userSession->getId());
        }

        $nb_projects = count($project_ids);

        $paginator = $this->paginator
            ->setUrl('project', 'index')
            ->setMax(20)
            ->setOrder('name')
            ->setQuery($this->project->getQueryProjectDetails($project_ids))
            ->calculate();

        $this->response->html($this->template->layout('project/index', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
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

            $this->response->redirect($this->helper->url->to('project', 'share', array('project_id' => $project['id'])));
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
    public function integrations()
    {
        $project = $this->getProject();

        if ($this->request->isPost()) {
            $this->projectMetadata->save($project['id'], $this->request->getValues());
            $this->session->flash(t('Project updated successfully.'));
            $this->response->redirect($this->helper->url->to('project', 'integrations', array('project_id' => $project['id'])));
        }

        $this->response->html($this->projectLayout('project/integrations', array(
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
            $this->session->flash(t('Project updated successfully.'));
            $this->response->redirect($this->helper->url->to('project', 'notifications', array('project_id' => $project['id'])));
        }

        $this->response->html($this->projectLayout('project/notifications', array(
            'notifications' => $this->projectNotification->readSettings($project['id']),
            'types' => $this->projectNotificationType->getTypes(),
            'project' => $project,
            'title' => t('Notifications'),
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

        if (isset($values['is_private'])) {
            if (! $this->helper->user->isProjectAdministrationAllowed($project['id'])) {
                unset($values['is_private']);
            }
        } elseif ($project['is_private'] == 1 && ! isset($values['is_private'])) {
            if ($this->helper->user->isProjectAdministrationAllowed($project['id'])) {
                $values += array('is_private' => 0);
            }
        }

        list($valid, $errors) = $this->project->validateModification($values);

        if ($valid) {
            if ($this->project->update($values)) {
                $this->session->flash(t('Project updated successfully.'));
                $this->response->redirect($this->helper->url->to('project', 'edit', array('project_id' => $project['id'])));
            } else {
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
        list($valid, ) = $this->projectPermission->validateProjectModification($values);

        if ($valid) {
            if ($this->project->update($values)) {
                $this->session->flash(t('Project updated successfully.'));
            } else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->redirect($this->helper->url->to('project', 'users', array('project_id' => $project['id'])));
    }

    /**
     * Allow a specific user (admin only)
     *
     * @access public
     */
    public function allow()
    {
        $values = $this->request->getValues();
        list($valid, ) = $this->projectPermission->validateUserModification($values);

        if ($valid) {
            if ($this->projectPermission->addMember($values['project_id'], $values['user_id'])) {
                $this->session->flash(t('Project updated successfully.'));
            } else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->redirect($this->helper->url->to('project', 'users', array('project_id' => $values['project_id'])));
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

        list($valid, ) = $this->projectPermission->validateUserModification($values);

        if ($valid) {
            if ($this->projectPermission->changeRole($values['project_id'], $values['user_id'], $values['is_owner'])) {
                $this->session->flash(t('Project updated successfully.'));
            } else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->redirect($this->helper->url->to('project', 'users', array('project_id' => $values['project_id'])));
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

        list($valid, ) = $this->projectPermission->validateUserModification($values);

        if ($valid) {
            if ($this->projectPermission->revokeMember($values['project_id'], $values['user_id'])) {
                $this->session->flash(t('Project updated successfully.'));
            } else {
                $this->session->flashError(t('Unable to update this project.'));
            }
        }

        $this->response->redirect($this->helper->url->to('project', 'users', array('project_id' => $values['project_id'])));
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

            $this->response->redirect($this->helper->url->to('project', 'index'));
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
     * @author Michael Lüpkes
     * @access public
     */
    public function duplicate()
    {
        $project = $this->getProject();

        if ($this->request->getStringParam('duplicate') === 'yes') {
            $values = array_keys($this->request->getValues());
            if ($this->projectDuplication->duplicate($project['id'], $values) !== false) {
                $this->session->flash(t('Project cloned successfully.'));
            } else {
                $this->session->flashError(t('Unable to clone this project.'));
            }

            $this->response->redirect($this->helper->url->to('project', 'index'));
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

            $this->response->redirect($this->helper->url->to('project', 'show', array('project_id' => $project['id'])));
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

            $this->response->redirect($this->helper->url->to('project', 'show', array('project_id' => $project['id'])));
        }

        $this->response->html($this->projectLayout('project/enable', array(
            'project' => $project,
            'title' => t('Project activation')
        )));
    }

    /**
     * Display a form to create a new project
     *
     * @access public
     */
    public function create(array $values = array(), array $errors = array())
    {
        $is_private = $this->request->getIntegerParam('private', $this->userSession->isAdmin() || $this->userSession->isProjectAdmin() ? 0 : 1);

        $this->response->html($this->template->layout('project/new', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
            'values' => empty($values) ? array('is_private' => $is_private) : $values,
            'errors' => $errors,
            'is_private' => $is_private,
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

            if ($project_id > 0) {
                $this->session->flash(t('Your project have been created successfully.'));
                $this->response->redirect($this->helper->url->to('project', 'show', array('project_id' => $project_id)));
            }

            $this->session->flashError(t('Unable to create your project.'));
        }

        $this->create($values, $errors);
    }
}
