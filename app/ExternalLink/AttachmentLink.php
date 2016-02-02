<?php

namespace Kanboard\ExternalLink;

use Kanboard\Core\ExternalLink\ExternalLinkInterface;

/**
 * Attachment Link
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
class AttachmentLink extends BaseLink implements ExternalLinkInterface
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
        return basename($path);
    }
}
