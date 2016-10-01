<?php

namespace Kanboard\ExternalLink;

use Kanboard\Core\Base;

/**
 * Base Link
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
abstract class BaseLink extends Base
{
    /**
     * URL
     *
     * @access protected
     * @var string
     */
    protected $url = '';

    /**
     * Get link URL
     *
     * @access public
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set link URL
     *
     * @access public
     * @param  string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}
