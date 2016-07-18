<?php

namespace Kanboard\EventBuilder;

use Kanboard\Core\Base;
use Kanboard\Event\GenericEvent;

/**
 * Class BaseEventBuilder
 *
 * @package Kanboard\EventBuilder
 * @author  Frederic Guillot
 */
abstract class BaseEventBuilder extends Base
{
    /**
     * Build event data
     *
     * @access public
     * @return GenericEvent|null
     */
    abstract public function build();
}
