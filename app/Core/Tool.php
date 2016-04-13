<?php

namespace Kanboard\Core;

use Pimple\Container;

/**
 * Tool class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Tool
{
    /**
     * Get the mailbox hash from an email address
     *
     * @static
     * @access public
     * @param  string  $email
     * @return string
     */
    public static function getMailboxHash($email)
    {
        if (! strpos($email, '@') || ! strpos($email, '+')) {
            return '';
        }

        list($local_part, ) = explode('@', $email);
        list(, $identifier) = explode('+', $local_part);

        return $identifier;
    }

    /**
     * Build dependency injection container from an array
     *
     * @static
     * @access public
     * @param  Container  $container
     * @param  array      $namespaces
     * @return Container
     */
    public static function buildDIC(Container $container, array $namespaces)
    {
        foreach ($namespaces as $namespace => $classes) {
            foreach ($classes as $name) {
                $class = '\\Kanboard\\'.$namespace.'\\'.$name;
                $container[lcfirst($name)] = function ($c) use ($class) {
                    return new $class($c);
                };
            }
        }

        return $container;
    }

    /**
     * Build dependency injection container for custom helpers from an array
     *
     * @static
     * @access public
     * @param  Container  $container
     * @param  array      $namespaces
     * @return Container
     */
    public static function buildDICHelpers(Container $container, array $namespaces)
    {
        foreach ($namespaces as $namespace => $classes) {
            foreach ($classes as $name) {
                $class = '\\Kanboard\\'.$namespace.'\\'.$name;
                $container['helper']->register($name, $class);
            }
        }

        return $container;
    }
}
