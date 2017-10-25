<?php

namespace PicoFeed\Syndication;

use DOMElement;

/**
 * Rss20 Item Builder
 *
 * @package PicoFeed\Syndication
 * @author  Frederic Guillot
 */
class Rss20ItemBuilder extends ItemBuilder
{
    /**
     * @var DOMElement
     */
    protected $itemElement;

    /**
     * @var Rss20Helper
     */
    protected $helper;

    /**
     * Build item
     *
     * @access public
     * @return DOMElement
     */
    public function build()
    {
        $this->itemElement = $this->feedBuilder->getDocument()->createElement('item');
        $this->helper = new Rss20Helper($this->feedBuilder->getDocument());

        if (!empty($this->itemId)) {
            $guid = $this->feedBuilder->getDocument()->createElement('guid');
            $guid->setAttribute('isPermaLink', 'false');
            $guid->appendChild($this->feedBuilder->getDocument()->createTextNode($this->itemId));
            $this->itemElement->appendChild($guid);
        } else {
            $guid = $this->feedBuilder->getDocument()->createElement('guid');
            $guid->setAttribute('isPermaLink', 'true');
            $guid->appendChild($this->feedBuilder->getDocument()->createTextNode($this->itemUrl));
            $this->itemElement->appendChild($guid);
        }

        $this->helper
            ->buildTitle($this->itemElement, $this->itemTitle)
            ->buildLink($this->itemElement, $this->itemUrl)
            ->buildDate($this->itemElement, $this->itemPublishedDate)
            ->buildAuthor($this->itemElement, 'author', $this->authorName, $this->authorEmail)
        ;

        if (!empty($this->itemSummary)) {
            $this->helper->buildNode($this->itemElement, 'description', $this->itemSummary);
        }

        if (!empty($this->itemContent)) {
            $node = $this->feedBuilder->getDocument()->createElement('content:encoded');
            $node->appendChild($this->feedBuilder->getDocument()->createCDATASection($this->itemContent));
            $this->itemElement->appendChild($node);
        }

        return $this->itemElement;
    }
}
