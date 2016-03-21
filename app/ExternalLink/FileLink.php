<?php

namespace Kanboard\ExternalLink;

use Kanboard\Core\ExternalLink\ExternalLinkInterface;

/**
 * File Link
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
class FileLink extends BaseLink implements ExternalLinkInterface
{
    /**
     * Get link title
     *
     * @access public
     * @return string
     */
    public function getTitle()
    {
        $path = parse_url($this->url, PHP_URL_PATH);
        return basename(str_replace('\\', '/', $path));
    }
}
