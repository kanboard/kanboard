<?php

namespace PicoFeed\Parser;

use SimpleXMLElement;
use PicoFeed\Filter\Filter;
use PicoFeed\Client\Url;

/**
 * RSS 2.0 Parser.
 *
 * @package PicoFeed\Parser
 * @author  Frederic Guillot
 */
class Rss20 extends Parser
{
    /**
     * Supported namespaces.
     */
    protected $namespaces = array(
        'dc' => 'http://purl.org/dc/elements/1.1/',
        'content' => 'http://purl.org/rss/1.0/modules/content/',
        'feedburner' => 'http://rssnamespace.org/feedburner/ext/1.0',
        'atom' => 'http://www.w3.org/2005/Atom',
    );

    /**
     * Get the path to the items XML tree.
     *
     * @param SimpleXMLElement $xml Feed xml
     *
     * @return SimpleXMLElement
     */
    public function getItemsTree(SimpleXMLElement $xml)
    {
        return XmlParser::getXPathResult($xml, 'channel/item');
    }

    /**
     * Find the feed url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedUrl(SimpleXMLElement $xml, Feed $feed)
    {
        $feed->setFeedUrl('');
    }

    /**
     * Find the site url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findSiteUrl(SimpleXMLElement $xml, Feed $feed)
    {
        $value = XmlParser::getXPathResult($xml, 'channel/link');
        $feed->setSiteUrl(XmlParser::getValue($value));
    }

    /**
     * Find the feed description.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedDescription(SimpleXMLElement $xml, Feed $feed)
    {
        $value = XmlParser::getXPathResult($xml, 'channel/description');
        $feed->setDescription(XmlParser::getValue($value));
    }

    /**
     * Find the feed logo url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedLogo(SimpleXMLElement $xml, Feed $feed)
    {
        $value = XmlParser::getXPathResult($xml, 'channel/image/url');
        $feed->setLogo(XmlParser::getValue($value));
    }

    /**
     * Find the feed icon.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedIcon(SimpleXMLElement $xml, Feed $feed)
    {
        $feed->setIcon('');
    }

    /**
     * Find the feed title.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedTitle(SimpleXMLElement $xml, Feed $feed)
    {
        $title = XmlParser::getXPathResult($xml, 'channel/title');
        $feed->setTitle(Filter::stripWhiteSpace(XmlParser::getValue($title)) ?: $feed->getSiteUrl());
    }

    /**
     * Find the feed language.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedLanguage(SimpleXMLElement $xml, Feed $feed)
    {
        $value = XmlParser::getXPathResult($xml, 'channel/language');
        $feed->setLanguage(XmlParser::getValue($value));
    }

    /**
     * Find the feed id.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedId(SimpleXMLElement $xml, Feed $feed)
    {
        $feed->setId($feed->getFeedUrl() ?: $feed->getSiteUrl());
    }

    /**
     * Find the feed date.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedDate(SimpleXMLElement $xml, Feed $feed)
    {
        $publish_date = XmlParser::getXPathResult($xml, 'channel/pubDate');
        $update_date = XmlParser::getXPathResult($xml, 'channel/lastBuildDate');

        $published = !empty($publish_date) ? $this->getDateParser()->getDateTime(XmlParser::getValue($publish_date)) : null;
        $updated = !empty($update_date) ? $this->getDateParser()->getDateTime(XmlParser::getValue($update_date)) : null;

        if ($published === null && $updated === null) {
            $feed->setDate($this->getDateParser()->getCurrentDateTime()); // We use the current date if there is no date for the feed
        } elseif ($published !== null && $updated !== null) {
            $feed->setDate(max($published, $updated)); // We use the most recent date between published and updated
        } else {
            $feed->setDate($updated ?: $published);
        }
    }

    /**
     * Find the item published date.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item                  $item  Item object
     * @param \PicoFeed\Parser\Feed $feed  Feed object
     */
    public function findItemPublishedDate(SimpleXMLElement $entry, Item $item, Feed $feed)
    {
        $date = XmlParser::getXPathResult($entry, 'pubDate');

        $item->setPublishedDate(!empty($date) ? $this->getDateParser()->getDateTime(XmlParser::getValue($date)) : null);
    }

    /**
     * Find the item updated date.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item                  $item  Item object
     * @param \PicoFeed\Parser\Feed $feed  Feed object
     */
    public function findItemUpdatedDate(SimpleXMLElement $entry, Item $item, Feed $feed)
    {
        if ($item->publishedDate === null) {
            $this->findItemPublishedDate($entry, $item, $feed);
        }
        $item->setUpdatedDate($item->getPublishedDate()); // No updated date in RSS 2.0 specifications
    }

