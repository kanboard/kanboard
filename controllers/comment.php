<?php

namespace Controller;

require_once __DIR__.'/base.php';

/**
 * Comment controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Comment extends Base
{
    /**
     * Forbidden page for comments
     *
     * @access public
     */
    public function forbidden()
    {
        $this->response->html($this->template->layout('comment_forbidden', array(
            'menu' => 'tasks',
            'title' => t('Access Forbidden')
        )));
    }

    /**
     * Add a comment
     *
     * @access public
     */
    public function save()
    {
        $task = $this->task->getById($this->request->getIntegerParam('task_id'), true);
        $values = $this->request->getValues();

        if (! $task) $this->notfound();
        $this->checkProjectPermissions($task['project_id']);

        list($valid, $errors) = $this->comment->validateCreation($values);

        if ($valid) {

            if ($this->comment->create($values)) {
                $this->session->flash(t('Comment added successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to create your comment.'));
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id']);
        }

        $this->showTask(
            $task,
            array('values' => $values, 'errors' => $errors)
        );
    }

    /**
     * Edit a comment
     *
     * @access public
     */
    public function edit()
    {
        $task_id = $this->request->getIntegerParam('task_id');
        $comment_id = $this->request->getIntegerParam('comment_id');

        $task = $this->task->getById($task_id, true);
        $comment = $this->comment->getById($comment_id);

        if (! $task || ! $comment) $this->notfound();
        $this->checkProjectPermissions($task['project_id']);

        if ($this->acl->isAdminUser() || $comment['user_id'] == $this->acl->getUserId()) {

            $this->showTask(
                $task,
                array(),
                array(),
                array('values' => array('id' => $comment['id']), 'errors' => array())
            );
        }

        $this->forbidden();
    }

    /**
     * Update and validate a comment
     *
     * @access public
     */
    public function update()
    {
        $task_id = $this->request->getIntegerParam('task_id');
        $comment_id = $this->request->getIntegerParam('comment_id');

        $task = $this->task->getById($task_id, true);
        $comment = $this->comment->getById($comment_id);

        $values = $this->request->getValues();

        if (! $task || ! $comment) $this->notfound();
        $this->checkProjectPermissions($task['project_id']);

        if ($this->acl->isAdminUser() || $comment['user_id'] == $this->acl->getUserId()) {

            list($valid, $errors) = $this->comment->validateModification($values);

            if ($valid) {

                if ($this->comment->update($values)) {
                    $this->session->flash(t('Comment updated successfully.'));
                }
                else {
                    $this->session->flashError(t('Unable to update your comment.'));
                }

                $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#comment-'.$comment_id);
            }

            $this->showTask(
                $task,
                array(),
                array(),
                array('values' => $values, 'errors' => $errors)
            );
        }

        $this->forbidden();
    }

    /**
     * Confirmation dialog before removing a comment
     *
     * @access public
     */
    public function confirm()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $comment_id = $this->request->getIntegerParam('comment_id');

        $this->checkProjectPermissions($project_id);

        $comment = $this->comment->getById($comment_id);
        if (! $comment) $this->notfound();

        if ($this->acl->isAdminUser() || $comment['user_id'] == $this->acl->getUserId()) {

            $this->response->html($this->template->layout('comment_remove', array(
                'comment' => $comment,
                'project_id' => $project_id,
                'menu' => 'tasks',
                'title' => t('Remove a comment')
            )));
        }

        $this->forbidden();
    }

    /**
     * Remove a comment
     *
     * @access public
     */
    public function remove()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $comment_id = $this->request->getIntegerParam('comment_id');

        $this->checkProjectPermissions($project_id);

        $comment = $this->comment->getById($comment_id);
        if (! $comment) $this->notfound();

        if ($this->acl->isAdminUser() || $comment['user_id'] == $this->acl->getUserId()) {

            if ($this->comment->remove($comment['id'])) {
                $this->session->flash(t('Comment removed successfully.'));
            } else {
                $this->session->flashError(t('Unable to remove this comment.'));
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$comment['task_id']);
        }

        $this->forbidden();
    }
}
