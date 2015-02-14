<?php

namespace Controller;

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

        if (! $link) {
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

        if (empty($values)) {
            $values = array(
                'task_id' => $task['id'],
            );
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

        list($valid, $errors) = $this->taskLink->validateCreation($values);

        if ($valid) {

            if ($this->taskLink->create($values['task_id'], $values['opposite_task_id'], $values['link_id'])) {
                $this->session->flash(t('Link added successfully.'));
                $this->response->redirect($this->helper->url('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])).'#links');
            }
            else {
                $this->session->flashError(t('Unable to create your link.'));
            }
        }

        $this->create($values, $errors);
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
        }
        else {
            $this->session->flashError(t('Unable to remove this link.'));
        }

        $this->response->redirect($this->helper->url('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
    }
}
