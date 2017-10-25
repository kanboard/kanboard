<?php

namespace PicoFeed\Parser;

use SimpleXMLElement;

/**
 * Interface ParserInterface
 *
 * @package PicoFeed\Parser
 * @author  Frederic Guillot
 */
interface ParserInterface
{
    /**
     * Find the feed url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param Feed $feed Feed object
     */
    public function findFeedUrl(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the site url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param Feed $feed Feed object
     */
    public function findSiteUrl(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed title.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param Feed $feed Feed object
     */
    public function findFeedTitle(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed description.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param Feed $feed Feed object
     */
    public function findFeedDescription(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed language.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param Feed $feed Feed object
     */
    public function findFeedLanguage(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed id.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param Feed $feed Feed object
     */
    public function findFeedId(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed date.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param Feed $feed Feed object
     */
    public function findFeedDate(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed logo url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param Feed $feed Feed object
     */
    public function findFeedLogo(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed icon.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param Feed $feed Feed object
     */
    public function findFeedIcon(SimpleXMLElement $xml, Feed $feed);

    /**
     * Get the path to the items XML tree.
     *
     * @param SimpleXMLElement $xml Feed xml
     *
     * @return SimpleXMLElement
     */
    public function getItemsTree(SimpleXMLElement $xml);

    /**
     * Find the item author.
     *
     * @param SimpleXMLElement      $xml   Feed
     * @param SimpleXMLElement      $entry Feed item
     * @param Item $item  Item object
     */
    public function findItemAuthor(SimpleXMLElement $xml, SimpleXMLElement $entry, Item $item);

    /**
     * Find the item URL.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item $item  Item object
     */
    public function findItemUrl(SimpleXMLElement $entry, Item $item);

    /**
     * Find the item title.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item $item  Item object
     */
    public function findItemTitle(SimpleXMLElement $entry, Item $item);

    /**
     * Genereate the item id.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item $item  Item object
     * @param Feed $feed  Feed object
     */
    public function findItemId(SimpleXMLElement $entry, Item $item, Feed $feed);

    /**
     * Find the item published date.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item                  $item  Item object
     * @param Feed $feed  Feed object
     */
    public function findItemPublishedDate(SimpleXMLElement $entry, Item $item, Feed $feed);

    /**
     * Find the item updated date.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item                  $item  Item object
     * @param Feed $feed  Feed object
     */
    public function findItemUpdatedDate(SimpleXMLElement $entry, Item $item, Feed $feed);

    /**
     * Find the item content.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item $item  Item object
     */
    public function findItemContent(SimpleXMLElement $entry, Item $item);

    /**
     * Find the item enclosure.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item $item  Item object
     * @param Feed $feed  Feed object
     */
    public function findItemEnclosure(SimpleXMLElement $entry, Item $item, Feed $feed);

    /**
     * Find the item language.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item $item  Item object
     * @param Feed $feed  Feed object
     */
    public function findItemLanguage(SimpleXMLElement $entry, Item $item, Feed $feed);

    /**
     * Find the item categories.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item $item  Item object
     * @param Feed $feed  Feed object
     */
    public function findItemCategories(SimpleXMLElement $entry, Item $item, Feed $feed);
}
