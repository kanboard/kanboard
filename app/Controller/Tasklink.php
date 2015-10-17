<?php

namespace Kanboard\Controller;

/**
 * TaskLink controller
 *
 * @package  controller
 * @author   Olivier Maridat
 * @author   Frederic Guillot
 */
class Tasklink extends Base
{
    /**
     * Get the current link
     *
     * @access private
     * @return array
     */
    private function getTaskLink()
    {
        $link = $this->taskLink->getById($this->request->getIntegerParam('link_id'));

        if (empty($link)) {
            $this->notfound();
        }

        return $link;
    }

    /**
     * Creation form
     *
     * @access public
     */
    public function create(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();
        $ajax = $this->request->isAjax() || $this->request->getIntegerParam('ajax');

        if ($ajax && empty($errors)) {
            $this->response->html($this->template->render('tasklink/create', array(
                'values' => $values,
                'errors' => $errors,
                'task' => $task,
                'labels' => $this->link->getList(0, false),
                'title' => t('Add a new link'),
                'ajax' => $ajax,
            )));
        }

        $this->response->html($this->taskLayout('tasklink/create', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'labels' => $this->link->getList(0, false),
            'title' => t('Add a new link')
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
        $ajax = $this->request->isAjax() || $this->request->getIntegerParam('ajax');

        list($valid, $errors) = $this->taskLink->validateCreation($values);

        if ($valid) {
            if ($this->taskLink->create($values['task_id'], $values['opposite_task_id'], $values['link_id'])) {
                $this->session->flash(t('Link added successfully.'));

                if ($ajax) {
                    $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $task['project_id'])));
                }

                $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).'#links');
            }

            $errors = array('title' => array(t('The exact same link already exists')));
            $this->session->flashError(t('Unable to create your link.'));
        }

        $this->create($values, $errors);
    }

    /**
     * Edit form
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();
        $task_link = $this->getTaskLink();

        if (empty($values)) {
            $opposite_task = $this->taskFinder->getById($task_link['opposite_task_id']);
            $values = $task_link;
            $values['title'] = '#'.$opposite_task['id'].' - '.$opposite_task['title'];
        }

        $this->response->html($this->taskLayout('tasklink/edit', array(
            'values' => $values,
            'errors' => $errors,
            'task_link' => $task_link,
            'task' => $task,
            'labels' => $this->link->getList(0, false),
            'title' => t('Edit link')
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

        list($valid, $errors) = $this->taskLink->validateModification($values);

        if ($valid) {
            if ($this->taskLink->update($values['id'], $values['task_id'], $values['opposite_task_id'], $values['link_id'])) {
                $this->session->flash(t('Link updated successfully.'));
                $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).'#links');
            }

            $this->session->flashError(t('Unable to update your link.'));
        }

        $this->edit($values, $errors);
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

        $this->response->html($this->taskLayout('tasklink/remove', array(
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

        if ($this->taskLink->remove($this->request->getIntegerParam('link_id'))) {
            $this->session->flash(t('Link removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this link.'));
        }

        $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).'#links');
    }
}
