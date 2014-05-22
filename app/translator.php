<?php

use Core\Translator;

// Get a translation
function t()
{
    $t = new Translator;
    return call_user_func_array(array($t, 'translate'), func_get_args());
}

// Get a locale currency
function c($value)
{
    $t = new Translator;
    return $t->currency($value);
}

// Get a formatted number
function n($value)
{
    $t = new Translator;
    return $t->number($value);
}

// Get a locale date
function dt($format, $timestamp)
{
    $t = new Translator;
    return $t->datetime($format, $timestamp);
}

// Plurals, return $t2 if $value > 1
function p($value, $t1, $t2) {
    return $value > 1 ? $t2 : $t1;
}
