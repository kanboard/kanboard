<?php

namespace PicoFeed\Syndication;

use DateTime;
use DOMElement;

/**
 * Class ItemBuilder
 *
 * @package PicoFeed\Syndication
 * @author  Frederic Guillot
 */
abstract class ItemBuilder
{
    /**
     * @var string
     */
    protected $itemTitle;

    /**
     * @var string
     */
    protected $itemId;

    /**
     * @var string
     */
    protected $itemSummary;

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
    protected $itemPublishedDate;

    /**
     * @var DateTime
     */
    protected $itemUpdatedDate;

    /**
     * @var string
     */
    protected $itemContent;

    /**
     * @var string
     */
    protected $itemUrl;

    /**
     * @var FeedBuilder
     */
    protected $feedBuilder;

    /**
     * Constructor
     *
     * @param FeedBuilder $feedBuilder
     */
    public function __construct(FeedBuilder $feedBuilder)
    {
        $this->feedBuilder = $feedBuilder;
    }

    /**
     * Get new object instance
     *
     * @access public
     * @param  FeedBuilder $feedBuilder
     * @return static
     */
    public static function create(FeedBuilder $feedBuilder)
    {
        return new static($feedBuilder);
    }

    /**
     * Add item title
     *
     * @access public
     * @param  string $title
     * @return $this
     */
    public function withTitle($title)
    {
        $this->itemTitle = $title;
        return $this;
    }

    /**
     * Add item id
     *
     * @access public
     * @param  string $id
     * @return $this
     */
    public function withId($id)
    {
        $this->itemId = $id;
        return $this;
    }

    /**
     * Add item url
     *
     * @access public
     * @param  string $url
     * @return $this
     */
    public function withUrl($url)
    {
        $this->itemUrl = $url;
        return $this;
    }

    /**
     * Add item summary
     *
     * @access public
     * @param  string $summary
     * @return $this
     */
    public function withSummary($summary)
    {
        $this->itemSummary = $summary;
        return $this;
    }

    /**
     * Add item content
     *
     * @access public
     * @param  string $content
     * @return $this
     */
    public function withContent($content)
    {
        $this->itemContent = $content;
        return $this;
    }

    /**
     * Add item updated date
     *
     * @access public
     * @param  DateTime $date
     * @return $this
     */
    public function withUpdatedDate(DateTime $date)
    {
        $this->itemUpdatedDate = $date;
        return $this;
    }

    /**
     * Add item published date
     *
     * @access public
     * @param  DateTime $date
     * @return $this
     */
    public function withPublishedDate(DateTime $date)
    {
        $this->itemPublishedDate = $date;
        return $this;
    }

    /**
     * Add item author
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
     * Build item
     *
     * @abstract
     * @access public
     * @return DOMElement
     */
    abstract public function build();
}
