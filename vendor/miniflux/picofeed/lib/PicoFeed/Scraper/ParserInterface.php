<?php

namespace PicoFeed\Scraper;

interface ParserInterface
{
    /**
     * Execute the parser and return the contents.
     *
     * @return string
     */
    public function execute();

    /**
     * Find link for next page of the article.
     *
     * @return string
     */
    public function findNextLink();
}
