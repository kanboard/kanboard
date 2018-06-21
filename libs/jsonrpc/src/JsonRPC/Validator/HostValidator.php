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
        if (!empty($hosts)) {
            foreach ($hosts as $host) {
                if (self::ipMatch($remoteAddress, $host)) {
                    return;
                }
            }
            throw new AccessDeniedException('Access Forbidden');
        }
    }
    
    /**
     * Validate remoteAddress match host
     * @param $remoteAddress
     * @param $host
     * @return bool
     */
    public static function ipMatch($remoteAddress, $host)
    {
        $host = trim($host);
        if (strpos($host, '/') !== false) {
            list($network, $mask) = explode('/', $host);
            if (self::netMatch($remoteAddress, $network, $mask)) {
                return true;
            }
        }

        if ($host === $remoteAddress) {
            return true;
        }

        return false;
    }

    /**
     * validate the ipAddress in network
     *  192.168.1.1/24
     * @param $clientIp
     * @param $networkIp
     * @param $mask
     *
     * @return bool
     */
    public static function netMatch($clientIp, $networkIp, $mask)
    {
        $mask1 = 32 - $mask;
        return ((ip2long($clientIp) >> $mask1) == (ip2long($networkIp) >> $mask1));
    }
}
