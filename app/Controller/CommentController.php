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
     * Render the refreshed comment list as HTML and send it as the response.
     *
     * Used after any comment mutation (save / update / remove) when the request
     * comes from the modal AJAX system. Returning plain HTML (instead of a
     * redirect) tells modal.js to call replace(html), keeping the modal open
     * and showing the user the up-to-date comment list immediately.
     *
     * WHY $notificationType / $notificationMessage INSTEAD OF $this->flash:
     *
     * $this->flash->success() stores the message in the PHP session. The flash
     * message is only consumed and rendered by $this->app->flashMessage() inside
     * the full page layout (layout.php). Because AJAX responses do not go through
     * the full layout, the message stays in the session untouched and does not
     * appear until the next full page load — which is the bug the user observed.
     *
     * By passing the message directly into the template we bypass the session
     * entirely. The template renders it as an inline self-dismissing notification
     * inside the modal. The session stays clean so no stale message ever leaks
     * onto a later page load.
     *
     * $notificationType: 'success' | 'error' | '' (empty = no notification)
     * $notificationMessage: translated string or ''
     *
     * @access private
     * @param  array  $task
     * @param  string $notificationType
     * @param  string $notificationMessage
     */
    private function renderCommentList(array $task, $notificationType = '', $notificationMessage = '')
    {
        $commentSortingDirection = $this->userMetadataCacheDecorator->get(
            UserMetadataModel::KEY_COMMENT_SORTING_DIRECTION,
            'ASC'
        );

        $this->response->html($this->template->render('comment_list/show', array(
            'task'                 => $task,
            'comments'             => $this->commentModel->getAll($task['id'], $commentSortingDirection),
            'editable'             => $this->helper->user->hasProjectAccess(
                'CommentController',
                'edit',
                $task['project_id']
            ),
            'notification_type'    => $notificationType,
            'notification_message' => $notificationMessage,
        )));
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
        $task = $this->getTask();
        $values['project_id'] = $task['project_id'];

        $this->response->html($this->helper->layout->task('comment/create', array(
            'values' => $values,
            'errors' => $errors,
            'task'   => $task,
        )));
    }

    /**
     * Add a comment.
     *
     * AJAX path: returns the refreshed comment list with an inline notification
     * so the modal stays open and the user sees the result immediately.
     * The flash session is NOT touched in this path (see renderCommentList()).
     *
     * Non-AJAX path: original redirect + flash behaviour is fully preserved.
     *
     * @access public
     */
    public function save()
    {
        $task   = $this->getTask();
        $values = $this->request->getValues();
        $values['task_id'] = $task['id'];
        $values['user_id'] = $this->userSession->getId();

        list($valid, $errors) = $this->commentValidator->validateCreation($values);

        if ($valid) {
            $created = $this->commentModel->create($values) !== false;

            if ($this->request->isAjax()) {
                // Do NOT call $this->flash here — see renderCommentList() docblock.
                $this->renderCommentList(
                    $task,
                    $created ? 'success' : 'error',
                    $created ? t('Comment added successfully.') : t('Unable to create your comment.')
                );
            } else {
                if ($created) {
                    $this->flash->success(t('Comment added successfully.'));
                } else {
                    $this->flash->failure(t('Unable to create your comment.'));
                }
                $this->response->redirect($this->helper->url->to(
                    'TaskViewController',
                    'show',
                    array('task_id' => $task['id']),
                    'comments'
                ), true);
            }
        } else {
            $this->create($values, $errors);
        }
    }

    /**
     * Edit a comment form
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws AccessForbiddenException
     * @throws PageNotFoundException
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $task    = $this->getTask();
        $comment = $this->getComment($task);

        if (empty($values)) {
            $values = $comment;
        }

        $values['project_id'] = $task['project_id'];

        $this->response->html($this->template->render('comment/edit', array(
            'values'  => $values,
            'errors'  => $errors,
            'comment' => $comment,
            'task'    => $task,
        )));
    }

    /**
     * Update and validate a comment.
     *
     * Same AJAX / non-AJAX split as save().
     *
     * @access public
     */
    public function update()
    {
        $task    = $this->getTask();
        $comment = $this->getComment($task);

        $values              = $this->request->getValues();
        $values['id']        = $comment['id'];
        $values['task_id']   = $task['id'];
        $values['user_id']   = $comment['user_id'];

        list($valid, $errors) = $this->commentValidator->validateModification($values);

        if ($valid) {
            $updated = $this->commentModel->update($values);

            if ($this->request->isAjax()) {
                $this->renderCommentList(
                    $task,
                    $updated ? 'success' : 'error',
                    $updated ? t('Comment updated successfully.') : t('Unable to update your comment.')
                );
            } else {
                if ($updated) {
                    $this->flash->success(t('Comment updated successfully.'));
                } else {
                    $this->flash->failure(t('Unable to update your comment.'));
                }
                $this->response->redirect($this->helper->url->to(
                    'TaskViewController',
                    'show',
                    array('task_id' => $task['id'])
                ), true);
            }
            return;
        }

        $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a comment.
     *
     * @access public
     */
    public function confirm()
    {
        $task    = $this->getTask();
        $comment = $this->getComment($task);

        $this->response->html($this->template->render('comment/remove', array(
            'comment' => $comment,
            'task'    => $task,
            'title'   => t('Remove a comment'),
        )));
    }

    /**
     * Remove a comment.
     *
     * CSRF: uses checkReusableCSRFParam() because the confirmation form submits
     * via POST (XHR). getStringParam() — used by checkCSRFParam() — reads only
     * the URL query string and never sees POST body values. getRawValue() — used
     * by checkReusableCSRFParam() — reads the full request body correctly.
     *
     * Same AJAX / non-AJAX split as save().
     *
     * @access public
     */
    public function remove()
    {
        $this->checkReusableCSRFParam();
        $task    = $this->getTask();
        $comment = $this->getComment($task);

        $removed = $this->commentModel->remove($comment['id']);

        if ($this->request->isAjax()) {
            $this->renderCommentList(
                $task,
                $removed ? 'success' : 'error',
                $removed ? t('Comment removed successfully.') : t('Unable to remove this comment.')
            );
        } else {
            if ($removed) {
                $this->flash->success(t('Comment removed successfully.'));
            } else {
                $this->flash->failure(t('Unable to remove this comment.'));
            }
            $this->response->redirect($this->helper->url->to(
                'TaskViewController',
                'show',
                array('task_id' => $task['id']),
                'comments'
            ), true);
        }
    }

    /**
     * Toggle comment sorting (task-view page only).
     *
     * The modal variant is handled by CommentListController::toggleSorting.
     *
     * @access public
     */
    public function toggleSorting()
    {
        $this->checkReusableGETCSRFParam();
        $task = $this->getTask();
        $this->helper->comment->toggleSorting();

        $this->response->redirect($this->helper->url->to(
            'TaskViewController',
            'show',
            array('task_id' => $task['id']),
            'comments'
        ));
    }
}