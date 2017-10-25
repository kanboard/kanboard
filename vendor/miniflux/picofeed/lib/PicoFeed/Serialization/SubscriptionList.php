<?php

namespace PicoFeed\Serialization;

/**
 * Class SubscriptionList
 *
 * @package PicoFeed\Serialization
 * @author  Frederic Guillot
 */
class SubscriptionList
{
    /**
     * OPML entries
     *
     * @var Subscription[]
     */
    public $subscriptions = array();

    /**
     * Title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Create object instance
     *
     * @static
     * @access public
     * @return SubscriptionList
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set title
     * 
     * @access public
     * @param string $title
     * @return SubscriptionList
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     * 
     * @access public
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add subscription
     *
     * @access public
     * @param  Subscription $subscription
     * @return SubscriptionList
     */
    public function addSubscription(Subscription $subscription)
    {
        $this->subscriptions[] = $subscription;
        return $this;
    }
}
