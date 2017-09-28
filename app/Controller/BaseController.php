<?php

namespace Kanboard\Controller;

use Kanboard\Core\Base;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\PageNotFoundException;

/**
 * Base Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
abstract class BaseController extends Base
{
    /**
     * Check if the CSRF token from the URL is correct
     *
     * @access protected
     */
    protected function checkCSRFParam()
    {
        if (! $this->token->validateCSRFToken($this->request->getStringParam('csrf_token'))) {
            throw new AccessForbiddenException();
        }
    }

    /**
     * Check webhook token
     *
     * @access protected
     */
    protected function checkWebhookToken()
    {
        if ($this->configModel->get('webhook_token') !== $this->request->getStringParam('token')) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }
    }

    /**
     * Common method to get a task for task views
     *
     * @access protected
     * @return array
     * @throws PageNotFoundException
     * @throws AccessForbiddenException
     */
    protected function getTask()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $task = $this->taskFinderModel->getDetails($this->request->getIntegerParam('task_id'));

        if (empty($task)) {
            throw new PageNotFoundException();
        }

        if ($project_id !== 0 && $project_id != $task['project_id']) {
            throw new AccessForbiddenException();
        }

        return $task;
    }

    /**
     * Get Task or Project file
     *
     * @access protected
     * @return array
     * @throws PageNotFoundException
     * @throws AccessForbiddenException
     */
    protected function getFile()
    {
        $task_id = $this->request->getIntegerParam('task_id');
        $file_id = $this->request->getIntegerParam('file_id');
        $project_id = $this->request->getIntegerParam('project_id');
        $model = 'projectFileModel';

        if ($task_id > 0) {
            $model = 'taskFileModel';
            $task_project_id = $this->taskFinderModel->getProjectId($task_id);

            if ($project_id != $task_project_id) {
                throw new AccessForbiddenException();
            }
        }

        $file = $this->$model->getById($file_id);

        if (empty($file)) {
            throw new PageNotFoundException();
        }

        if (isset($file['task_id']) && $file['task_id'] != $task_id) {
            throw new AccessForbiddenException();
        } else if (isset($file['project_id']) && $file['project_id'] != $project_id) {
            throw new AccessForbiddenException();
        }

        $file['model'] = $model;
        return $file;
    }

    /**
     * Common method to get a project
     *
     * @access protected
     * @param  integer      $project_id    Default project id
     * @return array
     * @throws PageNotFoundException
     */
    protected function getProject($project_id = 0)
    {
        $project_id = $this->request->getIntegerParam('project_id', $project_id);
        $project = $this->projectModel->getByIdWithOwner($project_id);

        if (empty($project)) {
            throw new PageNotFoundException();
        }

        return $project;
    }

    /**
     * Common method to get the user
     *
     * @access protected
     * @return array
     * @throws PageNotFoundException
     * @throws AccessForbiddenException
     */
    protected function getUser()
    {
        $user = $this->userModel->getById($this->request->getIntegerParam('user_id', $this->userSession->getId()));

        if (empty($user)) {
            throw new PageNotFoundException();
        }

        if (! $this->userSession->isAdmin() && $this->userSession->getId() != $user['id']) {
            throw new AccessForbiddenException();
        }

        return $user;
    }

    protected function getSubtask(array $task)
    {
        $subtask = $this->subtaskModel->getById($this->request->getIntegerParam('subtask_id'));

        if (empty($subtask)) {
            throw new PageNotFoundException();
        }

        if ($subtask['task_id'] != $task['id']) {
            throw new AccessForbiddenException();
        }

        return $subtask;
    }

    protected function getComment(array $task)
    {
        $comment = $this->commentModel->getById($this->request->getIntegerParam('comment_id'));

        if (empty($comment)) {
            throw new PageNotFoundException();
        }

        if (! $this->userSession->isAdmin() && $comment['user_id'] != $this->userSession->getId()) {
            throw new AccessForbiddenException();
        }

        if ($comment['task_id'] != $task['id']) {
            throw new AccessForbiddenException();
        }

        return $comment;
    }

    protected function getExternalTaskLink(array $task)
    {
        $link = $this->taskExternalLinkModel->getById($this->request->getIntegerParam('link_id'));

        if (empty($link)) {
            throw new PageNotFoundException();
        }

        if ($link['task_id'] != $task['id']) {
            throw new AccessForbiddenException();
        }

        return $link;
    }

    protected function getInternalTaskLink(array $task)
    {
        $link = $this->taskLinkModel->getById($this->request->getIntegerParam('link_id'));

        if (empty($link)) {
            throw new PageNotFoundException();
        }

        if ($link['task_id'] != $task['id']) {
            throw new AccessForbiddenException();
        }

        return $link;
    }

    protected function getColumn(array $project)
    {
        $column = $this->columnModel->getById($this->request->getIntegerParam('column_id'));

        if (empty($column)) {
            throw new PageNotFoundException();
        }

        if ($column['project_id'] != $project['id']) {
            throw new AccessForbiddenException();
        }

        return $column;
    }

    protected function getSwimlane(array $project)
    {
        $swimlane = $this->swimlaneModel->getById($this->request->getIntegerParam('swimlane_id'));

        if (empty($swimlane)) {
            throw new PageNotFoundException();
        }

        if ($swimlane['project_id'] != $project['id']) {
            throw new AccessForbiddenException();
        }

        return $swimlane;
    }

    protected function getCategory(array $project)
    {
        $category = $this->categoryModel->getById($this->request->getIntegerParam('category_id'));

        if (empty($category)) {
            throw new PageNotFoundException();
        }

        if ($category['project_id'] != $project['id']) {
            throw new AccessForbiddenException();
        }

        return $category;
    }

    protected function getProjectTag(array $project)
    {
        $tag = $this->tagModel->getById($this->request->getIntegerParam('tag_id'));

        if (empty($tag)) {
            throw new PageNotFoundException();
        }

        if ($tag['project_id'] != $project['id']) {
            throw new AccessForbiddenException();
        }

        return $tag;
    }

    protected function getAction(array $project)
    {
        $action = $this->actionModel->getById($this->request->getIntegerParam('action_id'));

        if (empty($action)) {
            throw new PageNotFoundException();
        }

        if ($action['project_id'] != $project['id']) {
            throw new AccessForbiddenException();
        }

        return $action;
    }

    protected function getCustomFilter(array $project)
    {
        $filter = $this->customFilterModel->getById($this->request->getIntegerParam('filter_id'));

        if (empty($filter)) {
            throw new PageNotFoundException();
        }

        if ($filter['project_id'] != $project['id']) {
            throw new AccessForbiddenException();
        }

        return $filter;
    }
}
