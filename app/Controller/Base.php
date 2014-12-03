<?php

namespace Controller;

use Pimple\Container;
use Core\Tool;
use Core\Security;
use Core\Request;
use Core\Response;
use Core\Template;
use Core\Session;
use Model\LastLogin;

/**
 * Base controller
 *
 * @package  controller
 * @author   Frederic Guillot
 *
 * @property \Model\Acl                    $acl
 * @property \Model\Authentication         $authentication
 * @property \Model\Action                 $action
 * @property \Model\Board                  $board
 * @property \Model\Category               $category
 * @property \Model\Color                  $color
 * @property \Model\Comment                $comment
 * @property \Model\Config                 $config
 * @property \Model\DateParser             $dateParser
 * @property \Model\File                   $file
 * @property \Model\LastLogin              $lastLogin
 * @property \Model\Notification           $notification
 * @property \Model\Project                $project
 * @property \Model\ProjectPermission      $projectPermission
 * @property \Model\ProjectAnalytic        $projectAnalytic
 * @property \Model\ProjectDailySummary    $projectDailySummary
 * @property \Model\SubTask                $subTask
 * @property \Model\Task                   $task
 * @property \Model\TaskCreation           $taskCreation
 * @property \Model\TaskModification       $taskModification
 * @property \Model\TaskDuplication        $taskDuplication
 * @property \Model\TaskHistory            $taskHistory
 * @property \Model\TaskExport             $taskExport
 * @property \Model\TaskFinder             $taskFinder
 * @property \Model\TaskPosition           $taskPosition
 * @property \Model\TaskPermission         $taskPermission
 * @property \Model\TaskStatus             $taskStatus
 * @property \Model\TaskValidator          $taskValidator
 * @property \Model\CommentHistory         $commentHistory
 * @property \Model\SubtaskHistory         $subtaskHistory
 * @property \Model\TimeTracking           $timeTracking
 * @property \Model\User                   $user
 * @property \Model\Webhook                $webhook
 */
abstract class Base
{
    /**
     * Request instance
     *
     * @accesss protected
     * @var \Core\Request
     */
    protected $request;

    /**
     * Response instance
     *
     * @accesss protected
     * @var \Core\Response
     */
    protected $response;

    /**
     * Template instance
     *
     * @accesss protected
     * @var \Core\Template
     */
    protected $template;

    /**
     * Session instance
     *
     * @accesss public
     * @var \Core\Session
     */
    protected $session;

    /**
     * Container instance
     *
     * @access private
     * @var \Pimple\Container
     */
    private $container;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->request = new Request;
        $this->response = new Response;
        $this->session = new Session;
        $this->template = new Template;
    }

    /**
     * Destructor
     *
     * @access public
     */
    public function __destruct()
    {
        // foreach ($this->container['db']->getLogMessages() as $message) {
        //     $this->container['logger']->addDebug($message);
        // }
    }

    /**
     * Load automatically models
     *
     * @access public
     * @param  string    $name    Model name
     * @return mixed
     */
    public function __get($name)
    {
        return Tool::loadModel($this->container, $name);
    }

    /**
     * Method executed before each action
     *
     * @access public
     */
    public function beforeAction($controller, $action)
    {
        // Start the session
        $this->session->open(BASE_URL_DIRECTORY);

        // HTTP secure headers
        $this->response->csp(array('style-src' => "'self' 'unsafe-inline'"));
        $this->response->nosniff();
        $this->response->xss();

        // Allow the public board iframe inclusion
        if ($action !== 'readonly') {
            $this->response->xframe();
        }

        if (ENABLE_HSTS) {
            $this->response->hsts();
        }

        $this->config->setupTranslations();
        $this->config->setupTimezone();

        // Authentication
        if (! $this->authentication->isAuthenticated($controller, $action)) {

            if ($this->request->isAjax()) {
                $this->response->text('Not Authorized', 401);
            }

            $this->response->redirect('?controller=user&action=login&redirect_query='.urlencode($this->request->getQueryString()));
        }

        // Check if the user is allowed to see this page
        if (! $this->acl->isPageAccessAllowed($controller, $action)) {
            $this->response->redirect('?controller=user&action=forbidden');
        }

        // Attach events
        $this->attachEvents();
    }

    /**
     * Attach events
     *
     * @access private
     */
    private function attachEvents()
    {
        $models = array(
            'projectActivity', // Order is important
            'projectDailySummary',
            'action',
            'project',
            'webhook',
            'notification',
        );

        foreach ($models as $model) {
            $this->$model->attachEvents();
        }
    }

    /**
     * Application not found page (404 error)
     *
     * @access public
     * @param  boolean   $no_layout   Display the layout or not
     */
    public function notfound($no_layout = false)
    {
        $this->response->html($this->template->layout('app/notfound', array(
            'title' => t('Page not found'),
            'no_layout' => $no_layout,
        )));
    }

    /**
     * Application forbidden page
     *
     * @access public
     * @param  boolean   $no_layout   Display the layout or not
     */
    public function forbidden($no_layout = false)
    {
        $this->response->html($this->template->layout('app/forbidden', array(
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
        if (! Security::validateCSRFToken($this->request->getStringParam('csrf_token'))) {
            $this->forbidden();
        }
    }

    /**
     * Check if the current user have access to the given project
     *
     * @access protected
     * @param  integer   $project_id  Project id
     */
    protected function checkProjectPermissions($project_id)
    {
        if ($this->acl->isRegularUser() && ! $this->projectPermission->isUserAllowed($project_id, $this->acl->getUserId())) {
            $this->forbidden();
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
     * Common layout for task views
     *
     * @access protected
     * @param  string $template Template name
     * @param  array $params Template parameters
     * @return string
     */
    protected function taskLayout($template, array $params)
    {
        if (isset($params['task']) && $this->taskPermission->canRemoveTask($params['task']) === false) {
            $params['hide_remove_menu'] = true;
        }

        $content = $this->template->load($template, $params);
        $params['task_content_for_layout'] = $content;
        $params['title'] = $params['task']['project_name'].' &gt; '.$params['task']['title'];
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->acl->getUserId());

        return $this->template->layout('task_layout', $params);
    }

    /**
     * Common layout for project views
     *
     * @access protected
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    protected function projectLayout($template, array $params)
    {
        $content = $this->template->load($template, $params);
        $params['project_content_for_layout'] = $content;
        $params['title'] = $params['project']['name'] === $params['title'] ? $params['title'] : $params['project']['name'].' &gt; '.$params['title'];
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->acl->getUserId());

        return $this->template->layout('project/layout', $params);
    }

    /**
     * Common method to get a task for task views
     *
     * @access protected
     * @return array
     */
    protected function getTask()
    {
        $task = $this->taskFinder->getDetails($this->request->getIntegerParam('task_id'));

        if (! $task) {
            $this->notfound();
        }

        $this->checkProjectPermissions($task['project_id']);

        return $task;
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
        $project = $this->project->getById($project_id);

        if (! $project) {
            $this->session->flashError(t('Project not found.'));
            $this->response->redirect('?controller=project');
        }

        $this->checkProjectPermissions($project['id']);

        return $project;
    }

    /**
     * Common method to get a project with administration rights
     *
     * @access protected
     * @return array
     */
    protected function getProjectManagement()
    {
        $project = $this->project->getById($this->request->getIntegerParam('project_id'));

        if (! $project) {
            $this->notfound();
        }

        if ($this->acl->isRegularUser() && ! $this->projectPermission->adminAllowed($project['id'], $this->acl->getUserId())) {
            $this->forbidden();
        }

        return $project;
    }
}
