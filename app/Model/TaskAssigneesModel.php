<?php

namespace Kanboard\Model;

use PicoDb\Database;
use Kanboard\Core\Base;
use Kanboard\Core\Security\Token;
use Kanboard\Core\Security\Role;

/**
 * User model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TaskAssigneesModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'task_has_users';

    /**
     * Get all assignees associated to a task
     *
     * @access public
     * @param  integer $task_id
     * @return array
     */
    public function getAssigneesByTask($task_id)
    {
        return $this->db->table(UserModel::TABLE)
            ->columns(UserModel::TABLE.'.id', UserModel::TABLE.'.name')
            ->eq(self::TABLE.'.task_id', $task_id)
            ->join(self::TABLE, 'user_id', 'id')
            ->findAll();
    }

    /**
     * Get all assignees ids associated to a task
     *
     * @access public
     * @param  integer $task_id
     * @return array
     */
    public function getAssigneeIdsByTask($task_id)
    {
        return $this->db->table(UserModel::TABLE)
            ->columns(UserModel::TABLE.'.id')
            ->eq(self::TABLE.'.task_id', $task_id)
            ->join(self::TABLE, 'user_id', 'id')
            ->findAll();
    }

    /**
     * Get dictionary of tags
     *
     * @access public
     * @param  integer $task_id
     * @return array
     */
    public function getList($task_id)
    {
        $assignees = $this->getAssigneesByTask($task_id);
        return array_column($assignees, 'name', 'id');
    }

    /**
     * Add or update a list of tags to a task
     *
     * @access public
     * @param  integer  $project_id
     * @param  integer  $task_id
     * @param  string[] $tags
     * @return boolean
     */
    public function save($task_id, array $assignee_ids)
    {
        $task_assignees = $this->getList($task_id);
        $assignee_ids = array_filter($assignee_ids);

        $ret = $this->associateAssignees($task_id, $task_assignees, $assignee_ids) &
               $this->dissociateAssignees($task_id, $task_assignees, $assignee_ids);
        return $ret;
    }

    /**
     * Associate additional assignees to a task
     *
     * @access protected
     * @param  integer  $task_id
     * @param  array    $task_assignees
     * @return bool
     */
    protected function associateAssignees($task_id, $task_assignees, array $assignees_ids)
    {
        foreach ($assignee_ids as $user_id ) {
            if (! isset($task_assignees[$user_id])) {
                if (! $this->associateAssignee($task_id, $user_id)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Dissociate removed assignees from a task 
     *
     * @access protected
     * @param  integer  $task_id
     * @param  array    $task_tags
     * @param  string[] $tags
     * @return bool
     */
    protected function dissociateAssignees($task_id, $task_assignees, $assignees_ids)
    {
        foreach ($task_assignees as $user_id => $task_assignee) {
            if (! in_array($user_id, $assignees_ids)) {
                if (! $this->dissociateAssignee($task_id, $user_id)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Associate a user from a task
     *
     * @access public
     * @param  integer  $task_id  
     * @param  integer  $user_id
     * @return boolean
     */
    public function associateAssignee($task_id, $user_id)
    {
        return $this->db->table(self::TABLE)->insert(array(
            'task_id' => $task_id,
            'user_id' => $user_id,
            )); 
    }

    /**
     * Dissociate a user from a task
     *
     * @access public
     * @param  integer  $task_id  
     * @param  integer  $user_id
     * @return boolean
     */
    public function dissociateAssignee($task_id, $user_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('task_id', $task_id)
            ->eq('user_id', $user_id)
            ->remove(); 
    }

}
