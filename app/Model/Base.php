<?php

namespace Model;

use Pimple\Container;

/**
 * Base model class
 *
 * @package  model
 * @author   Frederic Guillot
 *
 * @property \Core\Session             $session
 * @property \Core\Template            $template
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
 * @property \Model\Helper             $helper
 * @property \Model\LastLogin          $lastLogin
 * @property \Model\Notification       $notification
 * @property \Model\Project            $project
 * @property \Model\ProjectPermission  $projectPermission
 * @property \Model\SubTask            $subTask
 * @property \Model\SubtaskHistory     $subtaskHistory
 * @property \Model\Swimlane           $swimlane
 * @property \Model\Task               $task
 * @property \Model\TaskCreation       $taskCreation
 * @property \Model\TaskExport         $taskExport
 * @property \Model\TaskFinder         $taskFinder
 * @property \Model\TaskHistory        $taskHistory
 * @property \Model\TaskPosition       $taskPosition
 * @property \Model\TaskValidator      $taskValidator
 * @property \Model\TimeTracking       $timeTracking
 * @property \Model\User               $user
 * @property \Model\UserSession        $userSession
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
        $this->db = $this->container['db'];
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
     * Save a record in the database
     *
     * @access public
     * @param  string            $table      Table name
     * @param  array             $values     Form values
     * @return boolean|integer
     */
    public function persist($table, array $values)
    {
        return $this->db->transaction(function($db) use ($table, $values) {

            if (! $db->table($table)->save($values)) {
                return false;
            }

            return (int) $db->getConnection()->getLastId();
        });
    }

    /**
     * Remove keys from an array
     *
     * @access public
     * @param  array     $values    Input array
     * @param  string[]  $keys      List of keys to remove
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
     * @param  array        $values    Input array
     * @param  string[]     $keys      List of keys
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
     * @param  array        $values    Input array
     * @param  string[]     $keys      List of keys
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
