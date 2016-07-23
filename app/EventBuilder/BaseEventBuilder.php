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
    abstract public function buildEvent();

    /**
     * Get event title with author
     *
     * @access public
     * @param  string $author
     * @param  string $eventName
     * @param  array  $eventData
     * @return string
     */
    abstract public function buildTitleWithAuthor($author, $eventName, array $eventData);

    /**
     * Get event title without author
     *
     * @access public
     * @param  string $eventName
     * @param  array  $eventData
     * @return string
     */
    abstract public function buildTitleWithoutAuthor($eventName, array $eventData);
}
