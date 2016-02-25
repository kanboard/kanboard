<?php

namespace Kanboard\Core\ExternalLink;

/**
 * External Link Interface
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
interface ExternalLinkInterface
{
    /**
     * Get link title
     *
     * @access public
     * @return string
     */
    public function getTitle();

    /**
     * Get link URL
     *
     * @access public
     * @return string
     */
    public function getUrl();

    /**
     * Set link URL
     *
     * @access public
     * @param  string $url
     */
    public function setUrl($url);
}
