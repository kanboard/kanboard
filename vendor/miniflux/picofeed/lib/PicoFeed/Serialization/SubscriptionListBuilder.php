<?php

namespace PicoFeed\Serialization;

use DOMDocument;
use DOMElement;

/**
 * Class SubscriptionListBuilder
 *
 * @package PicoFeed\Serialization
 * @author  Frederic Guillot
 */
class SubscriptionListBuilder
{
    /**
     * @var SubscriptionList
     */
    protected $subscriptionList;

    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * Constructor.
     *
     * @access public
     * @param  SubscriptionList $subscriptionList
     */
    public function __construct(SubscriptionList $subscriptionList)
    {
        $this->subscriptionList = $subscriptionList;
    }

    /**
     * Get object instance
     *
     * @static
     * @access public
     * @param  SubscriptionList $subscriptionList
     * @return SubscriptionListBuilder
     */
    public static function create(SubscriptionList $subscriptionList)
    {
        return new static($subscriptionList);
    }

    /**
     * Build OPML feed
     *
     * @access public
     * @param  string $filename
     * @return string
     */
    public function build($filename = '')
    {
        $this->document = new DomDocument('1.0', 'UTF-8');
        $this->document->formatOutput = true;

        $opmlElement = $this->document->createElement('opml');
        $opmlElement->setAttribute('version', '1.0');

        $headElement = $this->document->createElement('head');

        if ($this->subscriptionList->getTitle() !== '') {
            $titleElement = $this->document->createElement('title');
            $titleElement->appendChild($this->document->createTextNode($this->subscriptionList->getTitle()));
            $headElement->appendChild($titleElement);
        }

        $opmlElement->appendChild($headElement);
        $opmlElement->appendChild($this->buildBody());
        $this->document->appendChild($opmlElement);

        if ($filename !== '') {
            $this->document->save($filename);
            return '';
        }

        return $this->document->saveXML();
    }

    /**
     * Return true if the list has categories
     *
     * @access public
     * @return bool
     */
    public function hasCategories()
    {
        foreach ($this->subscriptionList->subscriptions as $subscription) {
            if ($subscription->getCategory() !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Build OPML body
     *
     * @access protected
     * @return DOMElement
     */
    protected function buildBody()
    {
        $bodyElement = $this->document->createElement('body');

        if ($this->hasCategories()) {
            $this->buildCategories($bodyElement);
            return $bodyElement;
        }

        foreach ($this->subscriptionList->subscriptions as $subscription) {
            $bodyElement->appendChild($this->buildSubscription($subscription));
        }

        return $bodyElement;
    }

    /**
     * Build categories section
     *
     * @access protected
     * @param  DOMElement $bodyElement
     */
    protected function buildCategories(DOMElement $bodyElement)
    {
        $categories = $this->groupByCategories();

        foreach ($categories as $category => $subscriptions) {
            $bodyElement->appendChild($this->buildCategory($category, $subscriptions));
        }
    }

    /**
     * Build category tag
     *
     * @access protected
     * @param  string $category
     * @param  array  $subscriptions
     * @return DOMElement
     */
    protected function buildCategory($category, array $subscriptions)
    {
        $outlineElement = $this->document->createElement('outline');
        $outlineElement->setAttribute('text', $category);

        foreach ($subscriptions as $subscription) {
            $outlineElement->appendChild($this->buildSubscription($subscription));
        }

        return $outlineElement;
    }

    /**
     * Build subscription entry
     *
     * @access public
     * @param  Subscription $subscription
     * @return DOMElement
     */
    protected function buildSubscription(Subscription $subscription)
    {
        $outlineElement = $this->document->createElement('outline');
        $outlineElement->setAttribute('type', $subscription->getType() ?: 'rss');
        $outlineElement->setAttribute('text', $subscription->getTitle() ?: $subscription->getFeedUrl());
        $outlineElement->setAttribute('xmlUrl', $subscription->getFeedUrl());

        if ($subscription->getTitle() !== '') {
            $outlineElement->setAttribute('title', $subscription->getTitle());
        }

        if ($subscription->getDescription() !== '') {
            $outlineElement->setAttribute('description', $subscription->getDescription());
        }

        if ($subscription->getSiteUrl() !== '') {
            $outlineElement->setAttribute('htmlUrl', $subscription->getSiteUrl());
        }

        return $outlineElement;
    }

    /**
     * Group subscriptions by category
     *
     * @access private
     * @return array
     */
    private function groupByCategories()
    {
        $categories = array();

        foreach ($this->subscriptionList->subscriptions as $subscription) {
            $categories[$subscription->getCategory()][] = $subscription;
        }

        return $categories;
    }
}
