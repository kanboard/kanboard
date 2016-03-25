<?php

namespace Kanboard\Controller;

use Kanboard\Core\Security\Role;

/**
 * Base controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
abstract class Base extends \Kanboard\Core\Base
{
    /**
     * Method executed before each action
     *
     * @access public
     */
    public function beforeAction()
    {
        $this->sessionManager->open();
        $this->dispatcher->dispatch('app.bootstrap');
        $this->sendHeaders();
        $this->authenticationManager->checkCurrentSession();

        if (! $this->applicationAuthorization->isAllowed($this->router->getController(), $this->router->getAction(), Role::APP_PUBLIC)) {
            $this->handleAuthentication();
            $this->handlePostAuthentication();
            $this->checkApplicationAuthorization();
            $this->checkProjectAuthorization();
        }
    }

    /**
     * Send HTTP headers
     *
     * @access private
     */
    private function sendHeaders()
    {
        // HTTP secure headers
        $this->response->csp($this->container['cspRules']);
        $this->response->nosniff();
        $this->response->xss();

        // Allow the public board iframe inclusion
        if (ENABLE_XFRAME && $this->router->getAction() !== 'readonly') {
            $this->response->xframe();
        }

        if (ENABLE_HSTS) {
            $this->response->hsts();
        }
    }

    /**
     * Check authentication
     *
     * @access private
     */
    private function handleAuthentication()
    {
        if (! $this->userSession->isLogged() && ! $this->authenticationManager->preAuthentication()) {
            if ($this->request->isAjax()) {
                $this->response->text('Not Authorized', 401);
            }

            $this->sessionStorage->redirectAfterLogin = $this->request->getUri();
            $this->response->redirect($this->helper->url->to('auth', 'login'));
        }
    }

    /**
     * Handle Post-Authentication (2FA)
     *
     * @access private
     */
    private function handlePostAuthentication()
    {
        $controller = strtolower($this->router->getController());
        $action = strtolower($this->router->getAction());
        $ignore = ($controller === 'twofactor' && in_array($action, array('code', 'check'))) || ($controller === 'auth' && $action === 'logout');

        if ($ignore === false && $this->userSession->hasPostAuthentication() && ! $this->userSession->isPostAuthenticationValidated()) {
            if ($this->request->isAjax()) {
                $this->response->text('Not Authorized', 401);
            }

            $this->response->redirect($this->helper->url->to('twofactor', 'code'));
        }
    }

    /**
     * Check application authorization
     *
     * @access private
     */
    private function checkApplicationAuthorization()
    {
        if (! $this->helper->user->hasAccess($this->router->getController(), $this->router->getAction())) {
            $this->forbidden();
        }
    }

    /**
     * Check project authorization
     *
     * @access private
     */
    private function checkProjectAuthorization()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $task_id = $this->request->getIntegerParam('task_id');

        // Allow urls without "project_id"
        if ($task_id > 0 && $project_id === 0) {
            $project_id = $this->taskFinder->getProjectId($task_id);
        }

        if ($project_id > 0 && ! $this->helper->user->hasProjectAccess($this->router->getController(), $this->router->getAction(), $project_id)) {
            $this->forbidden();
        }
    }

    /**
     * Application not found page (404 error)
     *
     * @access protected
     * @param  boolean   $no_layout   Display the layout or not
     */
    protected function notfound($no_layout = false)
    {
        $this->response->html($this->helper->layout->app('app/notfound', array(
            'title' => t('Page not found'),
            'no_layout' => $no_layout,
        )));
    }

    /**
     * Application forbidden page
     *
     * @access protected
     * @param  boolean   $no_layout   Display the layout or not
     */
    protected function forbidden($no_layout = false)
    {
        if ($this->request->isAjax()) {
            $this->response->text('Access Forbidden', 403);
        }

        $this->response->html($this->helper->layout->app('app/forbidden', array(
            'title' => t('Access Forbidden'),
            'no_layout' => $no_layout,
        )));
    }

    /**
     * Check if the CSRF token from the URL is correct
     *
     * @access protected
     */
    protected function checkCSRFParam()
    {
        if (! $this->token->validateCSRFToken($this->request->getStringParam('csrf_token'))) {
            $this->forbidden();
        }
    }

    /**
     * Check webhook token
     *
     * @access protected
     */
    protected function checkWebhookToken()
    {
        if ($this->config->get('webhook_token') !== $this->request->getStringParam('token')) {
            $this->response->text('Not Authorized', 401);
        }
    }

    /**
     * Common method to get a task for task views
     *
     * @access protected
     * @return array
     */
    protected function getTask()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $task = $this->taskFinder->getDetails($this->request->getIntegerParam('task_id'));

        if (empty($task)) {
            $this->notfound();
        }

        if ($project_id !== 0 && $project_id != $task['project_id']) {
            $this->forbidden();
        }

        return $task;
    }

    /**
     * Get Task or Project file
     *
     * @access protected
     */
    protected function getFile()
    {
        $task_id = $this->request->getIntegerParam('task_id');
        $file_id = $this->request->getIntegerParam('file_id');
        $model = 'projectFile';

        if ($task_id > 0) {
            $model = 'taskFile';
            $project_id = $this->taskFinder->getProjectId($task_id);

            if ($project_id !== $this->request->getIntegerParam('project_id')) {
                $this->forbidden();
            }
        }

        $file = $this->$model->getById($file_id);

        if (empty($file)) {
            $this->notfound();
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
     */
    protected function getProject($project_id = 0)
    {
        $project_id = $this->request->getIntegerParam('project_id', $project_id);
        $project = $this->project->getByIdWithOwner($project_id);

        if (empty($project)) {
            $this->notfound();
        }

        return $project;
    }

    /**
     * Common method to get the user
     *
     * @access protected
     * @return array
     */
    protected function getUser()
    {
        $user = $this->user->getById($this->request->getIntegerParam('user_id', $this->userSession->getId()));

        if (empty($user)) {
            $this->notfound();
        }

        if (! $this->userSession->isAdmin() && $this->userSession->getId() != $user['id']) {
            $this->forbidden();
        }

        return $user;
    }

    /**
     * Get the current subtask
     *
     * @access protected
     * @return array
     */
    protected function getSubtask()
    {
        $subtask = $this->subtask->getById($this->request->getIntegerParam('subtask_id'));

        if (empty($subtask)) {
            $this->notfound();
        }

        return $subtask;
    }
}
