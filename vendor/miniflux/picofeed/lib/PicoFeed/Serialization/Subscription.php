<?php

namespace PicoFeed\Serialization;

/**
 * Class Subscription
 *
 * @package PicoFeed\Serialization
 * @author  Frederic Guillot
 */
class Subscription
{
    protected $title = '';
    protected $feedUrl = '';
    protected $siteUrl = '';
    protected $category = '';
    protected $description = '';
    protected $type = '';

    /**
     * Create object instance
     *
     * @static
     * @access public
     * @return Subscription
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set title
     *
     * @access public
     * @param  string $title
     * @return Subscription
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
     * Set feed URL
     *
     * @access public
     * @param string $feedUrl
     * @return Subscription
     */
    public function setFeedUrl($feedUrl)
    {
        $this->feedUrl = $feedUrl;
        return $this;
    }

    /**
     * Get feed URL
     *
     * @access public
     * @return string
     */
    public function getFeedUrl()
    {
        return $this->feedUrl;
    }

    /**
     * Set site URL
     *
     * @access public
     * @param string $siteUrl
     * @return Subscription
     */
    public function setSiteUrl($siteUrl)
    {
        $this->siteUrl = $siteUrl;
        return $this;
    }

    /**
     * Get site URL
     *
     * @access public
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /**
     * Set category
     *
     * @access public
     * @param string $category
     * @return Subscription
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @access public
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set description
     *
     * @access public
     * @param string $description
     * @return Subscription
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @access public
     * @param string $type
     * @return Subscription
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @access public
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
