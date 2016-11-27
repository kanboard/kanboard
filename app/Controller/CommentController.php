<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\PageNotFoundException;
use Kanboard\Model\UserMetadataModel;

/**
 * Comment Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class CommentController extends BaseController
{
    /**
     * Get the current comment
     *
     * @access private
     * @return array
     * @throws PageNotFoundException
     * @throws AccessForbiddenException
     */
    private function getComment()
    {
        $comment = $this->commentModel->getById($this->request->getIntegerParam('comment_id'));

        if (empty($comment)) {
            throw new PageNotFoundException();
        }

        if (! $this->userSession->isAdmin() && $comment['user_id'] != $this->userSession->getId()) {
            throw new AccessForbiddenException();
        }

        return $comment;
    }

    /**
     * Add comment form
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws AccessForbiddenException
     * @throws PageNotFoundException
     */
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $task = $this->getTask();

        if (empty($values)) {
            $values = array(
                'user_id' => $this->userSession->getId(),
                'task_id' => $task['id'],
            );
        }

        $values['project_id'] = $task['project_id'];

        $this->response->html($this->helper->layout->task('comment/create', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'project' => $project,
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
            if ($this->commentModel->create($values) !== false) {
                $this->flash->success(t('Comment added successfully.'));
            } else {
                $this->flash->failure(t('Unable to create your comment.'));
            }

            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), 'comments'), true);
        } else {
            $this->create($values, $errors);
        }
    }

    /**
     * Edit a comment
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws AccessForbiddenException
     * @throws PageNotFoundException
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();
        $comment = $this->getComment();

        if (empty($values)) {
            $values = $comment;
        }

        $values['project_id'] = $task['project_id'];

        $this->response->html($this->template->render('comment/edit', array(
            'values' => $values,
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
            if ($this->commentModel->update($values)) {
                $this->flash->success(t('Comment updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update your comment.'));
            }

            return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])), false);
        }

        return $this->edit($values, $errors);
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

        if ($this->commentModel->remove($comment['id'])) {
            $this->flash->success(t('Comment removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this comment.'));
        }

        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), 'comments'));
    }

    /**
     * Toggle comment sorting
     *
     * @access public
     */
    public function toggleSorting()
    {
        $task = $this->getTask();

        $oldDirection = $this->userMetadataCacheDecorator->get(UserMetadataModel::KEY_COMMENT_SORTING_DIRECTION, 'ASC');
        $newDirection = $oldDirection === 'ASC' ? 'DESC' : 'ASC';

        $this->userMetadataCacheDecorator->set(UserMetadataModel::KEY_COMMENT_SORTING_DIRECTION, $newDirection);

        $this->response->redirect($this->helper->url->to(
            'TaskViewController',
            'show',
            array('task_id' => $task['id'], 'project_id' => $task['project_id']),
            'comments'
        ));
    }
}
