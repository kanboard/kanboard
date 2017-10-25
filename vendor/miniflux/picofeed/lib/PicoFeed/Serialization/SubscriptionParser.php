<?php

namespace PicoFeed\Serialization;

use SimpleXMLElement;

/**
 * Class SubscriptionParser
 *
 * @package PicoFeed\Serialization
 * @author  Frederic Guillot
 */
class SubscriptionParser
{
    /**
     * @var Subscription
     */
    protected $subscription;

    /**
     * @var SimpleXMLElement
     */
    private $outlineElement;

    /**
     * @var SimpleXMLElement
     */
    private $parentElement;

    /**
     * Constructor
     *
     * @access public
     * @param  SimpleXMLElement  $parentElement
     * @param  SimpleXMLElement  $outlineElement
     */
    public function __construct(SimpleXMLElement $parentElement, SimpleXMLElement $outlineElement)
    {
        $this->parentElement = $parentElement;
        $this->outlineElement = $outlineElement;
        $this->subscription = new Subscription();
    }

    /**
     * Get object instance
     *
     * @static
     * @access public
     * @param  SimpleXMLElement $parentElement
     * @param  SimpleXMLElement $outlineElement
     * @return SubscriptionParser
     */
    public static function create(SimpleXMLElement $parentElement, SimpleXMLElement $outlineElement)
    {
        return new static($parentElement, $outlineElement);
    }

    /**
     * Parse subscription entry
     *
     * @access public
     * @return Subscription
     */
    public function parse()
    {
        $this->subscription->setCategory($this->findCategory());
        $this->subscription->setTitle($this->findTitle());
        $this->subscription->setFeedUrl($this->findFeedUrl());
        $this->subscription->setSiteUrl($this->findSiteUrl());
        $this->subscription->setType($this->findType());
        $this->subscription->setDescription($this->findDescription());

        return $this->subscription;
    }

    /**
     * Find category.
     *
     * @access protected
     * @return string
     */
    protected function findCategory()
    {
        return isset($this->parentElement['text']) ? (string) $this->parentElement['text'] : '';
    }

    /**
     * Find title.
     *
     * @access protected
     * @return string
     */
    protected function findTitle()
    {
        return isset($this->outlineElement['title']) ? (string) $this->outlineElement['title'] : (string) $this->outlineElement['text'];
    }

    /**
     * Find feed url.
     *
     * @access protected
     * @return string
     */
    protected function findFeedUrl()
    {
        return (string) $this->outlineElement['xmlUrl'];
    }

    /**
     * Find site url.
     *
     * @access protected
     * @return string
     */
    protected function findSiteUrl()
    {
        return isset($this->outlineElement['htmlUrl']) ? (string) $this->outlineElement['htmlUrl'] : $this->findFeedUrl();
    }

    /**
     * Find type.
     *
     * @access protected
     * @return string
     */
    protected function findType()
    {
        return isset($this->outlineElement['version']) ? (string) $this->outlineElement['version'] :
            isset($this->outlineElement['type']) ? (string) $this->outlineElement['type'] : 'rss';
    }

    /**
     * Find description.
     *
     * @access protected
     * @return string
     */
    protected function findDescription()
    {
        return isset($this->outlineElement['description']) ? (string) $this->outlineElement['description'] : $this->findTitle();
    }
}
