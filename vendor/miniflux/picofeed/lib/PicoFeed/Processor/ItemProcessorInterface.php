<?php

namespace PicoFeed\Processor;

use PicoFeed\Parser\Feed;
use PicoFeed\Parser\Item;

/**
 * Item Processor Interface
 *
 * @package PicoFeed\Processor
 * @author  Frederic Guillot
 */
interface ItemProcessorInterface
{
    /**
     * Execute Item Processor
     *
     * @access public
     * @param  Feed $feed
     * @param  Item $item
     * @return bool
     */
    public function execute(Feed $feed, Item $item);
}
