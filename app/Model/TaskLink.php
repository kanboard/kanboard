<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * TaskLink model
 *
 * @package model
 * @author  Olivier Maridat
 * @author  Frederic Guillot
 */
class TaskLink extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'task_has_links';

    /**
     * Get a task link
     *
     * @access public
     * @param  integer   $task_link_id   Task link id
     * @return array
     */
    public function getById($task_link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $task_link_id)->findOne();
    }

    /**
     * Get all links attached to a task
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return array
     */
    public function getLinks($task_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->columns(
                        self::TABLE.'.id',
                        self::TABLE.'.opposite_task_id AS task_id',
                        Link::TABLE.'.label',
                        Task::TABLE.'.title',
                        Task::TABLE.'.is_active',
                        Task::TABLE.'.project_id',
                        Board::TABLE.'.title AS column_title'
                    )
                    ->eq(self::TABLE.'.task_id', $task_id)
                    ->join(Link::TABLE, 'id', 'link_id')
                    ->join(Task::TABLE, 'id', 'opposite_task_id')
                    ->join(Board::TABLE, 'id', 'column_id', Task::TABLE)
                    ->findAll();
    }

    /**
     * Create a new link
     *
     * @access public
     * @param  integer   $task_id            Task id
     * @param  integer   $opposite_task_id   Opposite task id
     * @param  integer   $link_id            Link id
     * @return boolean
     */
    public function create($task_id, $opposite_task_id, $link_id)
    {
        $this->db->startTransaction();

        // Create the original link
        $this->db->table(self::TABLE)->insert(array(
            'task_id' => $task_id,
            'opposite_task_id' => $opposite_task_id,
            'link_id' => $link_id,
        ));

        $link_id = $this->link->getOppositeLinkId($link_id);

        // Create the opposite link
        $this->db->table(self::TABLE)->insert(array(
            'task_id' => $opposite_task_id,
            'opposite_task_id' => $task_id,
            'link_id' => $link_id,
        ));

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Remove a link between two tasks
     *
     * @access public
     * @param  integer   $task_link_id
     * @return boolean
     */
    public function remove($task_link_id)
    {
        $this->db->startTransaction();

        $link = $this->getById($task_link_id);
        $link_id = $this->link->getOppositeLinkId($link['link_id']);

        $this->db->table(self::TABLE)->eq('id', $task_link_id)->remove();

        $this->db
            ->table(self::TABLE)
            ->eq('opposite_task_id', $link['task_id'])
            ->eq('task_id', $link['opposite_task_id'])
            ->eq('link_id', $link_id)->remove();

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Validate creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('task_id', t('Field required')),
            new Validators\Required('link_id', t('Field required')),
            new Validators\Required('title', t('Field required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
