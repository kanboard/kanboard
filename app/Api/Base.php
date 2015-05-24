<?php

namespace Api;

use Pimple\Container;
use JsonRPC\AuthenticationFailure;
use Symfony\Component\EventDispatcher\Event;

/**
 * Base class
 *
 * @package  api
 * @author   Frederic Guillot
 *
 * @property \Model\Board                  $board
 * @property \Model\Config                 $config
 * @property \Model\Comment                $comment
 * @property \Model\LastLogin              $lastLogin
 * @property \Model\Notification           $notification
 * @property \Model\Project                $project
 * @property \Model\ProjectPermission      $projectPermission
 * @property \Model\ProjectActivity        $projectActivity
 * @property \Model\ProjectAnalytic        $projectAnalytic
 * @property \Model\ProjectDailySummary    $projectDailySummary
 * @property \Model\Subtask                $subtask
 * @property \Model\Task                   $task
 * @property \Model\TaskDuplication        $taskDuplication
 * @property \Model\TaskExport             $taskExport
 * @property \Model\TaskFinder             $taskFinder
 */
abstract class Base
{
    /**
     * Container instance
     *
     * @access protected
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Load automatically models
     *
     * @access public
     * @param  string $name Model name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container[$name];
    }

    /**
     * Check api credentials
     *
     * @access public
     * @param  string  $username
     * @param  string  $password
     * @param  string  $class
     * @param  string  $method
     */
    public function authentication($username, $password, $class, $method)
    {
        $this->container['dispatcher']->dispatch('api.bootstrap', new Event);

        if (! ($username === 'jsonrpc' && $password === $this->config->get('api_token'))) {
            throw new AuthenticationFailure('Wrond credentials');
        }
    }
}
