<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Class ColumnMoveRestrictionValidator
 *
 * @package Kanboard\Validator
 * @author  Frederic Guillot
 */
class ColumnMoveRestrictionValidator extends BaseValidator
{
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
            new Validators\Required('project_id', t('This field is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('role_id', t('This field is required')),
            new Validators\Integer('role_id', t('This value must be an integer')),
            new Validators\Required('src_column_id', t('This field is required')),
            new Validators\Integer('src_column_id', t('This value must be an integer')),
            new Validators\Required('dst_column_id', t('This field is required')),
            new Validators\Integer('dst_column_id', t('This value must be an integer')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
