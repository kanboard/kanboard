<?php

namespace Kanboard\Subscriber;

use Kanboard\Core\Base;

/**
 * Base class for subscribers
 *
 * @package subscriber
 * @author  Frederic Guillot
 */
class BaseSubscriber extends Base
{
    /**
     * Method called
     *
     * @access private
     * @var array
     */
    private $called = array();

    /**
     * Check if a method has been executed
     *
     * @access public
     * @param  string  $method
     * @return boolean
     */
    public function isExecuted($method = '')
    {
        if (isset($this->called[$method])) {
            return true;
        }

        $this->called[$method] = true;

        return false;
    }
}
