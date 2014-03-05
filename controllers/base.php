<?php

namespace Controller;

require __DIR__.'/../lib/request.php';
require __DIR__.'/../lib/response.php';
require __DIR__.'/../lib/session.php';
require __DIR__.'/../lib/template.php';
require __DIR__.'/../lib/helper.php';
require __DIR__.'/../lib/translator.php';
require __DIR__.'/../models/base.php';
require __DIR__.'/../models/acl.php';
require __DIR__.'/../models/config.php';
require __DIR__.'/../models/user.php';
require __DIR__.'/../models/project.php';
require __DIR__.'/../models/task.php';
require __DIR__.'/../models/board.php';
require __DIR__.'/../models/comment.php';

abstract class Base
{
    protected $request;
    protected $response;
    protected $session;
    protected $template;
    protected $user;
    protected $project;
    protected $task;
    protected $board;
    protected $config;
    protected $acl;
    protected $comment;

    public function __construct()
    {
        $this->request = new \Request;
        $this->response = new \Response;
        $this->session = new \Session;
        $this->template = new \Template;
        $this->config = new \Model\Config;
        $this->user = new \Model\User;
        $this->project = new \Model\Project;
        $this->task = new \Model\Task;
        $this->board = new \Model\Board;
        $this->acl = new \Model\Acl;
        $this->comment = new \Model\Comment;
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
