<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use PDO;
/**
 * TaskLink model
 *
 * @package  model
 * @author   Olivier Maridat
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
     * @param  integer   $link_id     Task link id
     * @return boolean
     */
    public function exists($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->count() > 0;
    }

    /**
     * Get a link by the task id
     *
     * @access public
     * @param  integer   $link_id     Task link id
     * @return array
     */
    public function getById($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->findOne();
    }

    /**
     * Return all links for a given task
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getAll($task_id)
    {
        $sql = 'SELECT
            '.self::TABLE.'.id,
            '.self::TABLE.'.task_inverse_id,
            '.Link::TABLE.'.name AS name,
            '.Task::TABLE.'.title AS task_inverse_name
            FROM '.self::TABLE.'
            LEFT JOIN '.Link::TABLE.' ON '.Link::TABLE.'.id = link_id
            LEFT JOIN '.Task::TABLE.' ON '.Task::TABLE.'.id = task_inverse_id
            WHERE task_id = ?
        ';
        $rq = $this->db->execute($sql, array($task_id));
//         var_dump($this->db->getLogMessages());
        $res = $rq->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }
    
    /**
     * Prepare data before insert/update
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepare(array &$values)
    {
        $this->removeFields($values, array('another_link'));
        $taskLink1 = $values;
        $taskLink2 = array('task_id' => $values['task_inverse_id'], 'task_inverse_id' => $values['task_id']);
        if (0 == $values['link_id']%2) {
            $taskLink2['link_id'] = $values['link_id']-1;
        }
        else {
            $taskLink2['link_id'] = $values['link_id']+1;
        }
        return array($taskLink1, $taskLink2);
    }
    

    /**
     * Create a link
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool|integer
     */
    public function create(array $values)
    {
        list($taskLink1, $taskLink2) = $this->prepare($values); 
        $res = $this->persist(self::TABLE, $taskLink1, $taskLink2);
        return $res;
    }

    /**
     * Update a link
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool
     */
    public function update(array $values)
    {
        list($taskLink1, $taskLink2) = $this->prepare($values);
        $this->db->startTransaction();
        $res = $this->db->table(self::TABLE)->eq('id', $values['id'])->save($taskLink1);
        $res *= $this->db->table(self::TABLE)->eq('id', 0 == $values['link_id']%2 ? $values['id']-1 : $values['id']+1)->save($taskLink2);
        if (!$res) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return $res;
    }

    /**
     * Remove a link
     *
     * @access public
     * @param  integer   $link_id    Category id
     * @return bool
     */
    public function remove($id)
    {
        $res = $this->db->table(self::TABLE)->eq('id', $id)->remove();
        var_dump($this->db->getLogMessages());
        $res = $this->db->table(self::TABLE)->eq('id', 0 == $id%2 ? $id-1 : $id+1)->remove();
        return $res;
    }

    /**
     * Duplicate links from a project to another one, must be executed inside a transaction
     *
     * @author Antonio Rabelo
     * @param  integer    $src_project_id        Source project id
     * @return integer    $dst_project_id        Destination project id
     * @return boolean
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $links = $this->db->table(self::TABLE)
                               ->columns('name')
                               ->eq('project_id', $src_project_id)
                               ->asc('name')
                               ->findAll();

        foreach ($links as $link) {

            $link['project_id'] = $dst_project_id;

            if (! $this->db->table(self::TABLE)->save($link)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate link creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $rules = array(
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('task_inverse_id', t('The linked task id is required')),
            new Validators\Required('link_id', t('The link type is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate link modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('task_inverse_id', t('The linked task id is required')),
            new Validators\Required('link_id', t('The link type is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
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
            new Validators\Integer('id', t('The id must be an integer')),
            new Validators\Integer('link_id', t('The related task id must be an integer')),
            new Validators\Integer('task_id', t('The task id must be an integer')),
            new Validators\Integer('task_inverse_id', t('The link id must be an integer')),
        );
    }
}
