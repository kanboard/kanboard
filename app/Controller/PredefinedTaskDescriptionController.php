<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\PageNotFoundException;

/**
 * Predefined Task Description Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class PredefinedTaskDescriptionController extends BaseController
{
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('predefined_task_description/create', array(
            'values' => $values,
            'errors' => $errors,
            'project' => $project,
        )));
    }

    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->predefinedTaskDescriptionValidator->validate($values);

        if ($valid) {
            if ($this->predefinedTaskDescriptionModel->create($project['id'], $values['title'], $values['description']) !== false) {
                $this->flash->success(t('Template created successfully.'));
            } else {
                $this->flash->failure(t('Unable to create this template.'));
            }

            $this->response->redirect($this->helper->url->to('ProjectPredefinedContentController', 'show', array('project_id' => $project['id'])), true);
        } else {
            $this->create($values, $errors);
        }
    }

    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $template = $this->predefinedTaskDescriptionModel->getById($project['id'], $this->request->getIntegerParam('id'));

        $this->response->html($this->template->render('predefined_task_description/edit', array(
            'values' => empty($values) ? $template : $values,
            'template' => $template,
            'errors' => $errors,
            'project' => $project,
        )));
    }

    public function update()
    {
        $project = $this->getProject();
        $template = $this->getTemplate($project);
        $values = $this->request->getValues();

        list($valid, $errors) = $this->predefinedTaskDescriptionValidator->validate($values);

        if ($valid) {
            if ($this->predefinedTaskDescriptionModel->update($project['id'], $template['id'], $values['title'], $values['description']) !== false) {
                $this->flash->success(t('Template updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update this template.'));
            }

            $this->response->redirect($this->helper->url->to('ProjectPredefinedContentController', 'show', array('project_id' => $project['id'])), true);
        } else {
            $this->edit($values, $errors);
        }
    }

    public function confirm()
    {
        $project = $this->getProject();
        $template = $this->getTemplate($project);

        $this->response->html($this->template->render('predefined_task_description/remove', array(
            'template' => $template,
            'project' => $project,
        )));
    }

    public function remove()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $template = $this->getTemplate($project);

        if ($this->predefinedTaskDescriptionModel->remove($project['id'], $template['id'])) {
            $this->flash->success(t('Template removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this template.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectPredefinedContentController', 'show', array('project_id' => $project['id'])), true);
    }

    protected function getTemplate(array $project)
    {
        $template = $this->predefinedTaskDescriptionModel->getById($project['id'], $this->request->getIntegerParam('id'));

        if (empty($template)) {
            throw new PageNotFoundException();
        }

        return $template;
    }
}