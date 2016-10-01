<?php

namespace Kanboard\Controller;

/**
 * Project Edit Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class ProjectEditController extends BaseController
{
    /**
     * General edition (most common operations)
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $this->renderView('project_edit/general', $values, $errors);
    }

    /**
     * Change start and end dates
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function dates(array $values = array(), array $errors = array())
    {
        $this->renderView('project_edit/dates', $values, $errors);
    }

    /**
     * Change project description
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function description(array $values = array(), array $errors = array())
    {
        $this->renderView('project_edit/description', $values, $errors);
    }

    /**
     * Change task priority
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function priority(array $values = array(), array $errors = array())
    {
        $this->renderView('project_edit/task_priority', $values, $errors);
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
        $redirect = $this->request->getStringParam('redirect', 'edit');

        $values = $this->prepareValues($redirect, $project, $values);
        list($valid, $errors) = $this->projectValidator->validateModification($values);

        if ($valid) {
            if ($this->projectModel->update($values)) {
                $this->flash->success(t('Project updated successfully.'));
                return $this->response->redirect($this->helper->url->to('ProjectEditController', $redirect, array('project_id' => $project['id'])), true);
            } else {
                $this->flash->failure(t('Unable to update this project.'));
            }
        }

        return $this->$redirect($values, $errors);
    }

    /**
     * Prepare form values
     *
     * @access private
     * @param  string $redirect
     * @param  array  $project
     * @param  array  $values
     * @return array
     */
    private function prepareValues($redirect, array $project, array $values)
    {
        if ($redirect === 'edit') {
            if (isset($values['is_private'])) {
                if (! $this->helper->user->hasProjectAccess('ProjectCreationController', 'create', $project['id'])) {
                    unset($values['is_private']);
                }
            } elseif ($project['is_private'] == 1 && ! isset($values['is_private'])) {
                if ($this->helper->user->hasProjectAccess('ProjectCreationController', 'create', $project['id'])) {
                    $values += array('is_private' => 0);
                }
            }
        }

        return $values;
    }

    /**
     * Common method to render different views
     *
     * @access private
     * @param  string $template
     * @param  array  $values
     * @param  array  $errors
     */
    private function renderView($template, array $values, array $errors)
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project($template, array(
            'owners' => $this->projectUserRoleModel->getAssignableUsersList($project['id'], true),
            'values' => empty($values) ? $project : $values,
            'errors' => $errors,
            'project' => $project,
            'title' => t('Edit project')
        )));
    }
}
