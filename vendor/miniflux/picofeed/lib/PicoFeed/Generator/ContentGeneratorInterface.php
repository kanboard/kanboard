<?php

namespace PicoFeed\Generator;

use PicoFeed\Parser\Item;

/**
 * Content Generator Interface
 *
 * @package PicoFeed\Generator
 * @author  Frederic Guillot
 */
interface ContentGeneratorInterface
{
    /**
     * Execute Content Generator
     *
     * @access public
     * @param  Item $item
     * @return boolean
     */
    public function execute(Item $item);
}
