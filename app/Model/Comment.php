<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Comment model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Comment extends Base
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
    const EVENT_UPDATE = 'comment.update';
    const EVENT_CREATE = 'comment.create';

    /**
     * Get all comments for a given task
     *
     * @access public
     * @param  integer  $task_id  Task id
     * @return array
     */
    public function getAll($task_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.date',
                self::TABLE.'.task_id',
                self::TABLE.'.user_id',
                self::TABLE.'.comment',
                User::TABLE.'.username',
                User::TABLE.'.name'
            )
            ->join(User::TABLE, 'id', 'user_id')
            ->orderBy(self::TABLE.'.date', 'ASC')
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
                self::TABLE.'.date',
                self::TABLE.'.comment',
                User::TABLE.'.username',
                User::TABLE.'.name'
            )
            ->join(User::TABLE, 'id', 'user_id')
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
     * Save a comment in the database
     *
     * @access public
     * @param  array    $values   Form values
     * @return boolean
     */
    public function create(array $values)
    {
        $values['date'] = time();

        if ($this->db->table(self::TABLE)->save($values)) {

            $values['id'] = $this->db->getConnection()->getLastId();
            $this->event->trigger(self::EVENT_CREATE, $values);
            return true;
        }

        return false;
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

        $this->event->trigger(self::EVENT_UPDATE, $values);

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

    /**
     * Validate comment creation
     *
     * @access public
     * @param  array   $values           Required parameters to save an action
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('task_id', t('This value is required')),
            new Validators\Integer('task_id', t('This value must be an integer')),
            new Validators\Required('user_id', t('This value is required')),
            new Validators\Integer('user_id', t('This value must be an integer')),
            new Validators\Required('comment', t('Comment is required'))
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate comment modification
     *
     * @access public
     * @param  array   $values           Required parameters to save an action
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('id', t('This value is required')),
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Required('comment', t('Comment is required'))
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
