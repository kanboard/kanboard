<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\PageNotFoundException;

/**
 * TaskInternalLink Controller
 *
 * @package  Kanboard\Controller
 * @author   Olivier Maridat
 * @author   Frederic Guillot
 */
class TaskInternalLinkController extends BaseController
{
    /**
     * Get the current link
     *
     * @access private
     * @return array
     * @throws PageNotFoundException
     */
    private function getTaskLink()
    {
        $link = $this->taskLinkModel->getById($this->request->getIntegerParam('link_id'));

        if (empty($link)) {
            throw new PageNotFoundException();
        }

        return $link;
    }

    /**
     * Creation form
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws PageNotFoundException
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     */
    public function create(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('task_internal_link/create', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'labels' => $this->linkModel->getList(0, false),
        )));
    }

    /**
     * Validation and creation
     *
     * @access public
     */
    public function save()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->taskLinkValidator->validateCreation($values);

        if ($valid) {
            if ($this->taskLinkModel->create($values['task_id'], $values['opposite_task_id'], $values['link_id'])) {
                $this->flash->success(t('Link added successfully.'));
                return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])), true);
            }

            $errors = array('title' => array(t('The exact same link already exists')));
            $this->flash->failure(t('Unable to create your link.'));
        }

        return $this->create($values, $errors);
    }

    /**
     * Edit form
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws PageNotFoundException
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();
        $task_link = $this->getTaskLink();

        if (empty($values)) {
            $opposite_task = $this->taskFinderModel->getById($task_link['opposite_task_id']);
            $values = $task_link;
            $values['title'] = '#'.$opposite_task['id'].' - '.$opposite_task['title'];
        }

        $this->response->html($this->template->render('task_internal_link/edit', array(
            'values' => $values,
            'errors' => $errors,
            'task_link' => $task_link,
            'task' => $task,
            'labels' => $this->linkModel->getList(0, false)
        )));
    }

    /**
     * Validation and update
     *
     * @access public
     */
    public function update()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->taskLinkValidator->validateModification($values);

        if ($valid) {
            if ($this->taskLinkModel->update($values['id'], $values['task_id'], $values['opposite_task_id'], $values['link_id'])) {
                $this->flash->success(t('Link updated successfully.'));
                return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).'#links');
            }

            $this->flash->failure(t('Unable to update your link.'));
        }

        return $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a link
     *
     * @access public
     */
    public function confirm()
    {
        $task = $this->getTask();
        $link = $this->getTaskLink();

        $this->response->html($this->template->render('task_internal_link/remove', array(
            'link' => $link,
            'task' => $task,
        )));
    }

    /**
     * Remove a link
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $task = $this->getTask();

        if ($this->taskLinkModel->remove($this->request->getIntegerParam('link_id'))) {
            $this->flash->success(t('Link removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this link.'));
        }

        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
    }
}
