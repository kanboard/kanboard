<?php

namespace Controller;

/**
 * Link controller
 *
 * @package  controller
 * @author   Olivier Maridat
 */
class Link extends Base
{
    /**
     * Get the current link
     *
     * @access private
     * @return array
     */
    private function getLink()
    {
        $link = $this->link->getById($this->request->getIntegerParam('link_id'));

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
                'another_link' => $this->request->getIntegerParam('another_link', 0)
            );
        }

        $this->response->html($this->taskLayout('link/create', array(
            'values' => $values,
            'errors' => $errors,
            'link_list' => $this->link->getList($task['project_id'], false),
            'task' => $task,
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

        list($valid, $errors) = $this->link->validateCreation($values);

        if ($valid) {

            if ($this->link->create($values)) {
                $this->session->flash(t('Link added successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to create your link.'));
            }

            if (isset($values['another_link']) && $values['another_link'] == 1) {
                $this->response->redirect('?controller=link&action=create&task_id='.$task['id'].'&another_link=1');
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#links');
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
        $link = $this->getLink();

        $this->response->html($this->taskLayout('link/edit', array(
            'values' => empty($values) ? $link : $values,
            'errors' => $errors,
            'link_list' => $this->link->getList($task['project_id'], false),
            'link' => $link,
            'task' => $task,
        )));
    }

    /**
     * Update and validate a link
     *
     * @access public
     */
    public function update()
    {
        $task = $this->getTask();
        $link = $this->getLink();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->link->validateModification($values);

        if ($valid) {

            if ($this->link->update($values)) {
                $this->session->flash(t('Link updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update your link.'));
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#links');
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
        $link = $this->getLink();

        $this->response->html($this->taskLayout('link/remove', array(
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
        	$this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#links');
        }
        else {
            $this->session->flashError(t('Unable to remove this link.'));
	        $this->response->redirect('?controller=tasklink&action=confirm&task_id='.$task['id'].'&link_id='.$this->request->getIntegerParam('link_id'));
        }
    }
}
