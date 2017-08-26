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
        $task_assignee_ids = $this->getAssigneeIdsByTask($task_id);
        $assignee_ids = array_filter($assignee_ids);

        return $this->associateAssignees($task_id, $task_assignee_ids, $assignee_ids) &&
            $this->dissociateAssignees($task_id, $task_assignee_ids, $assignee_ids);
    }

    /**
     * Associate additional assignees to a task
     *
     * @access protected
     * @param  integer  $task_id
     * @param  array    $task_assignees
     * @return bool
     */
    protected function associateAssignees($task_id, $task_assignee_ids, array $assignee_ids)
    {
        foreach ($assignee_ids as $assignee_id) {

            if (! isset($task_assignee_ids[$assignee_id]) && ! $this->associateAssignee($task_id, $assignee_id)) {
                return false;
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
    protected function dissociateAssignees($task_id, $task_assignee_ids, $assignee_ids)
    {
        foreach ($task_assignee_ids as $task_assignee_id) {
            if (! in_array($task_assignee_id, $assignee_ids)) {
                if (! $this->dissociateTag($task_id, $assignee_id)) {
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
