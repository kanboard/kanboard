<?php

namespace Core;

/**
 * Tool class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Tool
{
    /**
     * Write a CSV file
     *
     * @static
     * @access public
     * @param  array    $rows       Array of rows
     * @param  string   $filename   Output filename
     */
    public static function csv(array $rows, $filename = 'php://output')
    {
        $fp = fopen($filename, 'w');

        if (is_resource($fp)) {

            foreach ($rows as $fields) {
                fputcsv($fp, $fields);
            }

            fclose($fp);
        }
    }

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

        list($local_part,) = explode('@', $email);
        list(,$identifier) = explode('+', $local_part);

        return $identifier;
    }
}
