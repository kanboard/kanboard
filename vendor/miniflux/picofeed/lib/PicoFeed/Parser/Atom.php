<?php

namespace PicoFeed\Parser;

use SimpleXMLElement;
use PicoFeed\Filter\Filter;
use PicoFeed\Client\Url;

/**
 * Atom parser.
 *
 * @package PicoFeed\Parser
 * @author  Frederic Guillot
 */
class Atom extends Parser
{
    /**
     * Supported namespaces.
     */
    protected $namespaces = array(
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
        return XmlParser::getXPathResult($xml, 'atom:entry', $this->namespaces)
               ?: XmlParser::getXPathResult($xml, 'entry');
    }

    /**
     * Find the feed url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedUrl(SimpleXMLElement $xml, Feed $feed)
    {
        $feed->setFeedUrl($this->getUrl($xml, 'self'));
    }

    /**
     * Find the site url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findSiteUrl(SimpleXMLElement $xml, Feed $feed)
    {
        $feed->setSiteUrl($this->getUrl($xml, 'alternate', true));
    }

    /**
     * Find the feed description.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedDescription(SimpleXMLElement $xml, Feed $feed)
    {
        $description = XmlParser::getXPathResult($xml, 'atom:subtitle', $this->namespaces)
                       ?: XmlParser::getXPathResult($xml, 'subtitle');

        $feed->setDescription(XmlParser::getValue($description));
    }

    /**
     * Find the feed logo url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedLogo(SimpleXMLElement $xml, Feed $feed)
    {
        $logo = XmlParser::getXPathResult($xml, 'atom:logo', $this->namespaces)
                ?: XmlParser::getXPathResult($xml, 'logo');

        $feed->setLogo(XmlParser::getValue($logo));
    }

    /**
     * Find the feed icon.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedIcon(SimpleXMLElement $xml, Feed $feed)
    {
        $icon = XmlParser::getXPathResult($xml, 'atom:icon', $this->namespaces)
                ?: XmlParser::getXPathResult($xml, 'icon');

        $feed->setIcon(XmlParser::getValue($icon));
    }

    /**
     * Find the feed title.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedTitle(SimpleXMLElement $xml, Feed $feed)
    {
        $title = XmlParser::getXPathResult($xml, 'atom:title', $this->namespaces)
                ?: XmlParser::getXPathResult($xml, 'title');

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
        $language = XmlParser::getXPathResult($xml, '*[not(self::atom:entry)]/@xml:lang', $this->namespaces)
                    ?: XmlParser::getXPathResult($xml, '@xml:lang');

        $feed->setLanguage(XmlParser::getValue($language));
    }

    /**
     * Find the feed id.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedId(SimpleXMLElement $xml, Feed $feed)
    {
        $id = XmlParser::getXPathResult($xml, 'atom:id', $this->namespaces)
              ?: XmlParser::getXPathResult($xml, 'id');

        $feed->setId(XmlParser::getValue($id));
    }

    /**
     * Find the feed date.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \PicoFeed\Parser\Feed $feed Feed object
     */
    public function findFeedDate(SimpleXMLElement $xml, Feed $feed)
    {
        $updated = XmlParser::getXPathResult($xml, 'atom:updated', $this->namespaces)
                   ?: XmlParser::getXPathResult($xml, 'updated');

        $feed->setDate($this->getDateParser()->getDateTime(XmlParser::getValue($updated)));
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
        $date = XmlParser::getXPathResult($entry, 'atom:published', $this->namespaces)
            ?: XmlParser::getXPathResult($entry, 'published');

        $item->setPublishedDate(!empty($date) ? $this->getDateParser()->getDateTime((string) current($date)) : null);
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
        $date = XmlParser::getXPathResult($entry, 'atom:updated', $this->namespaces)
            ?: XmlParser::getXPathResult($entry, 'updated');

        $item->setUpdatedDate(!empty($date) ? $this->getDateParser()->getDateTime((string) current($date)) : null);
    }

