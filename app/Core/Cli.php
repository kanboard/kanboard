<?php

namespace Core;

use Closure;

/**
 * CLI class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Cli
{
    /**
     * Default command name
     *
     * @access public
     * @var    string
     */
    public $default_command = 'help';

    /**
     * List of registered commands
     *
     * @access private
     * @var    array
     */
    private $commands = array();

    /**
     *
     *
     * @access public
     * @param  string    $command      Command name
     * @param  Closure   $callback     Command callback
     */
    public function register($command, Closure $callback)
    {
        $this->commands[$command] = $callback;
    }

    /**
     * Execute a command
     *
     * @access public
     * @param  string   $command   Command name
     */
    public function call($command)
    {
        if (isset($this->commands[$command])) {
            $this->commands[$command]();
            exit;
        }
    }

    /**
     * Determine which command to execute
     *
     * @access public
     */
    public function execute()
    {
        if (php_sapi_name() !== 'cli') {
            die('This script work only from the command line.');
        }

        if ($GLOBALS['argc'] === 1) {
            $this->call($this->default_command);
        }

        $this->call($GLOBALS['argv'][1]);
        $this->call($this->default_command);
    }
}
