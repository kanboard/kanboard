<?php

namespace Controller;

/**
 * Comment controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Comment extends Base
{
    /**
     * Get the current comment
     *
     * @access private
     * @return array
     */
    private function getComment()
    {
        $comment = $this->comment->getById($this->request->getIntegerParam('comment_id'));

        if (! $comment) {
            $this->notfound();
        }

        if (! $this->acl->isAdminUser() && $comment['user_id'] != $this->acl->getUserId()) {
            $this->response->html($this->template->layout('comment/forbidden', array(
                'title' => t('Access Forbidden')
            )));
        }

        return $comment;
    }

    /**
     * Add comment form
     *
     * @access public
     */
    public function create(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();

        if (empty($values)) {
            $values = array(
                'user_id' => $this->acl->getUserId(),
                'task_id' => $task['id'],
            );
        }

        $this->response->html($this->taskLayout('comment/create', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'title' => t('Add a comment')
        )));
    }

    /**
     * Add a comment
     *
     * @access public
     */
    public function save()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->comment->validateCreation($values);

        if ($valid) {

            if ($this->comment->create($values)) {
                $this->session->flash(t('Comment added successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to create your comment.'));
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#comments');
        }

        $this->create($values, $errors);
    }

    /**
     * Edit a comment
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();
        $comment = $this->getComment();

        $this->response->html($this->taskLayout('comment/edit', array(
            'values' => empty($values) ? $comment : $values,
            'errors' => $errors,
            'comment' => $comment,
            'task' => $task,
            'title' => t('Edit a comment')
        )));
    }

    /**
     * Update and validate a comment
     *
     * @access public
     */
    public function update()
    {
        $task = $this->getTask();
        $comment = $this->getComment();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->comment->validateModification($values);

        if ($valid) {

            if ($this->comment->update($values)) {
                $this->session->flash(t('Comment updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update your comment.'));
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#comment-'.$comment['id']);
        }

        $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a comment
     *
     * @access public
     */
    public function confirm()
    {
        $task = $this->getTask();
        $comment = $this->getComment();

        $this->response->html($this->taskLayout('comment/remove', array(
            'comment' => $comment,
            'task' => $task,
            'title' => t('Remove a comment')
        )));
    }

    /**
     * Remove a comment
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $task = $this->getTask();
        $comment = $this->getComment();

        if ($this->comment->remove($comment['id'])) {
            $this->session->flash(t('Comment removed successfully.'));
        }
        else {
            $this->session->flashError(t('Unable to remove this comment.'));
        }

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#comments');
    }
}
