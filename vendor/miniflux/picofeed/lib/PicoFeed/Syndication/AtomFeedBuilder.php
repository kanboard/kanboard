<?php

namespace PicoFeed\Syndication;

use DOMAttr;
use DOMElement;

/**
 * Atom Feed Builder
 *
 * @package PicoFeed\Syndication
 * @author  Frederic Guillot
 */
class AtomFeedBuilder extends FeedBuilder
{
    /**
     * @var DOMElement
     */
    protected $feedElement;

    /**
     * @var AtomHelper
     */
    protected $helper;

    /**
     * Build feed
     *
     * @access public
     * @param  string $filename
     * @return string
     */
    public function build($filename = '')
    {
        $this->helper = new AtomHelper($this->getDocument());

        $this->feedElement = $this->getDocument()->createElement('feed');
        $this->feedElement->setAttributeNodeNS(new DomAttr('xmlns', 'http://www.w3.org/2005/Atom'));

        $generator = $this->getDocument()->createElement('generator', 'PicoFeed');
        $generator->setAttribute('uri', 'https://github.com/miniflux/picoFeed');
        $this->feedElement->appendChild($generator);

        $this->helper
            ->buildTitle($this->feedElement, $this->feedTitle)
            ->buildId($this->feedElement, $this->feedUrl)
            ->buildDate($this->feedElement, $this->feedDate)
            ->buildLink($this->feedElement, $this->siteUrl)
            ->buildLink($this->feedElement, $this->feedUrl, 'self', 'application/atom+xml')
            ->buildAuthor($this->feedElement, $this->authorName, $this->authorEmail, $this->authorUrl)
        ;

        foreach ($this->items as $item) {
            $this->feedElement->appendChild($item->build());
        }

        $this->getDocument()->appendChild($this->feedElement);

        if ($filename !== '') {
            $this->getDocument()->save($filename);
        }

        return $this->getDocument()->saveXML();
    }
}
