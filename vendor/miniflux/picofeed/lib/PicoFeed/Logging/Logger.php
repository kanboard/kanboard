<?php

namespace PicoFeed\Logging;

use DateTime;
use DateTimeZone;

/**
 * Logging class.
 *
 * @author  Frederic Guillot
 */
class Logger
{
    /**
     * List of messages.
     *
     * @static
     *
     * @var array
     */
    private static $messages = array();

    /**
     * Default timezone.
     *
     * @static
     *
     * @var string
     */
    private static $timezone = 'UTC';

    /**
     * Enable or disable logging.
     *
     * @static
     *
     * @var bool
     */
    public static $enable = false;

    /**
     * Enable logging.
     *
     * @static
     */
    public static function enable()
    {
        self::$enable = true;
    }

    /**
     * Add a new message.
     *
     * @static
     *
     * @param string $message Message
     */
    public static function setMessage($message)
    {
        if (self::$enable) {
            $date = new DateTime('now', new DateTimeZone(self::$timezone));
            self::$messages[] = '['.$date->format('Y-m-d H:i:s').'] '.$message;
        }
    }

    /**
     * Get all logged messages.
     *
     * @static
     *
     * @return array
     */
    public static function getMessages()
    {
        return self::$messages;
    }

    /**
     * Remove all logged messages.
     *
     * @static
     */
    public static function deleteMessages()
    {
        self::$messages = array();
    }

    /**
     * Set a different timezone.
     *
     * @static
     *
     * @see    http://php.net/manual/en/timezones.php
     *
     * @param string $timezone Timezone
     */
    public static function setTimeZone($timezone)
    {
        self::$timezone = $timezone ?: self::$timezone;
    }

    /**
     * Get all messages serialized into a string.
     *
     * @static
     *
     * @return string
     */
    public static function toString()
    {
        return implode(PHP_EOL, self::$messages).PHP_EOL;
    }
}
