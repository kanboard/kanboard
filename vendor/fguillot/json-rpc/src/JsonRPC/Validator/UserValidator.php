<?php

namespace JsonRPC\Validator;

use JsonRPC\Exception\AuthenticationFailureException;

/**
 * Class UserValidator
 *
 * @package JsonRPC\Validator
 * @author  Frederic Guillot
 */
class UserValidator
{
    public static function validate(array $users, $username, $password)
    {
        if (! empty($users) && (! isset($users[$username]) || $users[$username] !== $password)) {
            throw new AuthenticationFailureException('Access not allowed');
        }
    }
}
