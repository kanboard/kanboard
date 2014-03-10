<?php

namespace Controller;

abstract class Base
{
    public function __construct(\Core\Registry $registry)
    {
        $this->acl = $registry->acl;
        $this->action = $registry->action;
        $this->board = $registry->board;
        $this->config = $registry->config;
        $this->project = $registry->project;
        $this->task = $registry->task;
        $this->user = $registry->user;
        $this->comment = $registry->comment;
    }

    public function beforeAction($controller, $action)
    {
        // Start the session
        $this->session->open(dirname($_SERVER['PHP_SELF']), SESSION_SAVE_PATH);

        // HTTP secure headers
        $this->response->csp();
        $this->response->nosniff();
        $this->response->xss();
        $this->response->hsts();
        $this->response->xframe();

        // Load translations
        $language = $this->config->get('language', 'en_US');
        if ($language !== 'en_US') \Translator\load($language);

        // Set timezone
        date_default_timezone_set($this->config->get('timezone', 'UTC'));

        // If the user is not authenticated redirect to the login form, if the action is public continue
        if (! isset($_SESSION['user']) && ! $this->acl->isPublicAction($controller, $action)) {
            $this->response->redirect('?controller=user&action=login');
        }

        // Check if the user is allowed to see this page
        if (! $this->acl->isPageAccessAllowed($controller, $action)) {
            $this->response->redirect('?controller=user&action=forbidden');
        }

        // Attach events for automatic actions
        $this->action->attachEvents();
    }

    public function checkProjectPermissions($project_id)
    {
        if ($this->acl->isRegularUser()) {

            if ($project_id > 0 && ! $this->project->isUserAllowed($project_id, $this->acl->getUserId())) {
                $this->response->redirect('?controller=project&action=forbidden');
            }
        }
    }

    public function redirectNoProject()
    {
        $this->session->flash(t('There is no active project, the first step is to create a new project.'));
        $this->response->redirect('?controller=project&action=create');
    }

    public function notfound()
    {
        $this->response->html($this->template->layout('app_notfound', array('title' => t('Page not found'))));
    }
}
