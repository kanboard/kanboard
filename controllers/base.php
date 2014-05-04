<?php

namespace Controller;

/**
 * Base controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
abstract class Base
{
    /**
     * Request instance
     *
     * @accesss public
     * @var \Core\Request
     */
    public $request;

    /**
     * Response instance
     *
     * @accesss public
     * @var \Core\Response
     */
    public $response;

    /**
     * Template instance
     *
     * @accesss public
     * @var \Core\Template
     */
    public $template;

    /**
     * Session instance
     *
     * @accesss public
     * @var \Core\Session
     */
    public $session;

    /**
     * Acl model
     *
     * @accesss protected
     * @var \Model\Acl
     */
    protected $acl;

    /**
     * Action model
     *
     * @accesss protected
     * @var \Model\Action
     */
    protected $action;

    /**
     * Board model
     *
     * @accesss protected
     * @var \Model\Board
     */
    protected $board;

    /**
     * Config model
     *
     * @accesss protected
     * @var \Model\Config
     */
    protected $config;

    /**
     * Project model
     *
     * @accesss protected
     * @var \Model\Project
     */
    protected $project;

    /**
     * Task model
     *
     * @accesss protected
     * @var \Model\Task
     */
    protected $task;

    /**
     * User model
     *
     * @accesss protected
     * @var \Model\User
     */
    protected $user;

    /**
     * Comment model
     *
     * @accesss protected
     * @var \Model\Comment
     */
    protected $comment;

    /**
     * RememberMe model
     *
     * @accesss protected
     * @var \Model\RememberMe
     */
    protected $rememberMe;

    /**
     * LastLogin model
     *
     * @accesss protected
     * @var \Model\LastLogin
     */
    protected $lastLogin;

    /**
     * Google model
     *
     * @accesss protected
     * @var \Model\Google
     */
    protected $google;

    /**
     * Event instance
     *
     * @accesss protected
     * @var \Model\Event
     */
    protected $event;

    /**
     * Constructor
     *
     * @access public
     * @param  \Core\Registry  $registry
     */
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
        $this->rememberMe = $registry->rememberMe;
        $this->lastLogin = $registry->lastLogin;
        $this->google = $registry->google;
        $this->event = $registry->shared('event');
    }

    /**
     * Method executed before each action
     *
     * @access public
     */
    public function beforeAction($controller, $action)
    {
        // Start the session
        $this->session->open(BASE_URL_DIRECTORY, SESSION_SAVE_PATH);

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

        // Authentication
        if (! $this->acl->isLogged() && ! $this->acl->isPublicAction($controller, $action)) {

            // Try the remember me authentication first
            if (! $this->rememberMe->authenticate()) {

                // Redirect to the login form if not authenticated
                $this->response->redirect('?controller=user&action=login');
            }
            else {

                $this->lastLogin->create(
                    \Model\LastLogin::AUTH_REMEMBER_ME,
                    $this->acl->getUserId(),
                    $this->user->getIpAddress(),
                    $this->user->getUserAgent()
                );
            }
        }
        else if ($this->rememberMe->hasCookie()) {
            $this->rememberMe->refresh();
        }

        // Check if the user is allowed to see this page
        if (! $this->acl->isPageAccessAllowed($controller, $action)) {
            $this->response->redirect('?controller=user&action=forbidden');
        }

        // Attach events for automatic actions
        $this->action->attachEvents();
    }

    /**
     * Check if the current user have access to the given project
     *
     * @access protected
     * @param  integer   $project_id  Project id
     */
    protected function checkProjectPermissions($project_id)
    {
        if ($this->acl->isRegularUser()) {

            if ($project_id > 0 && ! $this->project->isUserAllowed($project_id, $this->acl->getUserId())) {
                $this->response->redirect('?controller=project&action=forbidden');
            }
        }
    }

    /**
     * Redirection when there is no project in the database
     *
     * @access protected
     */
    protected function redirectNoProject()
    {
        $this->session->flash(t('There is no active project, the first step is to create a new project.'));
        $this->response->redirect('?controller=project&action=create');
    }

    /**
     * Application not found page (404 error)
     *
     * @access public
     */
    public function notfound()
    {
        $this->response->html($this->template->layout('app_notfound', array('title' => t('Page not found'))));
    }

    /**
     * Display the template show task (common between different actions)
     *
     * @access protected
     * @param  array  $task               Task data
     * @param  array  $comment_form       Comment form data
     * @param  array  $description_form   Description form data
     * @param  array  $comment_edit_form  Comment edit form data
     */
    protected function showTask(array $task, array $comment_form = array(), array $description_form = array(), array $comment_edit_form = array())
    {
        if (empty($comment_form)) {
            $comment_form = array(
                'values' => array('task_id' => $task['id'], 'user_id' => $this->acl->getUserId()),
                'errors' => array()
            );
        }

        if (empty($description_form)) {
            $description_form = array(
                'values' => array('id' => $task['id']),
                'errors' => array()
            );
        }

        if (empty($comment_edit_form)) {
            $comment_edit_form = array(
                'values' => array('id' => 0),
                'errors' => array()
            );
        }
        else {
            $hide_comment_form = true;
        }

        $this->response->html($this->template->layout('task_show', array(
            'hide_comment_form' => isset($hide_comment_form),
            'comment_edit_form' => $comment_edit_form,
            'comment_form' => $comment_form,
            'description_form' => $description_form,
            'comments' => $this->comment->getAll($task['id']),
            'task' => $task,
            'columns_list' => $this->board->getColumnsList($task['project_id']),
            'colors_list' => $this->task->getColors(),
            'menu' => 'tasks',
            'title' => $task['title'],
        )));
    }
}
