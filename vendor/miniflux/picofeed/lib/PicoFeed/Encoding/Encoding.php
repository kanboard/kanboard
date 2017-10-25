<?php

namespace PicoFeed\Encoding;

/**
 * Encoding class.
 */
class Encoding
{
    public static function convert($input, $encoding)
    {
        if ($encoding === 'utf-8' || $encoding === '') {
            return $input;
        }

        // suppress all notices since it isn't possible to silence only the
        // notice "Wrong charset, conversion from $in_encoding to $out_encoding is not allowed"
        set_error_handler(function () {}, E_NOTICE);

        // convert input to utf-8 and strip invalid characters
        $value = iconv($encoding, 'UTF-8//IGNORE', $input);

        // stop silencing of notices
        restore_error_handler();

        // return input if something went wrong, maybe it's usable anyway
        if ($value === false) {
            return $input;
        }

        return $value;
    }
}
