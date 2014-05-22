<?php

namespace Core;

/**
 * Event listener interface
 *
 * @package core
 * @author  Frederic Guillot
 */
interface Listener {

    /**
     * @return boolean
     */
    public function execute(array $data);
}
