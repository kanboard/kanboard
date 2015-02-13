<?php

use Core\Translator;

/**
 * Translate a string
 *
 * @return string
 */
function t()
{
    $t = new Translator;
    return call_user_func_array(array($t, 'translate'), func_get_args());
}

/**
 * Translate a string with no HTML escaping
 *
 * @return string
 */
function e()
{
    $t = new Translator;
    return call_user_func_array(array($t, 'translateNoEscaping'), func_get_args());
}

/**
 * Translate a currency
 *
 * @return string
 */
function c($value)
{
    $t = new Translator;
    return $t->currency($value);
}

/**
 * Translate a number
 *
 * @return string
 */
function n($value)
{
    $t = new Translator;
    return $t->number($value);
}

/**
 * Translate a date
 *
 * @return string
 */
function dt($format, $timestamp)
{
    $t = new Translator;
    return $t->datetime($format, $timestamp);
}

/**
 * Handle plurals, return $t2 if $value > 1
 *
 * @todo   Improve this function
 * @return mixed
 */
function p($value, $t1, $t2) {
    return $value > 1 ? $t2 : $t1;
}

/**
 * Get the age of an item in quasi human readable format.
 * It's in this format: <1h , NNh, NNd
 *
 * @access public
 * @param int $time
 *        	Unix timestamp of the artifact for which age will be calculated
 * @param int $currenttime
 *        	Comepare with timestamp. Default current unix timestamp
 * @return string
 */
function getAgeShort($time, $currenttime = NULL) {
	if (! $currenttime) {
		$currenttime = time ();
	}
	$diff = $currenttime - $time;

	if ($diff < 3600)
		return "<1h";
	elseif ($diff < 86400) {
		return intval ( ($diff / 3600) ) . "h";
	}
	return intval ( ((floor ( $currenttime ) - floor ( $time )) / 86400) ) . "d";
}
