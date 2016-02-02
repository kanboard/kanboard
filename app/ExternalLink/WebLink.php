<?php

namespace Kanboard\ExternalLink;

use Kanboard\Core\ExternalLink\ExternalLinkInterface;

/**
 * Web Link
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
class WebLink extends BaseLink implements ExternalLinkInterface
{
    /**
     * Get link title
     *
     * @access public
     * @return string
     */
    public function getTitle()
    {
        $html = $this->httpClient->get($this->url);

        if (preg_match('/<title>(.*)<\/title>/siU', $html, $matches)) {
            return trim($matches[1]);
        }

        $components = parse_url($this->url);

        if (! empty($components['host']) && ! empty($components['path'])) {
            return $components['host'].$components['path'];
        }

        return t('Title not found');
    }
}
