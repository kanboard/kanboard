<?php
namespace Model;

use Core\Helper;
use SimpleValidator\Validator;
use SimpleValidator\Validators;
use PDO;

/**
 * TaskLink model
 *
 * @package model
 * @author Olivier Maridat
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
     * Return true if a link exists
     *
     * @access public
     * @param integer $link_id
     *            Task link id
     * @param integer $task_id
     *            Task id
     * @param integer $task_inverse_id
     *            Inverse task id
     * @return boolean
     */
    public function exists($link_id, $task_id, $task_inverse_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('link_id', $link_id)
            ->eq('task_id', $task_id)
            ->eq('task_inverse_id', $task_inverse_id)
            ->count() === 1;
    }

    /**
     * Get a link by the task id
     *
     * @access public
     * @param integer $link_id
     *            Task link id
     * @return array
     */
    public function getById($link_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $link_id)
            ->findOne();
    }

    /**
     * Return all links for a given task
     *
     * @access public
     * @param integer $task_id
     *            Task id
     * @return array
     */
    public function getAll($task_id)
    {
        $sql = 'SELECT
            ' . self::TABLE . '.id,
            ' . Link::TABLE . '.name AS name,
            ' . self::TABLE . '.task_inverse_id,
            ' . Task::TABLE . '.project_id AS task_inverse_project_id,
            ' . Category::TABLE . '.name AS task_inverse_category,
            ' . Task::TABLE . '.title AS task_inverse_name,
            ' . Task::TABLE . '.is_active AS task_inverse_is_active
            FROM ' . self::TABLE . '
            LEFT JOIN ' . Link::TABLE . ' ON ' . Link::TABLE . '.id = link_id
            LEFT JOIN ' . Task::TABLE . ' ON ' . Task::TABLE . '.id = task_inverse_id
            LEFT JOIN ' . Category::TABLE . ' ON ' . Category::TABLE . '.id = ' . Task::TABLE . '.category_id
            WHERE task_id = ?
            ORDER BY ' . Link::TABLE . '.name, ' . Category::TABLE . '.name, ' . self::TABLE . '.task_inverse_id
        ';
        $rq = $this->db->execute($sql, array(
            $task_id
        ));
        $res = $rq->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    /**
     * Prepare data before insert/update
     *
     * @access public
     * @param array $values
     *            Form values
     */
    public function prepare(array &$values)
    {
        $this->removeFields($values, array(
            'another_link'
        ));
        $taskLink1 = $values;
        $taskLink2 = array(
            'task_id' => $values['task_inverse_id'],
            'task_inverse_id' => $values['task_id']
        );
        if (0 == $values['link_id'] % 2) {
            $taskLink2['link_id'] = $values['link_id'] - 1;
        }
        else {
            $taskLink2['link_id'] = $values['link_id'] + 1;
        }
        return array(
            $taskLink1,
            $taskLink2
        );
    }

    /**
     * Create a link
     *
     * @access public
     * @param array $values
     *            Form values
     * @return bool integer
     */
    public function create(array $values)
    {
        list ($taskLink1, $taskLink2) = $this->prepare($values);
        $this->db->startTransaction();
        $res = $this->db->table(self::TABLE)->save($taskLink1);
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        $res = $this->db->table(self::TABLE)->save($taskLink2);
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return true;
    }

    /**
     * Update a link
     *
     * @access public
     * @param array $values
     *            Form values
     * @return bool
     */
    public function update(array $values)
    {
        list ($taskLink1, $taskLink2) = $this->prepare($values);
        $this->db->startTransaction();
        $res = $this->db->table(self::TABLE)
            ->eq('id', $values['id'])
            ->save($taskLink1);
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        $res = $this->db->table(self::TABLE)
            ->eq('id', (0 == ($values['id'] % 2)) ? $values['id'] - 1 : $values['id'] + 1)
            ->save($taskLink2);
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return true;
    }

    /**
     * Remove a link
     *
     * @access public
     * @param integer $id
     *            Link id
     * @return bool
     */
    public function remove($id)
    {
        $this->db->startTransaction();
        $res = $this->db->table(self::TABLE)
            ->eq('id', $id)
            ->remove();
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        $res = $this->db->table(self::TABLE)
            ->eq('id', (0 == ($id % 2)) ? $id - 1 : $id + 1)
            ->remove();
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return true;
    }

    /**
     * Duplicate all links to another task
     *
     * @access public
     * @param integer $src_task_id
     *            Source task id
     * @param integer $dst_task_id
     *            Destination task id
     * @return bool
     */
    public function duplicate($src_task_id, $dst_task_id)
    {
        return $this->db->transaction(function ($db) use($src_task_id, $dst_task_id)
        {
            $links = $db->table(TaskLink::TABLE)
                ->columns('link_id', 'task_id', 'task_inverse_id')
                ->eq('task_id', $src_task_id)
                ->asc('id')
                ->findAll();
            foreach ($links as &$link) {
                $link['task_id'] = $dst_task_id;
                if (! $db->table(TaskLink::TABLE)
                    ->save($link)) {
                    return false;
                }
            }
            
            $links = $db->table(TaskLink::TABLE)
                ->columns('link_id', 'task_id', 'task_inverse_id')
                ->eq('task_inverse_id', $src_task_id)
                ->asc('id')
                ->findAll();
            foreach ($links as &$link) {
                $link['task_inverse_id'] = $dst_task_id;
                if (! $db->table(TaskLink::TABLE)
                    ->save($link)) {
                    return false;
                }
            }
        });
    }

    /**
     * Validate link creation
     *
     * @access public
     * @param array $values
     *            Form values
     * @return array $valid, $errors [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, $this->commonValidationRules());
        $res = array(
            $v->execute(),
            $v->getErrors()
        );
        return $res;
    }

    /**
     * Validate link modification
     *
     * @access public
     * @param array $values
     *            Form values
     * @return array $valid, $errors [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required'))
        );
        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));
        $res = array(
            $v->execute(),
            $v->getErrors()
        );
        return $res;
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
            new Validators\Required('link_id', t('The link type is required')),
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('task_inverse_id', t('The linked task id is required')),
            new Validators\Integer('id', t('The id must be an integer')),
            new Validators\Integer('link_id', t('The link id must be an integer')),
            new Validators\Integer('task_id', t('The task id must be an integer')),
            new Validators\Integer('task_inverse_id', t('The related task id must be an integer')),
            new Validators\NotEquals('task_inverse_id', 'task_id', t('A task can not be linked to itself')),
            new Validators\Exists('task_inverse_id', t('This linked task id doesn\'t exist'), $this->db->getConnection(), Task::TABLE, 'id'),
            new Validators\Unique(array('task_inverse_id', 'link_id', 'task_id'), t('The exact same link already exists'), $this->db->getConnection(), self::TABLE),
        );
    }
}
