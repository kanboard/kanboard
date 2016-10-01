<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Kanboard\Model\LinkModel;

/**
 * Link Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class LinkValidator extends BaseValidator
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
            new Validators\Required('label', t('Field required')),
            new Validators\Unique('label', t('This label must be unique'), $this->db->getConnection(), LinkModel::TABLE),
            new Validators\NotEquals('label', 'opposite_label', t('The labels must be different')),
        ));

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
        $v = new Validator($values, array(
            new Validators\Required('id', t('Field required')),
            new Validators\Required('opposite_id', t('Field required')),
            new Validators\Required('label', t('Field required')),
            new Validators\Unique('label', t('This label must be unique'), $this->db->getConnection(), LinkModel::TABLE),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