    /**
     * Find the item title.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \PicoFeed\Parser\Item $item  Item object
     */
    public function findItemTitle(SimpleXMLElement $entry, Item $item)
    {
        $value = XmlParser::getXPathResult($entry, 'title');
        $item->setTitle(Filter::stripWhiteSpace(XmlParser::getValue($value)) ?: $item->getUrl());
    }

    /**
     * Find the item author.
     *
     * @param SimpleXMLElement      $xml   Feed
     * @param SimpleXMLElement      $entry Feed item
     * @param \PicoFeed\Parser\Item $item  Item object
     */
    public function findItemAuthor(SimpleXMLElement $xml, SimpleXMLElement $entry, Item $item)
    {
        $value = XmlParser::getXPathResult($entry, 'dc:creator', $this->namespaces)
                  ?: XmlParser::getXPathResult($entry, 'author')
                  ?: XmlParser::getXPathResult($xml, 'channel/dc:creator', $this->namespaces)
                  ?: XmlParser::getXPathResult($xml, 'channel/managingEditor');

        $item->setAuthor(XmlParser::getValue($value));
    }

    /**
     * Find the item content.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \PicoFeed\Parser\Item $item  Item object
     */
    public function findItemContent(SimpleXMLElement $entry, Item $item)
    {
        $content = XmlParser::getXPathResult($entry, 'content:encoded', $this->namespaces);

        if (XmlParser::getValue($content) === '') {
            $content = XmlParser::getXPathResult($entry, 'description');
        }

        $item->setContent(XmlParser::getValue($content));
    }

    /**
     * Find the item URL.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \PicoFeed\Parser\Item $item  Item object
     */
    public function findItemUrl(SimpleXMLElement $entry, Item $item)
    {
        $link = XmlParser::getXPathResult($entry, 'feedburner:origLink', $this->namespaces)
                 ?: XmlParser::getXPathResult($entry, 'link')
                 ?: XmlParser::getXPathResult($entry, 'atom:link/@href', $this->namespaces);

        if (!empty($link)) {
            $item->setUrl(XmlParser::getValue($link));
        } else {
            $link = XmlParser::getXPathResult($entry, 'guid');
            $link = XmlParser::getValue($link);

            if (filter_var($link, FILTER_VALIDATE_URL) !== false) {
                $item->setUrl($link);
            }
        }
    }

    /**
     * Genereate the item id.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \PicoFeed\Parser\Item $item  Item object
     * @param \PicoFeed\Parser\Feed $feed  Feed object
     */
    public function findItemId(SimpleXMLElement $entry, Item $item, Feed $feed)
    {
        $id = XmlParser::getValue(XmlParser::getXPathResult($entry, 'guid'));

        if ($id) {
            $item->setId($this->generateId($id));
        } else {
            $item->setId($this->generateId(
                $item->getTitle(), $item->getUrl(), $item->getContent()
            ));
        }
    }

    /**
     * Find the item enclosure.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \PicoFeed\Parser\Item $item  Item object
     * @param \PicoFeed\Parser\Feed $feed  Feed object
     */
    public function findItemEnclosure(SimpleXMLElement $entry, Item $item, Feed $feed)
    {
        if (isset($entry->enclosure)) {
            $type = XmlParser::getXPathResult($entry, 'enclosure/@type');
            $url = XmlParser::getXPathResult($entry, 'feedburner:origEnclosureLink', $this->namespaces)
                ?: XmlParser::getXPathResult($entry, 'enclosure/@url');

            $item->setEnclosureUrl(Url::resolve(XmlParser::getValue($url), $feed->getSiteUrl()));
            $item->setEnclosureType(XmlParser::getValue($type));
        }
    }

    /**
     * Find the item language.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \PicoFeed\Parser\Item $item  Item object
     * @param \PicoFeed\Parser\Feed $feed  Feed object
     */
    public function findItemLanguage(SimpleXMLElement $entry, Item $item, Feed $feed)
    {
        $language = XmlParser::getXPathResult($entry, 'dc:language', $this->namespaces);
        $item->setLanguage(XmlParser::getValue($language) ?: $feed->getLanguage());
    }

    /**
     * Find the item categories.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item $item  Item object
     * @param Feed $feed  Feed object
     */
    public function findItemCategories(SimpleXMLElement $entry, Item $item, Feed $feed)
    {
        $categories = XmlParser::getXPathResult($entry, 'category');
        $item->setCategoriesFromXml($categories);
    }

}
