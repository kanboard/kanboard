<?php

namespace Kanboard\Validator;

/**
 * Base Validator
 *
 * @package  validator
 * @author   Frederic Guillot
 */
class Base extends \Kanboard\Core\Base
{
    /**
     * Execute multiple validators
     *
     * @access public
     * @param  array  $validators       List of validators
     * @param  array  $values           Form values
     * @return array  $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function executeValidators(array $validators, array $values)
    {
        $result = false;
        $errors = array();

        foreach ($validators as $method) {
            list($result, $errors) = $this->$method($values);

            if (! $result) {
                break;
            }
        }

        return array($result, $errors);
    }
}
