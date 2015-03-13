<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Budget
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Budget extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'budget_lines';

    /**
     * Get all budget lines for a project
     *
     * @access public
     * @param  integer   $project_id
     * @return array
     */
    public function getAll($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->desc('date')->findAll();
    }

    /**
     * Get the current total of the budget
     *
     * @access public
     * @param  integer   $project_id
     * @return float
     */
    public function getTotal($project_id)
    {
        $result = $this->db->table(self::TABLE)->columns('SUM(amount) as total')->eq('project_id', $project_id)->findOne();
        return isset($result['total']) ? (float) $result['total'] : 0;
    }

    /**
     * Add a new budget line in the database
     *
     * @access public
     * @param  integer   $project_id
     * @param  float     $amount
     * @param  string    $comment
     * @param  string    $date
     * @return boolean|integer
     */
    public function create($project_id, $amount, $comment, $date = '')
    {
        $values = array(
            'project_id' => $project_id,
            'amount' => $amount,
            'comment' => $comment,
            'date' => $date ?: date('Y-m-d'),
        );

        return $this->persist(self::TABLE, $values);
    }

    /**
     * Remove a specific budget line
     *
     * @access public
     * @param  integer    $budget_id
     * @return boolean
     */
    public function remove($budget_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $budget_id)->remove();
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
            new Validators\Required('project_id', t('Field required')),
            new Validators\Required('amount', t('Field required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}