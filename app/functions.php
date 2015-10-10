<?php

use Core\Translator;

/**
 * Translate a string
 *
 * @return string
 */
function t()
{
    return call_user_func_array(array(Translator::getInstance(), 'translate'), func_get_args());
}

/**
 * Translate a string with no HTML escaping
 *
 * @return string
 */
function e()
{
    return call_user_func_array(array(Translator::getInstance(), 'translateNoEscaping'), func_get_args());
}

/**
 * Translate a number
 *
 * @return string
 */
function n($value)
{
    return Translator::getInstance()->number($value);
}

/**
 * Translate a date
 *
 * @return string
 */
function dt($format, $timestamp)
{
    return Translator::getInstance()->datetime($format, $timestamp);
}

/**
 * Handle plurals, return $t2 if $value > 1
 *
 * @todo   Improve this function
 * @return mixed
 */
function p($value, $t1, $t2)
{
    return $value > 1 ? $t2 : $t1;
}
