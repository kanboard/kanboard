<?php

namespace Core;

/**
 * Event listener interface
 *
 * @package core
 * @author  Frederic Guillot
 */
interface Listener
{
    /**
     * Execute the listener
     *
     * @access public
     * @param  array     $data    Event data
     * @return boolean
     */
    public function execute(array $data);
}
