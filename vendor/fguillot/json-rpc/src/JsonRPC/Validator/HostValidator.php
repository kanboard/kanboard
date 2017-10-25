<?php

namespace JsonRPC\Validator;

use JsonRPC\Exception\AccessDeniedException;

/**
 * Class HostValidator
 *
 * @package JsonRPC\Validator
 * @author  Frederic Guillot
 */
class HostValidator
{
    /**
     * Validate
     *
     * @static
     * @access public
     * @param  array  $hosts
     * @param  string $remoteAddress
     * @throws AccessDeniedException
     */
    public static function validate(array $hosts, $remoteAddress)
    {
        if (! empty($hosts) && ! in_array($remoteAddress, $hosts)) {
            throw new AccessDeniedException('Access Forbidden');
        }
    }
}
