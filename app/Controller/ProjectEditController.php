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
     * Edit project
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function show(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_edit/show', array(
            'owners' => $this->projectUserRoleModel->getAssignableUsersList($project['id'], true),
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

        $values = $this->prepareValues($project, $values);
        list($valid, $errors) = $this->projectValidator->validateModification($values);

        if ($valid) {
            if ($this->projectModel->update($values)) {
                $this->flash->success(t('Project updated successfully.'));
                return $this->response->redirect($this->helper->url->to('ProjectEditController', 'show', array('project_id' => $project['id'])), true);
            } else {
                $this->flash->failure(t('Unable to update this project.'));
            }
        }

        return $this->show($values, $errors);
    }

    /**
     * Prepare form values
     *
     * @access private
     * @param  array  $project
     * @param  array  $values
     * @return array
     */
    private function prepareValues(array $project, array $values)
    {
        $values['id'] = $project['id'];

        if (isset($values['is_private'])) {
            if (! $this->helper->user->hasProjectAccess('ProjectCreationController', 'create', $project['id'])) {
                unset($values['is_private']);
            }
        } elseif ($project['is_private'] == 1 && ! isset($values['is_private'])) {
            if ($this->helper->user->hasProjectAccess('ProjectCreationController', 'create', $project['id'])) {
                $values += array('is_private' => 0);
            }
        }

        return $values;
    }
}
