<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Project Creation Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class ProjectCreationController extends BaseController
{
    /**
     * Display a form to create a new project
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function create(array $values = array(), array $errors = array())
    {
        $is_private = isset($values['is_private']) && $values['is_private'] == 1;
        $projects_list = array(0 => t('Do not duplicate anything')) + $this->projectUserRoleModel->getActiveProjectsByUser($this->userSession->getId());

        $this->response->html($this->helper->layout->app('project_creation/create', array(
            'values' => $values,
            'errors' => $errors,
            'is_private' => $is_private,
            'projects_list' => $projects_list,
            'title' => $is_private ? t('New personal project') : t('New project'),
        )));
    }

    /**
     * Display a form to create a private project
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function createPrivate(array $values = array(), array $errors = array())
    {
        $this->checkPrivateProjectCreationAllowed();

        $values['is_private'] = 1;
        $this->create($values, $errors);
    }

    /**
     * Validate and save a new project
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        $values['is_private'] = empty($values['is_private']) ? 0 : 1;

        if (! empty($values['src_project_id'])) {
            $this->checkSourceProjectAccessAllowed($values['src_project_id']);
        }

        if ($this->isDestinationProjectPrivate($values)) {
            $this->checkPrivateProjectCreationAllowed();
        } else {
            $this->checkTeamProjectCreationAllowed();
        }

        list($valid, $errors) = $this->projectValidator->validateCreation($values);

        if ($valid) {
            $project_id = $this->createOrDuplicate($values);

            if ($project_id > 0) {
                $this->flash->success(t('Your project has been created successfully.'));
                return $this->response->redirect($this->helper->url->to('ProjectViewController', 'show', array('project_id' => $project_id)));
            }

            $this->flash->failure(t('Unable to create your project.'));
        }

        return $this->create($values, $errors);
    }

    /**
     * Get the visibility of the project that is going to be created
     *
     * Duplicated projects inherit the visibility of the source project when
     * the form does not ask for a personal project.
     *
     * @access private
     * @param  array $values
     * @return boolean
     */
    private function isDestinationProjectPrivate(array $values)
    {
        if (! empty($values['is_private'])) {
            return true;
        }

        if (! empty($values['src_project_id'])) {
            return $this->projectModel->isPrivate($values['src_project_id']);
        }

        return false;
    }

    /**
     * Check that the user has access to the project used as a template
     *
     * @access private
     * @param  integer $projectId
     * @throws AccessForbiddenException
     */
    private function checkSourceProjectAccessAllowed($projectId)
    {
        if (! $this->projectPermissionModel->isUserAllowed($projectId, $this->userSession->getId())) {
            throw new AccessForbiddenException();
        }
    }

    /**
     * Check that the user is allowed to create team projects
     *
     * @access private
     * @throws AccessForbiddenException
     */
    private function checkTeamProjectCreationAllowed()
    {
        if (! $this->helper->user->hasAccess('ProjectCreationController', 'create')) {
            throw new AccessForbiddenException();
        }
    }

    /**
     * Check that personal projects are enabled
     *
     * @access private
     * @throws AccessForbiddenException
     */
    private function checkPrivateProjectCreationAllowed()
    {
        if ($this->configModel->get('disable_private_project', 0) == 1) {
            throw new AccessForbiddenException();
        }
    }

    /**
     * Create or duplicate a project
     *
     * @access private
     * @param  array  $values
     * @return boolean|integer
     */
    private function createOrDuplicate(array $values)
    {
        if (empty($values['src_project_id'])) {
            return $this->createNewProject($values);
        }

        return $this->duplicateNewProject($values);
    }

    /**
     * Save a new project
     *
     * @access private
     * @param  array  $values
     * @return boolean|integer
     */
    private function createNewProject(array $values)
    {
        $project = array(
            'name' => $values['name'],
            'is_private' => $values['is_private'],
            'identifier' => $values['identifier'],
            'per_swimlane_task_limits' => array_key_exists('per_swimlane_task_limits', $values) ? $values['per_swimlane_task_limits'] : 0,
            'task_limit' => $values['task_limit'],
        );

        return $this->projectModel->create($project, $this->userSession->getId(), true);
    }

    /**
     * Create from another project
     *
     * @access private
     * @param  array  $values
     * @return boolean|integer
     */
    private function duplicateNewProject(array $values)
    {
        $selection = array();

        foreach ($this->projectDuplicationModel->getOptionalSelection() as $item) {
            if (isset($values[$item]) && $values[$item] == 1) {
                $selection[] = $item;
            }
        }

        return $this->projectDuplicationModel->duplicate(
            $values['src_project_id'],
            $selection,
            $this->userSession->getId(),
            $values['name'],
            $values['is_private'] == 1,
            $values['identifier']
        );
    }
}
