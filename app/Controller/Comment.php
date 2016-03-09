<?php

namespace Kanboard\Controller;

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

        if (empty($comment)) {
            return $this->notfound();
        }

        if (! $this->userSession->isAdmin() && $comment['user_id'] != $this->userSession->getId()) {
            return $this->forbidden();
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
                'user_id' => $this->userSession->getId(),
                'task_id' => $task['id'],
            );
        }

        $this->response->html($this->template->render('comment/create', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
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

        list($valid, $errors) = $this->commentValidator->validateCreation($values);

        if ($valid) {
            if ($this->comment->create($values)) {
                $this->flash->success(t('Comment added successfully.'));
            } else {
                $this->flash->failure(t('Unable to create your comment.'));
            }

            return $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), 'comments'), true);
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

        $this->response->html($this->template->render('comment/edit', array(
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
        $this->getComment();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->commentValidator->validateModification($values);

        if ($valid) {
            if ($this->comment->update($values)) {
                $this->flash->success(t('Comment updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update your comment.'));
            }

            return $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])), false);
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

        $this->response->html($this->template->render('comment/remove', array(
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
            $this->flash->success(t('Comment removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this comment.'));
        }

        $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), 'comments'));
    }

    /**
     * Toggle comment sorting
     *
     * @access public
     */
    public function toggleSorting()
    {
        $task = $this->getTask();

        $order = $this->userSession->getCommentSorting() === 'ASC' ? 'DESC' : 'ASC';
        $this->userSession->setCommentSorting($order);

        $this->response->redirect($this->helper->url->href('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'comments'));
    }
}
