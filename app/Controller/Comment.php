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
            $this->response->html($this->template->layout('comment_forbidden', array(
                'menu' => 'tasks',
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
    public function create()
    {
        $task = $this->getTask();

        $this->response->html($this->taskLayout('comment_create', array(
            'values' => array(
                'user_id' => $this->acl->getUserId(),
                'task_id' => $task['id'],
            ),
            'errors' => array(),
            'task' => $task,
            'menu' => 'tasks',
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

        $this->response->html($this->taskLayout('comment_create', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Add a comment')
        )));
    }

    /**
     * Edit a comment
     *
     * @access public
     */
    public function edit()
    {
        $task = $this->getTask();
        $comment = $this->getComment();

        $this->response->html($this->taskLayout('comment_edit', array(
            'values' => $comment,
            'errors' => array(),
            'comment' => $comment,
            'task' => $task,
            'menu' => 'tasks',
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

        $this->response->html($this->taskLayout('comment_edit', array(
            'values' => $values,
            'errors' => $errors,
            'comment' => $comment,
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Edit a comment')
        )));
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

        $this->response->html($this->taskLayout('comment_remove', array(
            'comment' => $comment,
            'task' => $task,
            'menu' => 'tasks',
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
