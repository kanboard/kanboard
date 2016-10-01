<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Kanboard\Model\TaskModel;

/**
 * Task Link Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class TaskLinkValidator extends BaseValidator
{
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
            new Validators\NotEquals('opposite_task_id', 'task_id', t('A task cannot be linked to itself')),
            new Validators\Exists('opposite_task_id', t('This linked task id doesn\'t exists'), $this->db->getConnection(), TaskModel::TABLE, 'id')
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
