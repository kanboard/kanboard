<?php

namespace PicoFeed\Processor;

use PicoFeed\Base;
use PicoFeed\Parser\Feed;
use PicoFeed\Parser\Item;
use PicoFeed\Config\Config;

/**
 * Item Post Processor
 *
 * @package PicoFeed\Processor
 * @author  Frederic Guillot
 */
class ItemPostProcessor extends Base
{
    /**
     * List of processors
     *
     * @access private
     * @var array
     */
    private $processors = array();

    /**
     * Execute all processors
     *
     * @access public
     * @param  Feed  $feed
     * @param  Item  $item
     * @return bool
     */
    public function execute(Feed $feed, Item $item)
    {
        foreach ($this->processors as $processor) {
            if ($processor->execute($feed, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register a new Item post-processor
     *
     * @access public
     * @param  ItemProcessorInterface $processor
     * @return ItemPostProcessor
     */
    public function register(ItemProcessorInterface $processor)
    {
        $this->processors[get_class($processor)] = $processor;
        return $this;
    }

    /**
     * Remove Processor instance
     *
     * @access public
     * @param  string $class
     * @return ItemPostProcessor
     */
    public function unregister($class)
    {
        if (isset($this->processors[$class])) {
            unset($this->processors[$class]);
        }

        return $this;
    }

    /**
     * Checks wheather a specific processor is registered or not
     *
     * @access public
     * @param  string $class
     * @return bool
     */
    public function hasProcessor($class)
    {
        return isset($this->processors[$class]);
    }

    /**
     * Get Processor instance
     *
     * @access public
     * @param  string $class
     * @return ItemProcessorInterface|null
     */
    public function getProcessor($class)
    {
        return isset($this->processors[$class]) ? $this->processors[$class] : null;
    }

    public function setConfig(Config $config)
    {
        foreach ($this->processors as $processor) {
            $processor->setConfig($config);
        }

        return false;
    }
}
