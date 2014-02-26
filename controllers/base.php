<?php

namespace Controller;

require __DIR__.'/../lib/request.php';
require __DIR__.'/../lib/response.php';
require __DIR__.'/../lib/session.php';
require __DIR__.'/../lib/template.php';
require __DIR__.'/../lib/helper.php';
require __DIR__.'/../lib/translator.php';
require __DIR__.'/../models/base.php';
require __DIR__.'/../models/config.php';
require __DIR__.'/../models/user.php';
require __DIR__.'/../models/project.php';
require __DIR__.'/../models/task.php';
require __DIR__.'/../models/board.php';

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
    }

    private function noAuthAllowed($controller, $action)
    {
        $public = array(
            'user' => array('login', 'check'),
            'task' => array('add'),
            'board' => array('readonly'),
        );

        if (isset($public[$controller])) {
            return in_array($action, $public[$controller]);
        }

        return false;
    }

    public function beforeAction($controller, $action)
    {
        $this->session->open(dirname($_SERVER['PHP_SELF']));

        if (! isset($_SESSION['user']) && ! $this->noAuthAllowed($controller, $action)) {
            $this->response->redirect('?controller=user&action=login');
        }

        // Load translations
        $language = $this->config->get('language', 'en_US');
        if ($language !== 'en_US') \Translator\load($language);

        // Set timezone
        date_default_timezone_set($this->config->get('timezone', 'UTC'));

        $this->response->csp();
        $this->response->nosniff();
        $this->response->xss();
        $this->response->hsts();
        $this->response->xframe();
    }

    public function checkPermissions()
    {
        if ($_SESSION['user']['is_admin'] == 0) {
            $this->response->redirect('?controller=user&action=forbidden');
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
