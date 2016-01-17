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
     * Check if a listener has been executed
     *
     * @access public
     * @param  string  $key
     * @return boolean
     */
    public function isExecuted($key = '')
    {
        if (isset($this->called[$key])) {
            return true;
        }

        $this->called[$key] = true;

        return false;
    }
}
