<?php

namespace PicoFeed\Processor;

use Closure;
use PicoFeed\Base;
use PicoFeed\Parser\Feed;
use PicoFeed\Parser\Item;
use PicoFeed\Scraper\Scraper;

/**
 * Scraper Processor
 *
 * @package PicoFeed\Processor
 * @author  Frederic Guillot
 */
class ScraperProcessor extends Base implements ItemProcessorInterface
{
    private $ignoredUrls = array();
    private $scraper;

    /**
     * Callback function for each scraper execution
     *
     * @var Closure
     */
    private $executionCallback;

    /**
     * Add a new execution callback
     *
     * @access public
     * @param  Closure $executionCallback
     * @return $this
     */
    public function setExecutionCallback(Closure $executionCallback)
    {
        $this->executionCallback = $executionCallback;
        return $this;
    }

    /**
     * Execute Item Processor
     *
     * @access public
     * @param  Feed $feed
     * @param  Item $item
     * @return bool
     */
    public function execute(Feed $feed, Item $item)
    {
        if (!in_array($item->getUrl(), $this->ignoredUrls)) {
            $scraper = $this->getScraper();
            $scraper->setUrl($item->getUrl());
            $scraper->execute();

            if ($this->executionCallback && is_callable($this->executionCallback)) {
                call_user_func($this->executionCallback, $feed, $item, $scraper);
            }

            if ($scraper->hasRelevantContent()) {
                $item->setContent($scraper->getFilteredContent());
            }
        }

        return false;
    }

    /**
     * Ignore list of URLs
     *
     * @access public
     * @param  array $urls
     * @return $this
     */
    public function ignoreUrls(array $urls)
    {
        $this->ignoredUrls = $urls;
        return $this;
    }

    /**
     * Returns Scraper instance
     *
     * @access public
     * @return Scraper
     */
    public function getScraper()
    {
        if ($this->scraper === null) {
            $this->scraper = new Scraper($this->config);
        }

        return $this->scraper;
    }
}
