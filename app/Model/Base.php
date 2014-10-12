<?php

namespace Model;

use Core\Event;
use Core\Tool;
use Core\Registry;
use PicoDb\Database;

/**
 * Base model class
 *
 * @package  model
 * @author   Frederic Guillot
 *
 * @property \Model\Acl                $acl
 * @property \Model\Action             $action
 * @property \Model\Authentication     $authentication
 * @property \Model\Board              $board
 * @property \Model\Category           $category
 * @property \Model\Comment            $comment
 * @property \Model\CommentHistory     $commentHistory
 * @property \Model\Color              $color
 * @property \Model\Config             $config
 * @property \Model\DateParser         $dateParser
 * @property \Model\File               $file
 * @property \Model\LastLogin          $lastLogin
 * @property \Model\Notification       $notification
 * @property \Model\Project            $project
 * @property \Model\ProjectPermission  $projectPermission
 * @property \Model\SubTask            $subTask
 * @property \Model\SubtaskHistory     $subtaskHistory
 * @property \Model\Task               $task
 * @property \Model\TaskExport         $taskExport
 * @property \Model\TaskFinder         $taskFinder
 * @property \Model\TaskHistory        $taskHistory
 * @property \Model\TaskValidator      $taskValidator
 * @property \Model\TimeTracking       $timeTracking
 * @property \Model\User               $user
 * @property \Model\Webhook            $webhook
 */
abstract class Base
{
    /**
     * Database instance
     *
     * @access protected
     * @var \PicoDb\Database
     */
    protected $db;

    /**
     * Event dispatcher instance
     *
     * @access public
     * @var \Core\Event
     */
    public $event;

    /**
     * Registry instance
     *
     * @access protected
     * @var \Core\Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @access public
     * @param  \Core\Registry  $registry  Registry instance
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
        $this->db = $this->registry->shared('db');
        $this->event = $this->registry->shared('event');
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
        return Tool::loadModel($this->registry, $name);
    }

    /**
     * Remove keys from an array
     *
     * @access public
     * @param  array     $values    Input array
     * @param  array     $keys      List of keys to remove
     */
    public function removeFields(array &$values, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($values[$key])) {
                unset($values[$key]);
            }
        }
    }

    /**
     * Force some fields to be at 0 if empty
     *
     * @access public
     * @param  array     $values    Input array
     * @param  array     $keys      List of keys
     */
    public function resetFields(array &$values, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($values[$key]) && empty($values[$key])) {
                $values[$key] = 0;
            }
        }
    }

    /**
     * Force some fields to be integer
     *
     * @access public
     * @param  array     $values    Input array
     * @param  array     $keys      List of keys
     */
    public function convertIntegerFields(array &$values, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($values[$key])) {
                $values[$key] = (int) $values[$key];
            }
        }
    }
}
