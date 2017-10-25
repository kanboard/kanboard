<?php

namespace PicoFeed\Syndication;

use DOMAttr;
use DOMElement;

/**
 * Rss20 Feed Builder
 *
 * @package PicoFeed\Syndication
 * @author  Frederic Guillot
 */
class Rss20FeedBuilder extends FeedBuilder
{
    /**
     * @var DOMElement
     */
    protected $rssElement;

    /**
     * @var Rss20Helper
     */
    protected $helper;

    /**
     * @var DOMElement
     */
    protected $channelElement;

    /**
     * Build feed
     *
     * @access public
     * @param  string $filename
     * @return string
     */
    public function build($filename = '')
    {
        $this->helper = new Rss20Helper($this->getDocument());

        $this->rssElement = $this->getDocument()->createElement('rss');
        $this->rssElement->setAttribute('version', '2.0');
        $this->rssElement->setAttributeNodeNS(new DomAttr('xmlns:content', 'http://purl.org/rss/1.0/modules/content/'));
        $this->rssElement->setAttributeNodeNS(new DomAttr('xmlns:atom', 'http://www.w3.org/2005/Atom'));

        $this->channelElement = $this->getDocument()->createElement('channel');
        $this->helper
            ->buildNode($this->channelElement, 'generator', 'PicoFeed (https://github.com/miniflux/picoFeed)')
            ->buildTitle($this->channelElement, $this->feedTitle)
            ->buildNode($this->channelElement, 'description', $this->feedTitle)
            ->buildDate($this->channelElement, $this->feedDate)
            ->buildAuthor($this->channelElement, 'webMaster', $this->authorName, $this->authorEmail)
            ->buildLink($this->channelElement, $this->siteUrl)
        ;

        $link = $this->getDocument()->createElement('atom:link');
        $link->setAttribute('href', $this->feedUrl);
        $link->setAttribute('rel', 'self');
        $link->setAttribute('type', 'application/rss+xml');
        $this->channelElement->appendChild($link);

        foreach ($this->items as $item) {
            $this->channelElement->appendChild($item->build());
        }

        $this->rssElement->appendChild($this->channelElement);
        $this->getDocument()->appendChild($this->rssElement);

        if ($filename !== '') {
            $this->getDocument()->save($filename);
        }

        return $this->getDocument()->saveXML();
    }
}
