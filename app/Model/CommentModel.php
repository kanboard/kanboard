<?php

namespace Kanboard\Model;

use Kanboard\Event\CommentEvent;
use Kanboard\Core\Base;

/**
 * Comment model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class CommentModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'comments';

    /**
     * Events
     *
     * @var string
     */
    const EVENT_UPDATE       = 'comment.update';
    const EVENT_CREATE       = 'comment.create';
    const EVENT_USER_MENTION = 'comment.user.mention';

    /**
     * Get projectId from commentId
     *
     * @access public
     * @param  integer $comment_id
     * @return integer
     */
    public function getProjectId($comment_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq(self::TABLE.'.id', $comment_id)
            ->join(TaskModel::TABLE, 'id', 'task_id')
            ->findOneColumn(TaskModel::TABLE . '.project_id') ?: 0;
    }

    /**
     * Get all comments for a given task
     *
     * @access public
     * @param  integer  $task_id  Task id
     * @param  string   $sorting  ASC/DESC
     * @return array
     */
    public function getAll($task_id, $sorting = 'ASC')
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.date_creation',
                self::TABLE.'.task_id',
                self::TABLE.'.user_id',
                self::TABLE.'.comment',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name',
                UserModel::TABLE.'.email',
                UserModel::TABLE.'.avatar_path'
            )
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->orderBy(self::TABLE.'.date_creation', $sorting)
            ->eq(self::TABLE.'.task_id', $task_id)
            ->findAll();
    }

    /**
     * Get a comment
     *
     * @access public
     * @param  integer  $comment_id  Comment id
     * @return array
     */
    public function getById($comment_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.task_id',
                self::TABLE.'.user_id',
                self::TABLE.'.date_creation',
                self::TABLE.'.comment',
                self::TABLE.'.reference',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name',
                UserModel::TABLE.'.email',
                UserModel::TABLE.'.avatar_path'
            )
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->eq(self::TABLE.'.id', $comment_id)
            ->findOne();
    }

    /**
     * Get the number of comments for a given task
     *
     * @access public
     * @param  integer  $task_id  Task id
     * @return integer
     */
    public function count($task_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq(self::TABLE.'.task_id', $task_id)
            ->count();
    }

    /**
     * Create a new comment
     *
     * @access public
     * @param  array    $values   Form values
     * @return boolean|integer
     */
    public function create(array $values)
    {
        $values['date_creation'] = time();
        $comment_id = $this->db->table(self::TABLE)->persist($values);

        if ($comment_id !== false) {
            $event = new CommentEvent(array('id' => $comment_id) + $values);
            $this->dispatcher->dispatch(self::EVENT_CREATE, $event);
            $this->userMentionModel->fireEvents($values['comment'], self::EVENT_USER_MENTION, $event);
        }

        return $comment_id;
    }

    /**
     * Update a comment in the database
     *
     * @access public
     * @param  array    $values   Form values
     * @return boolean
     */
    public function update(array $values)
    {
        $result = $this->db
                    ->table(self::TABLE)
                    ->eq('id', $values['id'])
                    ->update(array('comment' => $values['comment']));

        if ($result) {
            $this->container['dispatcher']->dispatch(self::EVENT_UPDATE, new CommentEvent($values));
        }

        return $result;
    }

    /**
     * Remove a comment
     *
     * @access public
     * @param  integer  $comment_id  Comment id
     * @return boolean
     */
    public function remove($comment_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $comment_id)->remove();
    }
}
