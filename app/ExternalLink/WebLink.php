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
        $httpClient = EXTERNAL_LINK_ALLOW_PRIVATE_NETWORKS ? $this->httpClient : $this->httpClient->withPrivateNetworksBlocked();

        // Do not follow redirects to prevent SSRF bypasses through redirect chains.
        $html = $httpClient->get($this->url, [], false, false);

        if (preg_match('/<title>(.*)<\/title>/siU', $html, $matches)) {
            return trim($matches[1]);
        }

        $components = parse_url($this->url);

        if (! empty($components['host']) && ! empty($components['path'])) {
            return $components['host'].$components['path'];
        }

        return $this->url;
    }
}
