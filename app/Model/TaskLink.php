<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use PicoDb\Table;

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
     * Get the opposite task link id
     *
     * @access public
     * @param  integer   $task_link_id   Task link id
     * @return integer
     */
    public function getOppositeTaskLinkId($task_link_id)
    {
        $task_link_id = $this->getById($task_link_id);
        return $task_link_id['opposite_task_link_id'] ?: $task_link_id;
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
                    ->orderBy(Link::TABLE.'.id ASC, '.Board::TABLE.'.position ASC, '.Task::TABLE.'.is_active DESC, '.Task::TABLE.'.id', Table::SORT_ASC)
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

        // Create the opposite link
        $this->createOpposite($opposite_task_id, $task_id, $this->link->getOppositeLinkId($link_id), $this->db->getConnection()->getLastId());

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Create the opposite task link (executed inside create() method)
     * Parameters are inversed compared to create
     * 
     * @access private
     * @param  integer   $task_id               Task id
     * @param  integer   $opposite_task_id      Opposite task id
     * @param  integer   $link_id               Link id
     * @param  integer   $opposite_task_link_id Opposition task link id
     */
    private function createOpposite($task_id, $opposite_task_id, $link_id, $opposite_task_link_id)
    {
        $this->db->table(self::TABLE)->insert(array(
            'opposite_task_link_id' => $opposite_task_link_id,
            'task_id' => $task_id,
            'opposite_task_id' => $opposite_task_id,
            'link_id' => $link_id,
        ));

        $this->db->table(self::TABLE)->eq('id', $opposite_task_link_id)->update(array(
            'opposite_task_link_id' => $this->db->getConnection()->getLastId()
        ));
    }

    /**
     * Update an existing link
     *
     * @access public
     * @param  integer   $task_id            Task id
     * @param  integer   $opposite_task_id   Opposite task id
     * @param  integer   $link_id            Link id
     * @return boolean
     */
    public function update($task_link_id, $task_id, $opposite_task_id, $link_id)
    {
        $this->db->startTransaction();
    
        // Update the original link
        $this->db->table(self::TABLE)->eq('id', $task_link_id)->update(array(
            'task_id' => $task_id,
            'opposite_task_id' => $opposite_task_id,
            'link_id' => $link_id,
        ));

        $link_id = $this->link->getOppositeLinkId($link_id);
        $task_link_id = $this->getOppositeTaskLinkId($task_link_id);

        // Create the opposite link
        $this->db->table(self::TABLE)->eq('id', $task_link_id)->update(array(
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
     * Common validation rules
     *
     * @access private
     * @return array
     */
    private function commonValidationRules()
    {
        return array(
            new Validators\Required('task_id', t('Field required')),
            new Validators\Required('opposite_task_id', t('Field required')),
            new Validators\Required('link_id', t('Field required')),
            new Validators\NotEquals('opposite_task_id', 'task_id', t('A task can not be linked to itself')),
            new Validators\Exists('opposite_task_id', t('This linked task id doesn\'t exist'), $this->db->getConnection(), Task::TABLE, 'id')
        );
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
        $v = new Validator($values, $this->commonValidationRules());

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('Field required')),
        );
        
        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
