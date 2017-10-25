<?php

namespace PicoFeed\Syndication;

use DateTime;
use DOMDocument;

/**
 * Class FeedBuilder
 *
 * @package PicoFeed\Syndication
 * @author  Frederic Guillot
 */
abstract class FeedBuilder
{
    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * @var string
     */
    protected $feedTitle;

    /**
     * @var string
     */
    protected $feedUrl;

    /**
     * @var string
     */
    protected $siteUrl;

    /**
     * @var string
     */
    protected $authorName;

    /**
     * @var string
     */
    protected $authorEmail;

    /**
     * @var string
     */
    protected $authorUrl;

    /**
     * @var DateTime
     */
    protected $feedDate;

    /**
     * @var ItemBuilder[]
     */
    protected $items = array();

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->document = new DomDocument('1.0', 'UTF-8');
        $this->document->formatOutput = true;
    }

    /**
     * Get new object instance
     *
     * @access public
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Add feed title
     *
     * @access public
     * @param  string $title
     * @return $this
     */
    public function withTitle($title)
    {
        $this->feedTitle = $title;
        return $this;
    }

    /**
     * Add feed url
     *
     * @access public
     * @param  string $url
     * @return $this
     */
    public function withFeedUrl($url)
    {
        $this->feedUrl = $url;
        return $this;
    }

    /**
     * Add website url
     *
     * @access public
     * @param  string $url
     * @return $this
     */
    public function withSiteUrl($url)
    {
        $this->siteUrl = $url;
        return $this;
    }

    /**
     * Add feed date
     *
     * @access public
     * @param  DateTime $date
     * @return $this
     */
    public function withDate(DateTime $date)
    {
        $this->feedDate = $date;
        return $this;
    }

    /**
     * Add feed author
     *
     * @access public
     * @param  string  $name
     * @param  string  $email
     * @param  string  $url
     * @return $this
     */
    public function withAuthor($name, $email = '', $url ='')
    {
        $this->authorName = $name;
        $this->authorEmail = $email;
        $this->authorUrl = $url;
        return $this;
    }

    /**
     * Add feed item
     *
     * @access public
     * @param  ItemBuilder $item
     * @return $this
     */
    public function withItem(ItemBuilder $item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Get DOM document
     *
     * @access public
     * @return DOMDocument
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Build feed
     *
     * @abstract
     * @access public
     * @param  string $filename
     * @return string
     */
    abstract public function build($filename = '');
}
