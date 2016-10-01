<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Currency Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class CurrencyValidator extends BaseValidator
{
    /**
     * Validate
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('currency', t('Field required')),
            new Validators\Required('rate', t('Field required')),
            new Validators\Numeric('rate', t('This value must be numeric')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
