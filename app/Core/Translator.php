<?php

namespace Core;

/**
 * Translator class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Translator
{
    /**
     * Locales path
     *
     * @var string
     */
    const PATH = 'app/Locales/';

    /**
     * Locales
     *
     * @static
     * @access private
     * @var array
     */
    private static $locales = array();

    /**
     * Flag to enable HTML escaping
     *
     * @static
     * @access private
     * @var boolean
     */
    private static $enable_escaping = true;

    /**
     * Disable HTML escaping for translations
     *
     * @static
     * @access public
     */
    public static function disableEscaping()
    {
        self::$enable_escaping = false;
    }

    /**
     * Enable HTML escaping for translations
     *
     * @static
     * @access public
     */
    public static function enableEscaping()
    {
        self::$enable_escaping = true;
    }

    /**
     * Get a translation
     *
     * $translator->translate('I have %d kids', 5);
     *
     * @access public
     * @param $identifier
     * @return string
     */
    public function translate($identifier)
    {
        $args = func_get_args();

        array_shift($args);
        array_unshift($args, $this->get($identifier, $identifier));

        if (self::$enable_escaping) {
            foreach ($args as &$arg) {
                $arg = htmlspecialchars($arg, ENT_QUOTES, 'UTF-8', false);
            }
        }

        return call_user_func_array(
            'sprintf',
            $args
        );
    }

    /**
     * Get a formatted number
     *
     * $translator->number(1234.56);
     *
     * @access public
     * @param  float    $number   Number to format
     * @return string
     */
    public function number($number)
    {
        return number_format(
            $number,
            $this->get('number.decimals', 2),
            $this->get('number.decimals_separator', '.'),
            $this->get('number.thousands_separator', ',')
        );
    }

    /**
     * Get a formatted currency number
     *
     * $translator->currency(1234.56);
     *
     * @access public
     * @param  float    $amount   Number to format
     * @return string
     */
    public function currency($amount)
    {
        $position = $this->get('currency.position', 'before');
        $symbol = $this->get('currency.symbol', '$');
        $str = '';

        if ($position === 'before') {
            $str .= $symbol;
        }

        $str .= $this->number($amount);

        if ($position === 'after') {
            $str .= ' '.$symbol;
        }

        return $str;
    }

    /**
     * Get a formatted datetime
     *
     * $translator->datetime('%Y-%m-%d', time());
     *
     * @access public
     * @param  string   $format      Format defined by the strftime function
     * @param  integer  $timestamp   Unix timestamp
     * @return string
     */
    public function datetime($format, $timestamp)
    {
        if (! $timestamp) {
            return '';
        }

        $format = $this->get($format, $format);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $format = str_replace('%e', '%d', $format);
            $format = str_replace('%k', '%H', $format);
        }

        return strftime($format, (int) $timestamp);
    }

    /**
     * Get an identifier from the translations or return the default
     *
     * @access public
     * @param  string   $identifier   Locale identifier
     * @param  string   $default      Default value
     * @return string
     */
    public function get($identifier, $default = '')
    {
        if (isset(self::$locales[$identifier])) {
            return self::$locales[$identifier];
        }
        else {
            return $default;
        }
    }

    /**
     * Load translations
     *
     * @static
     * @access public
     * @param  string   $language   Locale code: fr_FR
     */
    public static function load($language)
    {
        setlocale(LC_TIME, $language.'.UTF-8', $language);

        $filename = self::PATH.$language.DIRECTORY_SEPARATOR.'translations.php';

        if (file_exists($filename)) {
            self::$locales = require $filename;
        }
    }
}
