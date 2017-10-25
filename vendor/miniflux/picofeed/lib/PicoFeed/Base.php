<?php

namespace PicoFeed;

use PicoFeed\Config\Config;
use PicoFeed\Logging\Logger;

/**
 * Base class
 *
 * @package PicoFeed
 * @author  Frederic Guillot
 */
abstract class Base
{
    /**
     * Config class instance
     *
     * @access protected
     * @var \PicoFeed\Config\Config
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \PicoFeed\Config\Config   $config   Config class instance
     */
    public function __construct(Config $config = null)
    {
        $this->config = $config ?: new Config();
        Logger::setTimezone($this->config->getTimezone());
    }

    public function setConfig(Config $config) {
        $this->config = $config;
    }
}
