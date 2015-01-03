<?php

namespace Controller;

use Pimple\Container;
use Core\Security;
use Core\Request;
use Core\Response;
use Core\Template;
use Core\Session;
use Model\LastLogin;
use Symfony\Component\EventDispatcher\Event;

/**
 * Base controller
 *
 * @package  controller
 * @author   Frederic Guillot
 *
 * @property \Core\Session                 $session
 * @property \Core\Template                $template
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
 * @property \Model\UserSession            $userSession
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
    }

    /**
     * Destructor
     *
     * @access public
     */
    public function __destruct()
    {
        if (DEBUG) {

            foreach ($this->container['db']->getLogMessages() as $message) {
                $this->container['logger']->debug($message);
            }

            $this->container['logger']->debug('SQL_QUERIES={nb}', array('nb' => $this->container['db']->nb_queries));
            $this->container['logger']->debug('RENDERING={time}', array('time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']));
        }
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
        return $this->container[$name];
    }

    /**
     * Send HTTP headers
     *
     * @access private
     */
    private function sendHeaders($action)
    {
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
        $this->sendHeaders($action);
        $this->container['dispatcher']->dispatch('session.bootstrap', new Event);

        if (! $this->acl->isPublicAction($controller, $action)) {
            $this->handleAuthenticatedUser($controller, $action);
        }
    }

    /**
     * Check page access and authentication
     *
     * @access public
     */
    public function handleAuthenticatedUser($controller, $action)
    {
        if (! $this->authentication->isAuthenticated()) {

            if ($this->request->isAjax()) {
                $this->response->text('Not Authorized', 401);
            }

            $this->response->redirect('?controller=user&action=login&redirect_query='.urlencode($this->request->getQueryString()));
        }

        if (! $this->acl->isAllowed($controller, $action, $this->request->getIntegerParam('project_id', 0))) {
            $this->forbidden();
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
        $content = $this->template->render($template, $params);
        $params['task_content_for_layout'] = $content;
        $params['title'] = $params['task']['project_name'].' &gt; '.$params['task']['title'];
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->userSession->getId());

        return $this->template->layout('task/layout', $params);
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
        $content = $this->template->render($template, $params);
        $params['project_content_for_layout'] = $content;
        $params['title'] = $params['project']['name'] === $params['title'] ? $params['title'] : $params['project']['name'].' &gt; '.$params['title'];
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->userSession->getId());

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

        if (! $task || $task['project_id'] != $this->request->getIntegerParam('project_id')) {
            $this->notfound();
        }

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

        return $project;
    }
}
