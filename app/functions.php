<?php

use Kanboard\Core\Translator;

/**
 * Associate another dict to a dict based on a common key
 *
 * @param array  $input
 * @param array  $relations
 * @param string $relation
 * @param string $column
 */
function array_merge_relation(array &$input, array &$relations, $relation, $column)
{
    foreach ($input as &$row) {
        if (isset($row[$column]) && isset($relations[$row[$column]])) {
            $row[$relation] = $relations[$row[$column]];
        } else {
            $row[$relation] = array();
        }
    }
}

/**
 * Create indexed array from a list of dict
 *
 * $input = [
 *   ['k1' => 1, 'k2' => 2], ['k1' => 3, 'k2' => 4], ['k1' => 1, 'k2' => 5]
 * ]
 *
 * array_column_index($input, 'k1') will returns:
 *
 * [
 *   1 => [['k1' => 1, 'k2' => 2], ['k1' => 1, 'k2' => 5]],
 *   3 => [['k1' => 3, 'k2' => 4]],
 * ]
 *
 * @param  array   $input
 * @param  string  $column
 * @return array
 */
function array_column_index(array &$input, $column)
{
    $result = array();

    foreach ($input as &$row) {
        if (isset($row[$column])) {
            $result[$row[$column]][] = $row;
        }
    }

    return $result;
}

/**
 * Create indexed array from a list of dict with unique values
 *
 * $input = [
 *   ['k1' => 1, 'k2' => 2], ['k1' => 3, 'k2' => 4], ['k1' => 1, 'k2' => 5]
 * ]
 *
 * array_column_index_unique($input, 'k1') will returns:
 *
 * [
 *   1 => ['k1' => 1, 'k2' => 2],
 *   3 => ['k1' => 3, 'k2' => 4],
 * ]
 *
 * @param  array   $input
 * @param  string  $column
 * @return array
 */
function array_column_index_unique(array &$input, $column)
{
    $result = array();

    foreach ($input as &$row) {
        if (isset($row[$column]) && ! isset($result[$row[$column]])) {
            $result[$row[$column]] = $row;
        }
    }

    return $result;
}

/**
 * Sum all values from a single column in the input array
 *
 * $input = [
 *   ['column' => 2], ['column' => 3]
 * ]
 *
 * array_column_sum($input, 'column') returns 5
 *
 * @param  array   $input
 * @param  string  $column
 * @return double
 */
function array_column_sum(array &$input, $column)
{
    $sum = 0.0;

    foreach ($input as &$row) {
        if (isset($row[$column])) {
            $sum += (float) $row[$column];
        }
    }

    return $sum;
}

/**
 * Build version number from git-archive output
 *
 * @param  string $ref
 * @param  string $commit_hash
 * @return string
 */
function build_app_version($ref, $commit_hash)
{
    $version = 'master';

    if ($ref !== '$Format:%d$') {
        $tag = preg_replace('/\s*\(.*tag:\sv([^,]+).*\)/i', '\1', $ref);

        if (!is_null($tag) && $tag !== $ref) {
            return $tag;
        }
    }

    if ($commit_hash !== '$Format:%H$') {
        $version .= '.'.$commit_hash;
    }

    return $version;
}

/**
 * Get upload max size.
 *
 * @return string
 */
function get_upload_max_size()
{
    return min(ini_get('upload_max_filesize'), ini_get('post_max_size'));
}

/**
 * Get file extension
 *
 * @param  $filename
 * @return string
 */
function get_file_extension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

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
 * @param  mixed $value
 * @return string
 */
function n($value)
{
    return Translator::getInstance()->number($value);
}
