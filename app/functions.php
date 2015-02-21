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
function p($value, $t1, $t2)
{
    return $value > 1 ? $t2 : $t1;
}

/**
 * This creates an array of tags out of a comma separated tag names. It removes all special
 * characters, ignores empty values and creates an array of unique values 
 *
 * @todo   Improve this function
 * @return array
 */
function tags_csv2array($data) {
	// Allow only these characters
	$pattern = '/[^a-zA-Z0-9' . '.\-_,' . '\s'.']/u';
	// Remove all special characters, convert to lower case and create array 
	$tmparray = str_getcsv (strtolower(preg_replace($pattern, '', $data)));
	// Ignore if empty, else trim and put in final array
	$tagarray = [];
	foreach ($tmparray as $var)
	{
		$tmp = trim($var);
		if (!empty($tmp)) {
			array_push($tagarray,$tmp);
		}
	}
	// Return only unique values
	return array_unique($tagarray);
}

/**
 * This creates a comma delimited string from a tag array. It does not do any cleaning. It's dumb
 *
 * @todo   Improve this function
 * @return array
 */
function tags_array2csv($array) {
	$data = '';
	foreach ($array as $var)
	{
		$data .= $var.',';
	}
	return $data;
}
