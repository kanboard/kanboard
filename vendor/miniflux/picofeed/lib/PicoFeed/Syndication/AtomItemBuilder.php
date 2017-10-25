<?php

namespace PicoFeed\Syndication;

use DOMElement;

/**
 * Atom Item Builder
 *
 * @package PicoFeed\Syndication
 * @author  Frederic Guillot
 */
class AtomItemBuilder extends ItemBuilder
{
    /**
     * @var DOMElement
     */
    protected $itemElement;

    /**
     * @var AtomHelper
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
        $this->itemElement = $this->feedBuilder->getDocument()->createElement('entry');
        $this->helper = new AtomHelper($this->feedBuilder->getDocument());

        if (!empty($this->itemId)) {
            $this->helper->buildId($this->itemElement, $this->itemId);
        } else {
            $this->helper->buildId($this->itemElement, $this->itemUrl);
        }

        $this->helper
            ->buildTitle($this->itemElement, $this->itemTitle)
            ->buildLink($this->itemElement, $this->itemUrl)
            ->buildDate($this->itemElement, $this->itemUpdatedDate, 'updated')
            ->buildDate($this->itemElement, $this->itemPublishedDate, 'published')
            ->buildAuthor($this->itemElement, $this->authorName, $this->authorEmail, $this->authorUrl)
        ;

        if (!empty($this->itemSummary)) {
            $this->helper->buildNode($this->itemElement, 'summary', $this->itemSummary);
        }

        if (!empty($this->itemContent)) {
            $node = $this->feedBuilder->getDocument()->createElement('content');
            $node->setAttribute('type', 'html');
            $node->appendChild($this->feedBuilder->getDocument()->createCDATASection($this->itemContent));
            $this->itemElement->appendChild($node);
        }

        return $this->itemElement;
    }
}
