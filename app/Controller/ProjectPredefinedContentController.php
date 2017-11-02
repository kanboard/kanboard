<?php

namespace Kanboard\Controller;

/**
 * Project Predefined Content Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class ProjectPredefinedContentController extends BaseController
{
    public function show(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_predefined_content/show', array(
            'values' => empty($values) ? $project : $values,
            'errors' => $errors,
            'project' => $project,
            'predefined_task_descriptions' => $this->predefinedTaskDescriptionModel->getAll($project['id']),
            'title' => t('Predefined Contents'),
        )));
    }

    public function update()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        $values = array(
            'id' => $project['id'],
            'name' => $project['name'],
            'predefined_email_subjects' => isset($values['predefined_email_subjects']) ? $values['predefined_email_subjects'] : '',
        );

        list($valid, $errors) = $this->projectValidator->validateModification($values);

        if ($valid) {
            if ($this->projectModel->update($values)) {
                $this->flash->success(t('Project updated successfully.'));
                return $this->response->redirect($this->helper->url->to('ProjectPredefinedContentController', 'show', array('project_id' => $project['id'])), true);
            } else {
                $this->flash->failure(t('Unable to update this project.'));
            }
        }

        return $this->show($values, $errors);
    }
}