    /**
     * Find the item title.
     *
     * @param SimpleXMLElement $entry Feed item
     * @param Item             $item  Item object
     */
    public function findItemTitle(SimpleXMLElement $entry, Item $item)
    {
        $title = XmlParser::getXPathResult($entry, 'atom:title', $this->namespaces)
                 ?: XmlParser::getXPathResult($entry, 'title');

        $item->setTitle(Filter::stripWhiteSpace(XmlParser::getValue($title)) ?: $item->getUrl());
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
        $author = XmlParser::getXPathResult($entry, 'atom:author/atom:name', $this->namespaces)
                  ?: XmlParser::getXPathResult($entry, 'author/name')
                  ?: XmlParser::getXPathResult($xml, 'atom:author/atom:name', $this->namespaces)
                  ?: XmlParser::getXPathResult($xml, 'author/name');

        $item->setAuthor(XmlParser::getValue($author));
    }

    /**
     * Find the item content.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \PicoFeed\Parser\Item $item  Item object
     */
    public function findItemContent(SimpleXMLElement $entry, Item $item)
    {
        $item->setContent($this->getContent($entry));
    }

    /**
     * Find the item URL.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \PicoFeed\Parser\Item $item  Item object
     */
    public function findItemUrl(SimpleXMLElement $entry, Item $item)
    {
        $item->setUrl($this->getUrl($entry, 'alternate', true));
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
        $id = XmlParser::getXPathResult($entry, 'atom:id', $this->namespaces)
                  ?: XmlParser::getXPathResult($entry, 'id');

        if (!empty($id)) {
            $item->setId($this->generateId(XmlParser::getValue($id)));
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
        $enclosure = $this->findLink($entry, 'enclosure');

        if ($enclosure) {
            $item->setEnclosureUrl(Url::resolve((string) $enclosure['href'], $feed->getSiteUrl()));
            $item->setEnclosureType((string) $enclosure['type']);
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
        $language = XmlParser::getXPathResult($entry, './/@xml:lang');
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
        $categories = XmlParser::getXPathResult($entry, 'atom:category/@term', $this->namespaces)
                 ?: XmlParser::getXPathResult($entry, 'category/@term');
        $item->setCategoriesFromXml($categories);
    }

    /**
     * Get the URL from a link tag.
     *
     * @param SimpleXMLElement $xml XML tag
     * @param string           $rel Link relationship: alternate, enclosure, related, self, via
     *
     * @return string
     */
    private function getUrl(SimpleXMLElement $xml, $rel, $fallback = false)
    {
        $link = $this->findLink($xml, $rel);

        if ($link) {
            return (string) $link['href'];
        }

        if ($fallback) {
            $link = $this->findLink($xml, '');
            return $link ? (string) $link['href'] : '';
        }

        return '';
    }

    /**
     * Get a link tag that match a relationship.
     *
     * @param SimpleXMLElement $xml XML tag
     * @param string           $rel Link relationship: alternate, enclosure, related, self, via
     *
     * @return SimpleXMLElement|null
     */
    private function findLink(SimpleXMLElement $xml, $rel)
    {
        $links = XmlParser::getXPathResult($xml, 'atom:link', $this->namespaces)
                ?: XmlParser::getXPathResult($xml, 'link');

        foreach ($links as $link) {
            if ($rel === (string) $link['rel']) {
                return $link;
            }
        }

        return null;
    }

    /**
     * Get the entry content.
     *
     * @param SimpleXMLElement $entry XML Entry
     *
     * @return string
     */
    private function getContent(SimpleXMLElement $entry)
    {
        $content = current(
            XmlParser::getXPathResult($entry, 'atom:content', $this->namespaces)
            ?: XmlParser::getXPathResult($entry, 'content')
        );

        if (!empty($content) && count($content->children())) {
            $xml_string = '';

            foreach ($content->children() as $child) {
                $xml_string .= $child->asXML();
            }

            return $xml_string;
        } elseif (trim((string) $content) !== '') {
            return (string) $content;
        }

        $summary = XmlParser::getXPathResult($entry, 'atom:summary', $this->namespaces)
                   ?: XmlParser::getXPathResult($entry, 'summary');

        return (string) current($summary);
    }
}
